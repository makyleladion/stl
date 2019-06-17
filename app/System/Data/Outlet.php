<?php

namespace App\System\Data;

use App\Outlet as OutletModel;
use App\System\Utils\GeneratorUtils;
use App\System\Services\CachingService;
use App\System\Utils\ConfigUtils;

class Outlet
{

    const STATUS_ACTIVE     = 'active';
    const STATUS_SUSPENDED  = 'suspended';
    const STATUS_CLOSED     = 'closed';
    const STATUS_DISABLED   = 'disabled';

    private $outlet;
    private $owner;
    private $tellers;
    private $cache;

    /**
     * Outlet constructor.
     *
     * @param OutletModel $outlet
     */
    public function __construct(OutletModel $outlet)
    {
        $this->outlet = $outlet;
        $this->cache = new CachingService();
    }

    /**
     * Outlet id.
     *
     * Get the id of the outlet.
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
            return str_pad($this->outlet->id, 8, '0', STR_PAD_LEFT);
        }
        return $this->outlet->id;
    }

    /**
     * Outlet name.
     *
     * @return mixed
     */
    public function name()
    {
        return $this->outlet->name;
    }

    /**
     * Outlet address.
     *
     * @return mixed
     */
    public function address()
    {
        return $this->outlet->address;
    }

    /**
     * Outlet owner.
     *
     * @return mixed
     */
    public function owner()
    {
        if (is_null($this->owner)) {
            $this->owner = $this->outlet->user()->first()->name;
        }
        return $this->owner;
    }
    
    /**
     * Return assigned tellers.
     * 
     * @param string $toString
     * @return string|\App\System\Data\User[]
     */
    public function assignedTellers($toString = false)
    {
        if (is_null($this->tellers)) {
            
            // Retrieve default_outlet
            $defaultOutlets = null;
            if (isset($this->outlet->tellers)) {
                $defaultOutlets = $this->outlet->tellers;
            } else {
                $defaultOutlets = $this->outlet->tellers()->get();
            }
            
            // retrieve their users
            $users = [];
            $hideUshers = ConfigUtils::get('HIDE_OUTLET_USHERS_ASSIGNMENT');
            if ($defaultOutlets) {
                foreach ($defaultOutlets as $defaultOutlet) {
                    if (isset($defaultOutlet->user)) {
                        $tmpUser = $defaultOutlet->user;
                        if ($tmpUser->status == User::STATUS_ACTIVE) {
                            if ($hideUshers) {
                                if (!$tmpUser->is_usher) {
                                    $users[] = $tmpUser;
                                }
                            } else {
                                $users[] = $tmpUser;
                            }
                        }
                    } else {
                        $tmpUser = $defaultOutlet->user()->first();
                        if ($tmpUser != null) {
                            if ($tmpUser->status == User::STATUS_ACTIVE) {
                                if ($hideUshers) {
                                    if (!$tmpUser->is_usher) {
                                        $users[] = $tmpUser;
                                    }
                                } else {
                                    $users[] = $tmpUser;
                                }
                            }
                        }
                    }
                }
            }
            
            // Wrap with User data.
            $tellers = [];
            foreach ($users as $user) {
                $tellers[] = new User($user);
            }
            
            // Cache tellers
            $this->tellers = $tellers;
        }
        
        $allowedNames = [];
        foreach ($this->cache->getUserLeaves(auth()->user()) as $leaf) {
            $allowedNames[] = $leaf->name;
        }
        
        if ($toString) {
            $strs = [];
            foreach ($this->tellers as $teller) {
                if (auth()->user()->is_superadmin) {
                    $strs[] = $teller->name();
                } else {
                    if (in_array($teller->name(), $allowedNames)) {
                        $strs[] = $teller->name();
                    }
                }
            }
            return GeneratorUtils::generateStringWithDelimiter($strs, ', ');
        }
        
        return $this->tellers;
    }

    /**
     * Outlet status.
     *
     * @return mixed
     */
    public function status()
    {
        return $this->outlet->status;
    }
    
    /**
     * Get Affiliation.
     * 
     * @return boolean
     */
    public function isAffiliated()
    {
        return (bool) $this->outlet->is_affiliated;
    }
    
    public function getTimeIn($date = null)
    {
        $tellers = null;
        if (isset($this->outlet->tellers)) {
            $tellers = $this->outlet->tellers;
        } else {
            $tellers = $this->outlet->tellers()->get();
        }
        
        $allowedId = [];
        foreach ($this->cache->getUserLeaves(auth()->user()) as $leaf) {
            $allowedId[] = $leaf->id;
        }
        
        $timeIns = [];
        foreach ($tellers as $teller) {
            $user = new User($teller->user()->first());
            if (in_array($user->id(), $allowedId) || auth()->user()->is_superadmin) {
                $timeIn = $user->getEarliestLogin($date);
                if (strlen($timeIn) > 0) {
                    $timeIns[] = $timeIn;
                }
            }
        }
        
        return GeneratorUtils::generateStringWithDelimiter($timeIns, ', ');
    }
    
    public function getLatestTimeOut($date = null)
    {
        $tellers = null;
        if (isset($this->outlet->tellers)) {
            $tellers = $this->outlet->tellers;
        } else {
            $tellers = $this->outlet->tellers()->get();
        }
        
        $timeOuts = [];
        foreach ($tellers as $teller) {
            $user = new User($teller->user()->first());
            $timeOut = $user->getLatestLogout($date);
            if (strlen($timeOut) > 0) {
                $timeOuts[] = $timeOut;
            }
        }
        
        return GeneratorUtils::generateStringWithDelimiter($timeOuts, ', ');
    }

    /**
     * Get outlet model instance.
     *
     * @return OutletModel
     */
    public function getOutlet()
    {
        return $this->outlet;
    }
}
