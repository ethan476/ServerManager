<?php

class AppType
{
    const HTTP = 1;
    const WS = 2;

    public static function str_value($type)
    {
        switch($type)
        {
            case HTTP:
                return "HTTP";
            case WS:
                return "WS";
            default:
                return "HTTP";
        }
    }
}

class App
{
    private static $m_loaded_apps = array();

    private static $m_app_directories = null;

    private $m_routes = array();

    protected function __construct()
    {

    }

    protected function load_routes($routes = array())
    {
        array_merge($this->m_routes, $routes);
    }

    public function __destruct()
    {
        // TODO: Implement __destruct() method.
    }

    public static function load_directories()
    {
        if (!is_array(self::$m_app_directories))
        {
            self::$m_app_directories = Utils::find_directories(APPS_PATH);
        }
    }


    public static function load_apps($type = AppType::HTTP)
    {
        self::load_directories();

        foreach(self::$m_app_directories as $dir)
        {

            self::load_app($dir);
        }
    }

    public static function load_app($dir, $type = AppType::HTTP)
    {
        $logger = Logger::instance();

        self::load_directories();

        $name = ucfirst(basename($dir));

        $logger->info("Attempting to load app '" . $name . "' from '" . $dir . "'.");

        if (file_exists($dir . DIRECTORY_SEPARATOR . $name . ".php"))
        {
            try
            {
                require_once $dir . DIRECTORY_SEPARATOR . strtolower(AppType::str_value($type)) . DIRECTORY_SEPARATOR . $name . ".php";

                $instance = new $name;
                self::$m_loaded_apps[lcfirst($name)] = $instance;

                return true;
            }
            catch(Exception $e)
            {
                return false;
            }
        }
    }
}