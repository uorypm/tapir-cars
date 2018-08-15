<?php

namespace Nieroo\Config;

/**
 * Class Config
 * @package Nieroo\Config
 */
class Config
{
    /**
     * @var Config
     */
    private static $instance;

    /**
     * @var array
     */
    private $settings = [];

    /**
     * Config constructor.
     *
     * @param array $settings
     */
    private function __construct(array $settings)
    {
        // TODO: Запилить проверку на корректность массива
        //       Для тестового задания пока оставлю так

        $this->settings = $settings;
    }

    /**
     * @param array $settings
     *
     * @return Config
     */
    public static function getInstance(array $settings) : self
    {
        if (!isset(self::$instance)
            || !(self::$instance instanceof self)
        ) {
            self::$instance = new self($settings);
        }

        return self::$instance;
    }

    /**
     * @param string $section
     *
     * @return mixed|null
     */
    private function getConfig(string $section)
    {
        return $this->settings[$section] ?? null;
    }

    /**
     * Возращает параметры подключения к БД
     *
     * @return mixed|null Параметры подключения к БД
     */
    public function getConfigDB()
    {
        return $this->getConfig('db');
    }

    /**
     * Возращает параметры внешних источников данных
     *
     * @return mixed|null Параметры внешних источников данных
     */
    public function getConfigSource()
    {
        return $this->getConfig('source');
    }

    /**
     * Возращает параметры отображения данных
     *
     * @return mixed|null Параметры отображения данных
     */
    public function getConfigScreen()
    {
        return $this->getConfig('screen');
    }
}
