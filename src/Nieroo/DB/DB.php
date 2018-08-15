<?php

namespace Nieroo\DB;

use PDO;
use PDOException;
use InvalidArgumentException;
use RuntimeException;

/**
 * Class DB
 * @package Nieroo\DB
 */
class DB
{
    /**
     * @var DB
     */
    private static $instance;

    /**
     * @var PDO
     */
    private static $pdo;

    /**
     * DB constructor.
     *
     * @param array $connectionSettings
     *
     * @throws InvalidArgumentException Если не переданы параметры подключения
     * @throws RuntimeException         Если не удалось подключиться к БД
     */
    private function __construct(array $connectionSettings)
    {
        if (!isset($connectionSettings['dsn'])) {
            throw new InvalidArgumentException('Wrong DB dsn');
        }

        if (!isset($connectionSettings['username'])) {
            throw new InvalidArgumentException('Wrong DB username');
        }

        if (!isset($connectionSettings['password'])) {
            throw new InvalidArgumentException('Wrong DB password');
        }

        if (isset($connectionSettings['options'])
            && !is_array($connectionSettings['options'])
        ) {
            throw new InvalidArgumentException('Wrong DB extra options');
        } elseif (!isset($connectionSettings['options'])) {
            $connectionSettings['options'] = [];
        }

        try {
            self::$pdo = new PDO(
                $connectionSettings['dsn'],
                $connectionSettings['username'],
                $connectionSettings['password'],
                $connectionSettings['options']
            );

            self::$pdo->setAttribute(
                PDO::ATTR_ERRMODE,
                PDO::ERRMODE_EXCEPTION
            );
        } catch (PDOException $e) {
            throw new RuntimeException(
                "Wrong DB connection: {$e->getMessage()}",
                0,
                $e
            );
        }
    }

    /**
     * @param array $connectionSettings
     *
     * @return DB
     */
    public static function getInstance(array $connectionSettings) : self
    {
        if (!isset(self::$instance)
            || !(self::$instance instanceof PDO)
        ) {
            self::$instance = new self($connectionSettings);
        }

        return self::$instance;
    }

    /**
     * @return PDO
     */
    public function getPDO()
    {
        return self::$pdo;
    }
}
