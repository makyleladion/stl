<?php
namespace App\System\Services;

use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use App\System\Data\Timeslot;
use App\System\Games\GamesFactory;
use App\System\Data\User as UserData;
use App\User;

class CachingService
{
    private $transactionService;
    
    const CACHE_SALES = 'cache_admin_sales_';
    const CACHE_TICKETS = 'cache_admin_tickets_';
    const CACHE_WINNERS = 'cache_admin_winners_';
    const CACHE_TOTAL_AMOUNT = 'cache_admin_total_amount_';
    const CACHE_USER_RELATIONSHIP_LEAVES = 'cache_user_relationship_leaves_';
    const CACHE_USER_RELATIONSHIP_SUBORDINATES = 'cache_user_relationship_subordinates_';
    const CACHE_USER_RELATIONSHIP_SUPERIORS = 'cache_user_relationship_superiors_';
    const CACHE_USER_SUPERADMINS = 'cache_user_superadmins_';
    
    private $rawDataKeyPrefixes = [];
    
    public function __construct($origin = null)
    {
        $this->transactionService = new TransactionService(null, $origin);
        $this->rawDataKeyPrefixes = [
            self::CACHE_SALES,
            self::CACHE_TICKETS,
            self::CACHE_WINNERS,
        ];
    }
    
    public function getRawAmountDataByDate($drawDate)
    {
        /* if (!$this->rawAmountIsCached($drawDate)) {
         $this->clearRawAmountCache($drawDate);
         $rawSalesData = $this->transactionService->newGetTotalAmountByDate($drawDate);
         foreach (Timeslot::drawTimeslots() as $key => $timeslot) {
         $amount = (isset($rawSalesData[$key]['amount'])) ? $rawSalesData[$key]['amount'] : 0;
         $numberOfTickets = (isset($rawSalesData[$key]['tickets_count'])) ? $rawSalesData[$key]['tickets_count'] : 0;
         $numberOfWinnings = (isset($rawSalesData[$key]['winners_count'])) ? $rawSalesData[$key]['winners_count'] : 0;
         
         $this->setRawAmount(self::CACHE_SALES, $amount, $key, $drawDate);
         $this->setRawAmount(self::CACHE_TICKETS, $numberOfTickets, $key, $drawDate);
         $this->setRawAmount(self::CACHE_WINNERS, $numberOfWinnings, $key, $drawDate);
         }
         }
         
         $finalStructure = [];
         foreach (Timeslot::getTimeslotKeys() as $key) {
         $finalStructure[$key] = [
         'amount' => $this->getRawAmount(self::CACHE_SALES, $key, $drawDate),
         'tickets_count' => $this->getRawAmount(self::CACHE_TICKETS, $key, $drawDate),
         'winners_count' => $this->getRawAmount(self::CACHE_WINNERS, $key, $drawDate),
         ];
         } */
        
        return $this->transactionService->newGetTotalAmountByDate($drawDate);
    }
    
    public function setRawAmount($key, $value, $schedKey, $drawDate)
    {
        if (!in_array($key, $this->rawDataKeyPrefixes)) {
            return;
        }
        
        $cacheKey = sha1($key.$schedKey.$drawDate);
        if (Cache::has($cacheKey)) {
            Cache::increment($cacheKey, $value);
        } else {
            Cache::put($cacheKey, $value, Carbon::tomorrow(env('APP_TIMEZONE')));
        }
    }
    
    public function getRawAmount($key, $schedKey, $drawDate)
    {
        if (!in_array($key, $this->rawDataKeyPrefixes)) {
            return;
        }
        
        $cacheKey = sha1($key.$schedKey.$drawDate);
        return Cache::get($cacheKey);
    }
    
    public function rawAmountIsCached($drawDate)
    {
        $isCached = true;
        $timeslots = Timeslot::getTimeslotKeys();
        foreach ($this->rawDataKeyPrefixes as $key) {
            foreach ($timeslots as $schedKey) {
                $cacheKey = sha1($key.$schedKey.$drawDate);
                if (!Cache::has($cacheKey)) {
                    $isCached = false;
                    break;
                }
            }
        }
        
        return $isCached;
    }
    
