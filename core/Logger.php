<?php

class Logger
{
    private static $instance = null;

    private $path = null;

    private $buffer = array();

    private $flush_threshold = 20;

    public function __construct($path = null, $flush_threshold = 20)
    {
        if (is_null($path))
        {
            $this->path = time() . ".log";
        }

        $this->path = $path;

        // Log every n msgs
        $this->flush_threshold = $flush_threshold;
    }

    public function __destruct()
    {
        $this->flush();
    }

    public static function instance()
    {
        if (is_null(self::$instance))
        {
            self::$instance = new Logger(LOG_PATH . 'server-manager.log');
        }

        return self::$instance;
    }

    // Possible double write to buffer on exception
    public function flush()
    {
        if (count($this->buffer) == 0)
        {
            return;
        }

        try
        {
            if (!is_dir(dirname($this->path)))
            {
                mkdir(dirname($this->path), 0755, true);
            }

            $fh = fopen($this->path, 'a');
            if (!$fh)
            {
                return false;
            }

            flock($fh, LOCK_EX);

            foreach ($this->buffer as $msg)
            {
                fwrite($fh, $msg . PHP_EOL);
            }

            flock($fh, LOCK_UN);
            fclose($fh);
        } catch (Exception $e)
        {
            //hmm
            echo $e->getMessage();
        }

        return true;
    }

    public function log($msg, $callback = null)
    {
        if (is_callable($callback))
        {
            $msg = $callback($msg);
        }

        $this->write($msg);
    }

    public function write($msg)
    {
        echo $msg . PHP_EOL;

        $this->buffer[] = $msg;

        if (count($this->buffer) > $this->flush_threshold)
        {
            $this->flush();
        }
    }

    public function info($msg)
    {
        $msg = self::time_format() . " INFO: " . $msg;

        $this->log($msg);
    }

    public function warning($msg)
    {
        $msg = self::time_format() . " WARNING: " . $msg;

        $this->log($msg);
    }

    public function error($msg)
    {
        $msg = self::time_format() . " ERROR: " . $msg;

        $this->log($msg);

        //Flush to be safe
        $this->flush();
    }

    public static function time_format($time = null, $format = "Y-m-d H:i:s")
    {
        if ($time == null)
        {
            $time = time();
        }

        return "[" . date($format, $time) . "]:";
    }
}