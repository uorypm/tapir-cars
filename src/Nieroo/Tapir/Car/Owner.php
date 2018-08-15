<?php

namespace Nieroo\Tapir\Car;

/**
 * Class Owner Владелец автомобиля б/у
 * @package Nieroo\Tapir\CarTable
 */
class Owner
{
    /**
     * @var self[] Список Владельцев
     */
    private static $owners = [];

    /**
     * @var int ID Владельца
     */
    private $id;

    /**
     * Создаёт экземпляр класса Owner
     *
     * @param int $ownerId
     */
    private function __construct(int $ownerId)
    {
        $this->id = $ownerId;
    }

    /**
     * @param int $ownerId
     *
     * @return Owner
     */
    public static function getInstance(int $ownerId) : self
    {
        if (!isset(self::$owners[$ownerId])
            || !(self::$owners[$ownerId] instanceof self)
        ) {
            self::$owners[$ownerId] = new self($ownerId);
        }

        return self::$owners[$ownerId];
    }
}
