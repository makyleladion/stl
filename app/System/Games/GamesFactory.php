<?php

namespace App\System\Games;

use App\Outlet;
use App\System\Games\Pares\ParesGame;
use App\System\Games\Swertres\SwertresGame;
use App\System\Games\SwertresSTL\SwertresSTLGame;
use App\System\Games\SwertresSTLLocal\SwertresSTLLocalGame;

class GamesFactory
{
    private static $swertresInstances = [];
    private static $swertresSTLInstance = [];
    private static $swertresSTLLocalInstance = [];
    private static $paresInstances = [];

    public static function getGame($name, Outlet $outlet = null)
    {
        if ($name === SwertresGame::name()) {
            return self::createSwertres($outlet);
        } else if ($name === ParesGame::name()) {
            return self::createPares($outlet);
        } else if ($name === SwertresSTLGame::name()) {
            return self::createSwertresSTL($outlet);
        } else if ($name === SwertresSTLLocalGame::name()) {
            return self::createSwertresSTLLocal($outlet);
        } else {
            throw new \Exception('Game does not exist: '.$name);
        }
    }

    public static function getGames($outlet = null)
    {
        return [
            self::createSwertres($outlet),
            self::createSwertresSTL($outlet),
            self::createSwertresSTLLocal($outlet),
        ];
    }

    public static function getGameNames()
    {
        return [
            SwertresGame::name(),
            SwertresSTLGame::name(),
            SwertresSTLLocalGame::name(),
        ];
    }
    
    public static function getGameLabelByGameName($gameName)
    {
        if ($gameName == ParesGame::name()) {
            return ParesGame::GAME_LABEL;
        } else if ($gameName == SwertresGame::name()) {
            return SwertresGame::GAME_LABEL;
        } else if ($gameName == SwertresSTLGame::name()) {
            return SwertresSTLGame::GAME_LABEL;
        } else if ($gameName == SwertresSTLLocalGame::name()) {
            return SwertresSTLLocalGame::GAME_LABEL;
        }
        
        throw new \Exception('Invalid game name.');
    }

    private static function createSwertres(Outlet $outlet = null)
    {
        if ($outlet instanceof Outlet) {
            $key = (string) $outlet->id;
        } else {
            $key = '0';
        }
        if (!isset(self::$swertresInstances[$key])) {
            self::$swertresInstances[$key] = new SwertresGame($outlet);
        }

        return self::$swertresInstances[$key];
    }
    
    private static function createSwertresSTL(Outlet $outlet = null)
    {
        if ($outlet instanceof Outlet) {
            $key = (string) $outlet->id;
        } else {
            $key = '0';
        }
        if (!isset(self::$swertresSTLInstance[$key])) {
            self::$swertresSTLInstance[$key] = new SwertresSTLGame($outlet);
        }
        
        return self::$swertresSTLInstance[$key];
    }
    
    private static function createSwertresSTLLocal(Outlet $outlet = null)
    {
        if ($outlet instanceof Outlet) {
            $key = (string) $outlet->id;
        } else {
            $key = '0';
        }
        if (!isset(self::$swertresSTLLocalInstance[$key])) {
            self::$swertresSTLLocalInstance[$key] = new SwertresSTLLocalGame($outlet);
        }
        
        return self::$swertresSTLLocalInstance[$key];
    }

    private static function createPares(Outlet $outlet = null)
    {
        if ($outlet instanceof Outlet) {
            $key = (string) $outlet->id;
        } else {
            $key = '0';
        }
        if (!isset(self::$paresInstances[$key])) {
            self::$paresInstances[$key] = new ParesGame($outlet);
        }

        return self::$paresInstances[$key];
    }
}