    public function clearRawAmountCache($drawDate)
    {
        foreach ($this->rawDataKeyPrefixes as $key) {
            foreach (Timeslot::getTimeslotKeys() as $schedKey) {
                $cacheKey = sha1($key.$schedKey.$drawDate);
                if (Cache::has($cacheKey)) {
                    Cache::forget($cacheKey);
                }
            }
        }
    }
    
    public function getRawAmountDataByDateAndGame($drawDate)
    {
        /* if (!$this->rawAmountAggregatedIsCached($drawDate)) {
         $this->clearRawAmountAggregatedCache($drawDate);
         $rawSalesDataGameAggregated = $this->transactionService->getGameAggregatedTotalAmountByDate($drawDate);
         foreach (Timeslot::drawTimeslots() as $key => $timeslot) {
         foreach (GamesFactory::getGameNames() as $game) {
         $amount = 0;
         if (isset($rawSalesDataGameAggregated[$key])) {
         $amount = array_key_exists($game,$rawSalesDataGameAggregated[$key]) ? $rawSalesDataGameAggregated[$key][$game]['amount'] : 0;
         }
         $this->setRawAmountAggregated(self::CACHE_SALES, intval($amount), $game, $key, $drawDate);
         }
         }
         }
         
         $finalStructure = [];
         foreach (Timeslot::drawTimeslots() as $key => $timeslot) {
         foreach (GamesFactory::getGameNames() as $game) {
         $finalStructure[$key][$game]['amount'] = $this->getRawAmountAggregated(self::CACHE_SALES, $game, $key, $drawDate);
         }
         } */
        
        return $this->transactionService->getGameAggregatedTotalAmountByDate($drawDate);
    }
    
    public function rawAmountAggregatedIsCached($drawDate)
    {
        $isCached = true;
        $timeslots = Timeslot::getTimeslotKeys();
        $key = self::CACHE_SALES;
        
        foreach ($timeslots as $schedKey) {
            foreach (GamesFactory::getGameNames() as $game) {
                $cacheKey = sha1($key.$schedKey.$game.$drawDate);
                if (!Cache::has($cacheKey)) {
                    $isCached = false;
                    break;
                }
            }
        }
        
        return $isCached;
    }
    
    public function clearRawAmountAggregatedCache($drawDate)
    {
        $key = self::CACHE_SALES;
        foreach (Timeslot::getTimeslotKeys() as $schedKey) {
            foreach (GamesFactory::getGameNames() as $game) {
                $cacheKey = sha1($key.$schedKey.$game.$drawDate);
                if (Cache::has($cacheKey)) {
                    Cache::forget($cacheKey);
                }
            }
        }
    }
    
    public function setRawAmountAggregated($key, $value, $game, $schedKey, $drawDate)
    {
        if (!in_array($key, $this->rawDataKeyPrefixes)) {
            return;
        }
        
        if (!in_array($game, GamesFactory::getGameNames())) {
            return;
        }
        
        $cacheKey = sha1($key.$schedKey.$game.$drawDate);
        if (Cache::has($cacheKey)) {
            Cache::increment($cacheKey, $value);
        } else {
            Cache::put($cacheKey, $value, Carbon::tomorrow(env('APP_TIMEZONE')));
        }
    }
    
    public function getRawAmountAggregated($key, $game, $schedKey, $drawDate)
    {
        if (!in_array($key, $this->rawDataKeyPrefixes)) {
            return;
        }
        
        if (!in_array($game, GamesFactory::getGameNames())) {
            return;
        }
        
        $cacheKey = sha1($key.$schedKey.$game.$drawDate);
        return Cache::get($cacheKey);
    }
    
    public function getTotalAmountByDate($carbonObject = null)
    {
        /* if (is_null($carbonObject)) {
            $carbonObject = Carbon::now(env('APP_TIMEZONE'));
        }
        
        $cacheKey = sha1(self::CACHE_TOTAL_AMOUNT.$carbonObject->format('Y-m-d'));
        if (!Cache::has($cacheKey)) {
            $totalAmount = $this->transactionService->getTotalAmountByDate($carbonObject);
            $this->addToTotalAmountByDate($totalAmount, $carbonObject);
        }
        
        return Cache::get($cacheKey); */
        
        return $this->transactionService->getTotalAmountByDate($carbonObject);
    }
    
