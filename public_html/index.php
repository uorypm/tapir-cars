<?php

use Nieroo\Tapir\Car\Car;
use Nieroo\Tapir\Car\Screen\ScreenFactory;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../application/globals.php';

global $application;

$screen = ScreenFactory::getScreen(
    $application->getConfig(),
    Car::getCars($_GET, $application->getDB())
);
$screen->display();

//$storage = new CarStorage();
//
//$storage->attach(CarOwner::getInstance(33));
//
//$storage->rewind();
//
//while ($storage->valid()) {
//    var_dump($storage->current());
//    $storage->next();
//}
