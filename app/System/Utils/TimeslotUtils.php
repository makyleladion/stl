<?php

namespace App\System\Utils;

use App\System\Data\Timeslot;
use Carbon\Carbon;
use DateTime;

class TimeslotUtils
{
    /**
     * Check if draw time already passed the current time.
     *
     * @param $date
     * @param $timeslotKey
     * @return bool
     */
    public static function drawDateTimeslotHasPassed($date, $timeslotKey)
    {
        return self::isDrawTimePassed($date, $timeslotKey, self::globalCutOffTime());
    }

    /**
     * Get incremental timeslots until days.
     *
     * @param $days
     * @return array
     * @throws \Exception
     */
    public static function getIncrementalTimeslotsUntil($days, $pointDate = null)
    {
        if ($days < 0) {
            throw new \Exception('Invalid days supplied.');
        }

        $timeslots = [];
        for ($i = 0; $i <= $days; $i++) {
            $now = (!$pointDate) ? Carbon::now(env('APP_TIMEZONE')) : Carbon::createFromFormat('Y-m-d', $pointDate);
            $now->addDays($i);
            foreach (Timeslot::drawTimeslots() as $key => $time) {
                if (!self::drawDateTimeslotHasPassed($now->toDateString(), $key)) {
                    $timeslot = [
                        'key'  => $key,
                        'date' => $now->toDateString(),
                        'time' => $time,
                    ];
                    $timeslots[] = $timeslot;
                }
            }
        }

        return $timeslots;
    }

    /**
     * Get decremental timeslots until days.
     * 
     * @param integer $days
     * @throws \Exception
     * @return array[][]|string[][]
     */
    public static function getDecrementalTimeslotsUntil($days, $pointDate = null, $withObj = false)
    {
        if ($days < 1) {
            throw new \Exception('Invalid days supplied.');
        }

        $timeslots = [];
        for ($i = 0; $i <= $days; $i++) {
            $now = (!$pointDate) ? Carbon::now(env('APP_TIMEZONE')) : Carbon::createFromFormat('Y-m-d', $pointDate);
            $now->subDays($i);
            foreach (Timeslot::drawTimeslots() as $key => $time) {
                $timeslot = [
                    'key' => $key,
                    'date' => $now->toDateString(),
                    'time' => $time,
                ];
                if ($withObj) {
                    $timeslot['obj'] = $now;
                }
                $timeslots[] = $timeslot;
            }
        }

        return $timeslots;
    }

    /**
     * Check if current time has already passed the given drawtime.
     *
     * @param $date
     * @param null $timeslotKey
     * @return bool
     */
    public static function isDrawTimePassed($date, $timeslotKey = null, $subMins = null)
    {
        $now = Carbon::now(env('APP_TIMEZONE'));
        if (count(explode(' ', $date)) == 2) {
            $datetime = $date;
        } else if (!is_null($timeslotKey)) {
            $datetime = sprintf("%s %s", $date, Timeslot::getTimeByKey($timeslotKey));
        }
        $drawTime = Carbon::createFromFormat('Y-m-d H:i:s', $datetime, env('APP_TIMEZONE'));
        if ($subMins) {
            $drawTime->subMinutes($subMins);
        }
        
        return $now->gt($drawTime);
    }
    
    public static function validateDate($date, $format = 'Y-m-d')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }
    
    public static function globalCutOffTime()
    {
        return 30;
    }
    
    public static function globalPrepareToCutOffTime()
    {
        return 40;
    }
    
    public static function getCurrentDrawTime($noCutOff = false)
    {
        $now = Carbon::now(env('APP_TIMEZONE'));
        $prev = Carbon::today(env('APP_TIMEZONE'));
        $currentDrawTime = null;
        
        $drawTimeslots = Timeslot::drawTimeslots();
        
        foreach ($drawTimeslots as $schedKey => $timeslot) {
            if ($noCutOff) {
                $curr = Carbon::createFromFormat('H:i:s', $timeslot, env('APP_TIMEZONE')); 
            } else {
                $curr = Carbon::createFromFormat('H:i:s', $timeslot, env('APP_TIMEZONE'))->subMinutes(self::globalCutOffTime());
            }
            
            if ($now->greaterThanOrEqualTo($prev) && $now->lessThanOrEqualTo($curr)) {
                $currentDrawTime = $curr;
            }
            $prev = $curr;
            if ($currentDrawTime) {
                break;
            }
        }
        
        if ($prev && is_null($currentDrawTime)) {
            reset($drawTimeslots);
            if ($noCutOff) {
                $currentDrawTime = Carbon::createFromFormat('H:i:s', $drawTimeslots[key($drawTimeslots)], env('APP_TIMEZONE'))
                    ->addDays(1);
            } else {
                $currentDrawTime = Carbon::createFromFormat('H:i:s', $drawTimeslots[key($drawTimeslots)], env('APP_TIMEZONE'))
                    ->addDays(1)
                    ->subMinutes(self::globalCutOffTime());
            }
        }
        
        return $currentDrawTime;
    }
    
    public static function timeWithinDrawTimeBracket($datetime, $allowTimeBeyondBracket = false)
    {
        if (is_string($datetime) && self::validateDate($datetime, 'H:i:s')) {
            $datetime = Carbon::createFromFormat('H:i:s', $datetime, env('APP_TIMEZONE'));
        } else if (!$datetime instanceof Carbon) {
            throw new \Exception('Parameter should be a string or an instance of Carbon');
        }
        
        $start = Carbon::today(env('APP_TIMEZONE'));
        $currentDrawTime = self::getCurrentDrawTime();
        $drawTimeslots = Timeslot::drawTimeslots();
        
        foreach ($drawTimeslots as $schedKey => $timeslot) {
            $curr = Carbon::createFromFormat('H:i:s', $timeslot, env('APP_TIMEZONE'))->subMinutes(self::globalCutOffTime());
            if ($curr->equalTo($currentDrawTime)) {
                break;
            } else {
                $start = $curr;
                $start->addSeconds(1);
            }
        }
        
        if ($allowTimeBeyondBracket) {
            return $datetime->greaterThanOrEqualTo($start);
        }
       
        return $datetime->greaterThanOrEqualTo($start) && $datetime->lessThanOrEqualTo($currentDrawTime);
    }
    
    public static function transactionInputStringToSchedKey($input)
    {
        list($date, $time) = explode(' ', $input);
        return Timeslot::getKeyByTime($time);
    }

    public static function dateToday() {
        $date = Carbon::createFromFormat("Y-m-d", Carbon::now()->toDateString(), env('APP_TIMEZONE'));
        return $date->toDateString();
    }
}
