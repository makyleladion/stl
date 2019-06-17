<?php

namespace App\System\Data;

use Illuminate\Support\Facades\DB;
use App\User as UserModel;
use Carbon\Carbon;

class User
{
    const ROLE_ADMIN  = 'admin';
    const ROLE_OWNER  = 'owner';
    const ROLE_TELLER = 'teller';
    
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';

    const BETTING_ACTIVE = 1;
    const BETTING_INACTIVE = 0;
    
    const MODE_LOGIN  = 'login';
    const MODE_LOGOUT = 'logout';

    private $user;

    /**
     * User constructor.
     *
     * @param UserModel $user
     */
    public function __construct(UserModel $user)
    {
        $this->user = $user;
    }

    /**
     * User id.
     *
     * Get the id of the user.
     * Option to pad the id with trailing zeros is available.
     * Padded id is 8 digits long.
     *
     * Example: 00000001
     *
     * @param bool $padded
     * @return mixed|string
     */
    public function id($padded = false)
    {
        if ($padded) {
            return str_pad($this->user->id, 8, '0', STR_PAD_LEFT);
        }
        return $this->user->id;
    }

    /**
     * Name of user.
     *
     * @return mixed
     */
    public function name()
    {
        return $this->user->name;
    }

    /**
     * Email address.
     *
     * @return mixed
     */
    public function email()
    {
        return $this->user->email;
    }

    /**
     * Password.
     *
     * @return mixed
     */
    public function password()
    {
        return $this->user->password;
    }

    /**
     * Role.
     *
     * It is determined via the is_admin field and its association with the outlet(s) as well.
     *
     * @return string
     */
    public function role()
    {
        if ($this->user->is_admin) {
            return self::ROLE_ADMIN;
        } elseif (!$this->user->is_admin && $this->user->outlets()->count() > 0) {
            return self::ROLE_OWNER;
        }
        return self::ROLE_TELLER;
    }
    
    public function getEarliestLogin($date = null)
    {
        $date = (is_null($date)) ? date('Y-m-d') : $date;
        $logTime = $this->user->userTimeLogs()->whereDate('log_time', $date)
                   ->where('mode', self::MODE_LOGIN)
                   ->orderBy('log_time', 'asc')->take(1)->first();
        
        $time = '';
        if ($logTime) {
            $obj = Carbon::parse($logTime->log_time, env('APP_TIMEZONE'));
            $time = $obj->format('h:i A');
        }
        
        return $time;
    }
    
    public function getLatestLogout($date = null)
    {
        $date = (is_null($date)) ? date('Y-m-d') : $date;
        $logTime = $this->user->userTimeLogs()->whereDate('log_time', $date)
                   ->where('mode', self::MODE_LOGOUT)
                   ->orderBy('log_time', 'desc')->take(1)->first();
        
        $time = '';
        if ($logTime) {
            $obj = Carbon::parse($logTime->log_time, env('APP_TIMEZONE'));
            $time = $obj->format('h:i A');
        }
        
        return $time;
    }
    
    public function isReadOnly()
    {
        if (!$this->user->is_admin) {
            return false;
        }
        
        return (bool) $this->user->is_read_only;
    }
    
    public function isUsher()
    {
        if ($this->user->is_admin) {
            return false;
        }
        
        return (bool) $this->user->is_usher;
    }
    
    public function getSuperior()
    {
        $hierarcy = $this->user->subordinateHierarchy()->first();
        $superior = null;
        if ($hierarcy) {
            $superior = $hierarcy->superiorUser()->first();
        }
        
        return $superior;
    }
    
    public function getSubordinates()
    {
        $hierarcy = $this->user->superiorHierarchy()->get();
        $subordinates = [];
        foreach ($hierarcy as $h) {
            $subordinates[] = $h->subordinateUser()->first();
        }
        
        return $subordinates;
    }
    
    public function traverseSubordinates()
    {
        return $this->traverseSubordinatesRecursive($this->getSubordinates());
    }
    
    public function getHierarchyLeaves()
    {
        return $this->getLeavesRecursive($this->getSubordinates());
    }
    
    public function getSuperiors()
    {
        $superiors = [];
        $user = $this;
        while ($user = $user->getSuperior()) {
            $superiors[] = clone $user;
            $user = new User($user);
        }
        
        return $superiors;
    }
    
    public function isSuperAdmin()
    {
        return (bool) $this->user->is_superadmin;
    }

    public function isAdmin()
    {
        return (bool) $this->user->is_admin;
    }
    
    private function getLeavesRecursive(array $subs)
    {
        $subsubs = [];
        foreach ($subs as $sub) {
            $usub = new User($sub);
            $subsubsubs = $usub->getSubordinates();
            if (count($subsubsubs) > 0) {
                $tmp = $this->getLeavesRecursive($subsubsubs);
                $subsubs = array_merge($subsubs, $tmp);
            } else {
                if (!$sub->is_admin) {
                    $subsubs[] = $sub;
                }
            }
        }
        
        return $subsubs;
    }
    
    private function traverseSubordinatesRecursive(array $subs)
    {
        $subsubs = [];
        foreach ($subs as $sub) {
            $usub = new User($sub);
            $subsubs[] = $sub;
            $subsubsubs = $usub->getSubordinates();
            $tmp = $this->traverseSubordinatesRecursive($subsubsubs);
            $subsubs = array_merge($subsubs, $tmp);
        }
        
        return $subsubs;
    }
    
    public function isBettingEnabled() {
        return (bool) $this->user->is_betting_enabled;
    }
}
