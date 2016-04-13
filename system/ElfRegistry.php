<?php

/**
 * Elf - Registry 
 *
 */
class Elf
{

    private static $registry = array();

    public static function Set($index, $value)
    {
        self::$registry[$index] = &$value;
    }

    public static function &Get($index)
    {
        if(isset(self::$registry[$index]))
        {
            return self::$registry[$index];
        }
        else
        {
            throw new ElfException("No entry is registered for key '$index'");
        }
    }

    /*
     * Дальше полезные сокращения для быстрого доступа
     */

    /**
     * Return DbSimple_Mysql object
     * @return DbSimple_Mysql
     */
    public static function Db()
    {
        return self::Get('db');
    }

    /**
     *
     * @return Auth
     */
    public static function Auth()
    {
        return self::Get('auth');
    }

    /**
     *
     * @param string $key
     * @return mixed
     */
    public static function Settings($key)
    {
        return self::Get('settings')->Get($key);
    }

}

/**
 * Exception
 */
class ElfException extends Exception
{

}

?>
