<?php

namespace Nieroo\Tapir\Application;

use Nieroo\DB\DB;
use Nieroo\Config\Config;

/**
 * Class Application
 * @package Nieroo\Tapir\Application
 */
class Application
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var DB
     */
    private $db;

    /**
     * @param Config $config
     */
    public function setConfig(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param DB $db
     */
    public function setDB(DB $db)
    {
        $this->db = $db;
    }

    /**
     * @return DB
     */
    public function getDB()
    {
        return $this->db;
    }
}
