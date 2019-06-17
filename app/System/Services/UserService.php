<?php

namespace App\System\Services;

use App\System\Data\Timeslot;
use App\System\Data\User;
use App\System\Utils\ConfigUtils;
use App\User as UserModel;
use App\UserTimeLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\LoginNotification;

class UserService
{
    const USERS_SORT_TOTAL = 'total';
    
    private $usersSummaryCache = [];
    
    private $cache;
    
    public function __construct()
    {
        $this->cache = new CachingService();
    }
    
    /**
     * Retrieve all users.
     *
     * @param $skip
     * @param $take
     * @return array
     */
    public function getUsers($skip, $take)
    {
        $users = [];
        $user = new User(auth()->user());
        $allowedIds = $this->getAllowedIds($user);
        
        $query = UserModel::where('status', User::STATUS_ACTIVE);
        if (!$user->isSuperAdmin()) {
            $query->whereIn('id', $allowedIds);
        }
        $query->orderBy('name', 'asc');
        $query->skip($skip);
        $query->take($take);
        $results = $query->get();
        
        foreach ($results as $result) {
            $users[] = new User($result);
        }
        return $users;
    }
    
    
    /**
     * Count active users.
     * 
     * @return number
     */
    public function countUsers()
    {
        $user = new User(auth()->user());
        $allowedIds = $this->getAllowedIds($user);
        
        $query = UserModel::where('status', User::STATUS_ACTIVE);
        if (!$user->isSuperAdmin()) {
            $query->whereIn('id', $allowedIds);
        }
        
        return (int) $query->count();
    }
    
    public function recordUserLogin(UserModel $user, $description = '')
    {
        $this->recordUserLog($user, User::MODE_LOGIN, $description);
        $this->sendLoginEmail($user);
    }
    
    public function recordUserLogout(UserModel $user, $description = '')
    {
        $this->recordUserLog($user, User::MODE_LOGOUT, $description);
    }
    
    private function recordUserLog(UserModel $user, $mode, $description = '')
    {
        UserTimeLog::create([
            'user_id' => $user->id,
            'log_time' => Carbon::now(env('APP_TIMEZONE')),
            'mode' => $mode,
            'ip_address' => request()->ip(),
            'agent' => request()->header('User-Agent'),
            'description' => $description,
        ]);
    }
    
    private function sendLoginEmail(UserModel $user, $trackAdminOnly = true)
    {
        $emails = ConfigUtils::get('NOTIFICATION_EMAILS');
        if ($emails) {
            foreach ($emails as $email) {
                if ($trackAdminOnly && !$user->is_admin) {
                    continue;
                }
                Mail::to($email)->send(new LoginNotification($user));
            }
        }
    }

    public function updatePassword($oldPassword, $newPassword, $newPasswordConfirm) {
        if (empty($oldPassword) || empty($newPassword) || empty($newPasswordConfirm)) {
            throw new \Exception('Please do not leave any input text blank.');
        }

        if (Hash::check($oldPassword, auth()->user()->password)) {
            $newPassword = bcrypt($newPassword);
            if (Hash::check($newPasswordConfirm, $newPassword)) {
                $user = auth()->user();
                $user->password = $newPassword;
                $user->is_password_updated = true;
                $user->save();

            } else {
                throw new \Exception('Password confirmation failed.');
            }
        } else {
            throw new \Exception('Old password incorrect.');
        }
    }

    public function getUserTimeLog(UserModel $user)
    {
        try {
            $userLogs = UserTimeLog::where('user_id',$user->id)->orderBy('id','desc')->get()->toArray();
            return $userLogs;
        } catch (\Exception $e) {
            return array();
        }
        
    }
    
    public function getUsersSummary($skip, $take, $draw_date, $sortByKey = self::OUTLETS_SORT_TOTAL, $origin = null, $ushersOnly = false)
    {
        $users = $this->getUsers($skip, $take);
        $transactionService = new TransactionService(null, $origin);
        $usersSummary = [];
        $rawData = $transactionService->getDailySalesPerUser($draw_date, $ushersOnly);
        
        foreach ($users as $user) {
            if (!$user->isUsher()) {
                continue;
            }
            
            $summary = [];
            $total = 0;
            $summary['user'] = $user;
            foreach (Timeslot::drawTimeslots() as $key => $timeslot) {
                $summary['sales'][$key] = 0;
                if (array_key_exists($user->id(), $rawData) && array_key_exists($key, $rawData[$user->id()])) {
                    $summary['sales'][$key] = $rawData[$user->id()][$key];
                }
                $total = bcadd($total, $summary['sales'][$key]);
            }
            
            $summary['sales'][self::USERS_SORT_TOTAL] = $total;
            $usersSummary[] = $summary;
        }
        
        $this->usersSummaryCache = $usersSummary;
        $this->quicksortUsersSummary(0, (count($this->usersSummaryCache) - 1), $sortByKey);
        $usersSummary = $this->usersSummaryCache;
        $this->usersSummaryCache = [];
        
        return $usersSummary;
    }
    
    public static function getSuperAdmins($useUserData = false)
    {
        $users = UserModel::where('is_superadmin', true)->get();
        
        $tmp = [];
        foreach ($users as $user) {
            $tmp[] = ($useUserData) ? new User($user) : $user;
        }
        $users = $tmp;
        
        return $users;
    }
    
    private function quicksortUsersSummary($lower, $upper, $sortByKey) {
        if ($lower >= $upper) {
            return;
        }
        $m = $lower;
        
        for ($i = $lower + 1; $i <= $upper; $i++) {
            if ((double) $this->usersSummaryCache[$i]['sales'][$sortByKey] > (double) $this->usersSummaryCache[$lower]['sales'][$sortByKey]) {
                $tmp = $this->usersSummaryCache[++$m];
                $this->usersSummaryCache[$m] = $this->usersSummaryCache[$i];
                $this->usersSummaryCache[$i] = $tmp;
            }
        }
        
        $tmp = $this->usersSummaryCache[$m];
        $this->usersSummaryCache[$m] = $this->usersSummaryCache[$lower];
        $this->usersSummaryCache[$lower] = $tmp;
        
        $this->quicksortUsersSummary($lower, $m - 1, $sortByKey);
        $this->quicksortUsersSummary($m + 1, $upper, $sortByKey);
    }
    
    private function getAllowedIds($user)
    {
        if ($user instanceof User || $user instanceof UserModel) {
            $user = ($user instanceof User) ? $user : new User($user);
        } else {
            throw new \Exception('Only User and UserModel object is accepted.');
        }
        
        $allowedId = [];
        foreach ($this->cache->getUserAllSubordinates($user) as $sub) {
            $allowedId[] = $sub->id;
        }
        
        return $allowedId;
    }

    public function setAllowBetting($allow, $user) {
        if (!is_bool($allow)) {
            throw new \Exception('Invalid allow value. Must be a boolean.');
        }
        if (!($user instanceof UserModel)) {
            throw new \Exception('User is not an instance of the UserModel');
        }
        $user->is_betting_enabled = $allow ? 1 : 0;
        $user->save();
    }

    public function isAllowedBetting($user) {
        if (!($user instanceof UserModel)) {
            throw new \Exception('User is not an instance of the UserModel');
        }
        return $user->is_betting_enabled == User::BETTING_ACTIVE;
    }
}
