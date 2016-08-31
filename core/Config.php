<?php

class Config extends Singleton
{
    private $m_config = array();

    private $m_path = null;

    public function __construct($path = null)
    {
        if ($path == null)
        {
            $path = CONFIG_PATH . 'config.json';
        }

        $this->m_path = path;
        try
        {
            $this->m_config = json_decode(file_get_contents($path), true);
        }
        catch(Exception $e)
        {
            echo "Failed to load configuration file: '" . $path . "'.";
        }
    }

    public function get_setting($keys, $default_value = null)
    {
        if (is_string($keys))
        {
            if (isset($this->m_config[$keys]))
            {
                return $this->m_config[$keys];
            }
        }
        else if (is_array($keys))
        {
            $ptr = $this->m_config;

            foreach($keys as $key)
            {
                if (isset($ptr[$key]))
                {
                    $ptr = $ptr[$key];
                }
                else
                {
                    return $default_value;
                }
            }
        }

        return $default_value;
    }

    public static function get($keys, $default_value = null)
    {
        $instance = self::instance();

        $instance->get_setting($keys, $default_value);
    }
}