<?php

namespace Nieroo\DB\DAO;

use PDO;

/**
 * Interface DAOAbstract
 * @package Nieroo\DB\DAO
 */
abstract class DAOAbstract
{
    /**
     * @var PDO Дескриптор подключения к БД
     */
    protected $pdo;

    /**
     * DAOAbstract constructor.
     *
     * @param PDO $pdo Дескриптор подключения к БД
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }
}
