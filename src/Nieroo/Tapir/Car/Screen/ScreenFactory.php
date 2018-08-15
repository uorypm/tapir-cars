<?php

namespace Nieroo\Tapir\Car\Screen;

use Nieroo\Config\Config;
use ReflectionClass;

use ReflectionException;
use RuntimeException;
use InvalidArgumentException;

/**
 * Class ScreenFactory
 * @package Nieroo\Tapir\Car\Screen
 */
class ScreenFactory
{
    /**
     * @param Config $config
     * @param array $storage
     *
     * @return ScreenInterface
     *
     * @throws InvalidArgumentException Если тип экрана задан некорректно
     * @throws RuntimeException Если тип экрана не наследует класс ScreenInterface
     * @throws ReflectionException Если не найден экран данного типа
     */
    public static function getScreen(
        Config $config,
        array $storage
    ) : ScreenInterface
    {
        if (!isset($config->getConfigScreen()['type'])) {
            throw new \InvalidArgumentException('Screen type not defined');
        }

        $className = __NAMESPACE__ . '\\Screen' . \ucfirst(strtolower(
            $config->getConfigScreen()['type']
        ));

        $reflection = new ReflectionClass($className);

        if (!$reflection->isSubclassOf(ScreenInterface::class)) {
            throw new \RuntimeException('Screen class wrong parent');
        }

        return new $className($storage);
    }
}
