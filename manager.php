<?php

require __DIR__ . '/vendor/autoload.php';

define('BASE_PATH', dirname(__FILE__) . DIRECTORY_SEPARATOR);

define('CONFIG_PATH', BASE_PATH . 'config' . DIRECTORY_SEPARATOR);

define('CORE_PATH', BASE_PATH . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR);

define('APPS_PATH', CORE_PATH . DIRECTORY_SEPARATOR . 'apps'. DIRECTORY_SEPARATOR);

define('LOG_PATH', BASE_PATH . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR);

try
{
    require_once CORE_PATH . 'Bootstrap.php';

    Bootstrap::load(array(
        CORE_PATH
    ));
}
catch(Exception $e)
{
    echo "Error: " . $e->getMessage();
}

$server = new Server();

$server->run();