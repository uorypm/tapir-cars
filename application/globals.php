<?php

use Nieroo\Config\Config;
use Nieroo\DB\DB;
use Nieroo\Tapir\Application\Application;

error_reporting(E_ALL | E_WARNING | E_NOTICE | E_STRICT);
ini_set('display_errors', 'On');

global $application;

$application = new Application();

$application->setConfig(
    Config::getInstance(
        require_once __DIR__ . '/.settings.php'
    )
);

$application->setDB(
    DB::getInstance(
        $application->getConfig()->getConfigDB()
    )
);
