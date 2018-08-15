<?php

namespace Nieroo\Tapir\Car\Screen;

/**
 * Interface ScreenInterface
 * @package Nieroo\Tapir\Car\Screen
 */
interface ScreenInterface
{
    /**
     * ScreenInterface constructor.
     *
     * @param array $storage
     */
    public function __construct(array $storage);

    /**
     * Отображает данные
     *
     * @return string
     */
    public function display();
}
