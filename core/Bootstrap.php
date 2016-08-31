<?php

class Bootstrap
{
    private static $m_loaded = false;

    private static $m_files = array();

    // Directories excluded from Autoloading
    private static $m_excluded = array(

    );

    private function __construct()
    {

    }

    public static function load($paths, $exclude = array())
    {
        if (self::$m_loaded)
        {
            return;
        }

        $files = array();
        foreach($paths as $path)
        {
            $files = array_merge($files, self::scan_path($path, $exclude));
        }

        self::$m_files = $files;

        spl_autoload_register(__CLASS__ . '::spl_autoloader');

        self::$m_loaded = true;
    }

    public static function spl_autoloader($class)
    {
        $class = ucfirst($class);

        foreach(self::$m_files as $file)
        {
            if (basename($file, ".php") == $class)
            {
                include_once $file;
                return;
            }
        }
    }

    public static function scan_path($path, $exclude = array())
    {
        $paths = array();

        $di = new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS);

        // Exclude 'apps' directory
        $filter = new DirectoryFilter($di, self::$m_excluded);

        $it = new RecursiveIteratorIterator($filter);

        foreach($it as $path)
        {
            if (pathinfo($path, PATHINFO_EXTENSION) == "php")
            {
                $paths[] = $path;
            }
        }


        return $paths;
    }


}

class DirectoryFilter extends RecursiveFilterIterator
{
    protected $exclude;

    public function __construct($iterator, $exclude = array())
    {
        parent::__construct($iterator);
        $this->exclude = $exclude;
    }

    public function accept()
    {
        return !($this->isDir() && in_array($this->getFilename(), $this->exclude));
    }

    public function getChildren()
    {
        return new DirectoryFilter($this->getInnerIterator()->getChildren(), $this->exclude);
    }
}