    public function addToTotalAmountByDate($amount, $carbonObject = null)
    {
        if (is_null($carbonObject)) {
            $carbonObject = Carbon::now(env('APP_TIMEZONE'));
        }
        
        $cacheKey = sha1(self::CACHE_TOTAL_AMOUNT.$carbonObject->format('Y-m-d'));
        if (!Cache::has($cacheKey)) {
            Cache::put($cacheKey, $amount, Carbon::tomorrow(env('APP_TIMEZONE')));
        } else {
            Cache::increment($cacheKey, $amount);
        }
    }
    
    public function getUserLeaves($user)
    {
        if ($user instanceof UserData || $user instanceof User) {
            $user = ($user instanceof UserData) ? $user : new UserData($user);
        } else {
            throw new \Exception('Only UserData and User object is accepted.');
        }
        
        $cacheKey = sha1(self::CACHE_USER_RELATIONSHIP_LEAVES.$user->id());
        if (!Cache::has($cacheKey)) {
            Cache::put(
                $cacheKey,
                $user->getHierarchyLeaves(),
                Carbon::tomorrow(env('APP_TIMEZONE'))
            );
        }
        
        return Cache::get($cacheKey);
    }
    
    public function getUserAllSubordinates($user)
    {
        if ($user instanceof UserData || $user instanceof User) {
            $user = ($user instanceof UserData) ? $user : new UserData($user);
        } else {
            throw new \Exception('Only UserData and User object is accepted.');
        }
        
        $cacheKey = sha1(self::CACHE_USER_RELATIONSHIP_SUBORDINATES.$user->id());
        if (!Cache::has($cacheKey)) {
            Cache::put(
                $cacheKey,
                $user->traverseSubordinates(),
                Carbon::tomorrow(env('APP_TIMEZONE'))
            );
        }
        
        return Cache::get($cacheKey);
    }
    
    public function getUserAllSuperiors($user)
    {
        if ($user instanceof UserData || $user instanceof User) {
            $user = ($user instanceof UserData) ? $user : new UserData($user);
        } else {
            throw new \Exception('Only UserData and User object is accepted.');
        }
        
        $cacheKey = sha1(self::CACHE_USER_RELATIONSHIP_SUPERIORS.$user->id());
        if (!Cache::has($cacheKey)) {
            Cache::put(
                $cacheKey,
                $user->getSuperiors(),
                Carbon::tomorrow(env('APP_TIMEZONE'))
            );
        }
        
        return Cache::get($cacheKey);
    }
    
    public function getSuperAdmins($useUserData = false)
    {
        $strVal = ($useUserData) ? 'true' : 'false';
        $cacheKey = sha1(self::CACHE_USER_SUPERADMINS.$strVal.auth()->user()->id);
        if (!Cache::has($cacheKey)) {
            Cache::put(
                $cacheKey,
                UserService::getSuperAdmins($useUserData),
                Carbon::tomorrow(env('APP_TIMEZONE'))
            );
        }
        
        return Cache::get($cacheKey);
    }
    
    public function clearUserCache($user)
    {
        if ($user instanceof UserData || $user instanceof User) {
            $user = ($user instanceof UserData) ? $user : new UserData($user);
        } else {
            throw new \Exception('Only UserData and User object is accepted.');
        }
        
        $cacheKey1 = sha1(self::CACHE_USER_RELATIONSHIP_LEAVES.$user->id());
        $cacheKey2 = sha1(self::CACHE_USER_RELATIONSHIP_SUBORDINATES.$user->id());
        $cacheKey3 = sha1(self::CACHE_USER_RELATIONSHIP_SUPERIORS.$user->id());
        
        $cacheKey4 = sha1(self::CACHE_USER_SUPERADMINS.'false'.$user->id());
        $cacheKey5 = sha1(self::CACHE_USER_SUPERADMINS.'true'.$user->id());
        
        Cache::forget($cacheKey1);
        Cache::forget($cacheKey2);
        Cache::forget($cacheKey3);
        
        Cache::forget($cacheKey4);
        Cache::forget($cacheKey5);
    }
}
