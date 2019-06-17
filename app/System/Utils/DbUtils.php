<?php
namespace App\System\Utils;

class DbUtils
{
    public static function backupDBAsFile($filename, $destination = null)
    {
        try {
            // Credentials
            $hostname = env('DB_HOST');
            $username = env('DB_USERNAME');
            $password = env('DB_PASSWORD');
            $database = env('DB_DATABASE');
            $filename = realpath($destination).DIRECTORY_SEPARATOR.$filename;
            
            // Concatenate to command
            $cmd = sprintf("mysqldump -h %s -u %s -p%s %s > %s --skip-tz-utc", $hostname, $username, $password, $database, $filename);
            
            // Execute
            exec($cmd);
        } catch (\Exception $e) {
            report($e);
            throw $e;
        }
    }
}
