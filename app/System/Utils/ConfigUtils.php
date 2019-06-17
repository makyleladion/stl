<?php
namespace App\System\Utils;

use App\Config;

final class ConfigUtils
{
    const TYPE_NUMBER = 'number';
    const TYPE_BOOL = 'boolean';
    const TYPE_STRING = 'string';
    const TYPE_JSON = 'json';
    const TYPE_OBJECT = 'object';
    
    public static function get($name)
    {
        $config = Config::where('name', $name)->first();
        if ($config) {
            return self::handleValueByType($config->type, $config->value);
        }
        
        throw new \Exception('The config '.$name.' could not be found.');
    }
    
    private static function handleValueByType($type, $value)
    {
        switch ($type) {
            case self::TYPE_BOOL:
                return (strtolower($value) === 'true') ? true : false;
            case self::TYPE_JSON:
                return json_decode($value);
            case self::TYPE_OBJECT:
                return unserialize($value);
            case self::TYPE_NUMBER:
            case self::TYPE_STRING:
                return $value;
                
            default:
                return $value;
        }
    }
}
