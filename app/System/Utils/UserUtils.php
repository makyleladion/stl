<?php

namespace App\System\Utils;

use App\System\Services\CachingService;

class UserUtils
{
    public static function generallyAllowedUsers($user)
    {
        $service = new CachingService();
        $users = array_merge(
            $service->getUserAllSuperiors($user),
            [$user],
            $service->getSuperAdmins(),
            $service->getUserAllSubordinates($user)
        );

        return $users;
    }
    
    public static function currentAndSubordinatesIds($user)
    {
        $service = new CachingService();
        $allowedIds = [];
        
        foreach (array_merge([$user], $service->getUserAllSubordinates($user)) as $sub) {
            $allowedIds[] = $sub->id;
        }
        
        return $allowedIds;
    }
    
    public static function leavesIds($user, $fromMobile = false)
    {
        $service = new CachingService();
        $allowedIds = [];
        
        if (!$fromMobile) {
            foreach ($service->getUserLeaves($user) as $sub) {
                $allowedIds[] = $sub->id;
            }
        } else {
            $allowedIds[] = $user->id;
        }
        
        return $allowedIds;
    }
}
