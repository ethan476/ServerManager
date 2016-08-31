<?php

class Utils
{
    public static function find_directories($path)
    {
        $paths = array();

        $dirs = scandir($path);

        foreach($dirs as $dir)
        {
            if ($dir != "." && $dir != "..")
            {
                if (is_dir($path . DIRECTORY_SEPARATOR . $dir))
                {
                    $paths[] = $dir;
                }
            }
        }

        return $paths;
    }

}