<?php

namespace App\System\Data;

class Timeslot
{
    const TIMESLOT_MORNING   = 'bet_schedule_morning';
    const TIMESLOT_AFTERNOON = 'bet_schedule_afternoon';
    const TIMESLOT_EVENING   = 'bet_schedule_evening';

    /**
     * STL defined draw timeslots.
     *
     * @return array
     */
    public static function drawTimeslots()
    {
        return [
            self::TIMESLOT_MORNING   => '11:00:00',
            self::TIMESLOT_AFTERNOON => '16:00:00',
            self::TIMESLOT_EVENING   => '21:00:00',
        ];
    }

    /**
     * Get timeslot keys.
     *
     * In the system, draw timeslots are defined by a key.
     * This method will return all defined keys in the system.
     *
     * @return array
     */
    public static function getTimeslotKeys()
    {
        return array_keys(self::drawTimeslots());
    }

    /**
     * Get the actual time by its corresponding key.
     *
     * @param $key
     * @return mixed
     * @throws \Exception
     */
    public static function getTimeByKey($key)
    {
        if (array_key_exists($key, self::drawTimeslots())) {
            return self::drawTimeslots()[$key];
        }
        throw new \Exception('Invalid Timeslot constant: ' . $key);
    }

    /**
     * Get the key of an actual time given.
     * Return and exception if time is incorrect.
     *
     * @param $time
     * @return false|int|string
     * @throws \Exception
     */
    public static function getKeyByTime($time)
    {
        if (in_array($time, self::drawTimeslots())) {
            return array_search($time, self::drawTimeslots());
        }
        throw new \Exception('Invalid Time given: ' . $time);
    }
}
