#!/usr/bin/env php
<?php

use Symfony\Component\Console\Application;
use Nieroo\Tapir\Command\ImportCarsCommand;
use Nieroo\Tapir\Command\CreateCarsTableCommand;

require __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../application/globals.php';

$consoleApp = new Application();

$consoleApp->add(new ImportCarsCommand());
$consoleApp->add(new CreateCarsTableCommand());

$consoleApp->run();
