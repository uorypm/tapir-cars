<?php

namespace Nieroo\Tapir\Car\Screen;

use Nieroo\DB\DAO\CarTable;
use StdClass;

/**
 * Class ScreenJson
 * @package Nieroo\Tapir\Care\Screen
 */
class ScreenJson implements ScreenInterface
{
    /**
     * @var array
     */
    private $filterErrors = [];

    /**
     * @var array
     */
    private $data;

    /**
     * @inheritdoc
     */
    public function __construct(array $storage)
    {
        $this->data = new StdClass();

        $this->data->data = [];

        $this->data->errors = CarTable::$filterErrors;
        if ($this->data->errors) {
            $this->data->message = 'validation error';
        } else {
            $this->data->message = 'ok';
        }

        foreach ($storage as $stdClass) {
            $this->data->data[] = $stdClass;
        }
    }

    /**
     * Отображает данные в виде JSON-строки
     *
     * @return void
     */
    public function display()
    {
        header('Content-Type: application/json');

        echo json_encode($this->data, JSON_PRETTY_PRINT);
    }
}
