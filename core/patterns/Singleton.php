<?php

class Singleton
{
    private static $instances = array();

    protected function __construct()
    {

    }

    public static function instance($args = array())
    {
        $class = get_called_class();

        if (!isset(self::$instances[$class])) {
            self::$instances[$class] = new static(...($args));
        }

        return self::$instances[$class];
    }

    final private function __clone()
    {

    }
}