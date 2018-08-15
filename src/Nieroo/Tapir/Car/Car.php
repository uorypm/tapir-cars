<?php

namespace Nieroo\Tapir\Car;

use Nieroo\DB\DAO\CarTable;
use StdClass;
use Nieroo\DB\DB;

/**
 * Class CarTable Автомобиль б/у
 * @package Nieroo\Tapir\CarTable
 */
class Car
{
    /**
     * @var int Id
     */
    private $id = null;

    /**
     * @var string Модель
     */
    private $model = '';

    /**
     * @var string Цвет
     */
    private $color = '';

    /**
     * @var
     */
    private $transmission = '';

    /**
     * @var float Цена
     */
    private $price = 0.0;

    /**
     * @var float Пробег
     */
    private $km = 0.0;

    /**
     * @var Owner[] Владельцы
     */
    private $owners = [];

    /**
     * @var int Мощность (в л.с.)
     */
    private $power = 0;

    /**
     * @var float Объём двигателя
     */
    private $engineCapacity = 0.0;

    /**
     * @var string Номер
     *
     * @todo Какой-то уникальный ключ (уточнить у заказчика)
     */
    private $vin = '';

    /**
     * @var CarTable
     */
    private $table;

    /**
     * Car constructor.
     *
     * @param StdClass $stdClass
     * @param CarTable $table
     */
    public function __construct(StdClass $stdClass, CarTable $table)
    {
        $this->table = $table;

        if (isset($stdClass->id)) {
            $this->id = \intval($stdClass->id);
        }

        if (isset($stdClass->vin)) {
            $this->vin = \trim($stdClass->vin);
        }

        if (isset($stdClass->model)) {
            $this->model = \trim($stdClass->model);
        }

        if (isset($stdClass->km)) {
            $this->km = \floatval($stdClass->km);
        }

        if (isset($stdClass->color)) {
            $this->color = \trim($stdClass->color);
        }

        if (isset($stdClass->owners)) {
            $this->owners = \intval($stdClass->owners);
        }

        if (isset($stdClass->power)) {
            $this->power = \intval($stdClass->power);
        }

        if (isset($stdClass->engineCapacity)) {
            $this->engineCapacity = \floatval($stdClass->engineCapacity);
        }

        if (isset($stdClass->transmission)) {
            $this->transmission = \trim($stdClass->transmission);
        }

        if (isset($stdClass->price)) {
            $this->price = \floatval($stdClass->price);
        }
    }

    /**
     * Сохраняет автомобиль в БД
     *
     * @return bool
     */
    public function add()
    {
        return $this->table->add($this);
    }

    /**
     * @return null|string
     */
    public function getLastError()
    {
        return $this->table->lastError;
    }

    /**
     * @param string $param
     *
     * @return mixed|null
     */
    public function get(string $param)
    {
        return isset($this->$param) ? $this->$param : null;
    }

    /**
     * @return mixed|null
     */
    public function getId()
    {
        return $this->get('id');
    }

    /**
     * @return mixed|null
     */
    public function getVin()
    {
        return $this->get('vin');
    }

    /**
     * @return mixed|null
     */
    public function getModel()
    {
        return $this->get('model');
    }

    /**
     * @return mixed|null
     */
    public function getColor()
    {
        return $this->get('color');
    }

    /**
     * @return mixed|null
     */
    public function getPrice()
    {
        return $this->get('price');
    }

    /**
     * @return mixed|null
     */
    public function getPower()
    {
        return $this->get('power');
    }

    /**
     * @return mixed|null
     */
    public function getTransmission()
    {
        return $this->get('transmission');
    }

    /**
     * @return mixed|null
     */
    public function getKm()
    {
        return $this->get('km');
    }

    /**
     * @return mixed|null
     */
    public function getOwners()
    {
        return $this->get('owners');
    }

    /**
     * @return mixed|null
     */
    public function getEngineCapacity()
    {
        return $this->get('engineCapacity');
    }

    /**
     * @param array $filter
     * @param DB $db
     *
     * @return StdClass[]
     */
    public static function getCars(array $filter = null, DB $db) : array
    {
        return CarTable::getAll($filter, $db);
    }
}
