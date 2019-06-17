<?php
namespace App\System\Utils;

class QueryUtils
{
    public static function appendSkipTake($query, $skip, $take)
    {
        if (!is_numeric($skip) || !is_numeric($skip)) {
            throw new \Exception('Invalid skip/take value!');
        }

        return $query->skip($skip)->take($take);
    }
}
