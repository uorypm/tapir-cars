<?php

namespace Nieroo\DB\DAO;

use Nieroo\Tapir\Car\Car;
use StdClass;
use Nieroo\DB\DB;
use PDO;

use PDOException;
use RuntimeException;

/**
 * Class CarTable
 * @package Nieroo\DB\DAO
 */
class CarTable extends DAOAbstract implements TableInterface
{
    private static $tableName = 'cars';

    /**
     * @var string|null Последняя ошибка
     */
    public $lastError = null;

    /**
     * @var array Ошибки фильтра
     */
    public static $filterErrors = [];

    /**
     * Сохраняет автомобиль в БД
     *
     * @param Car $car
     *
     * @return bool
     */
    public function add(Car $car)
    {
        $tableName = self::$tableName;

        $sql = "
            INSERT INTO `{$tableName}` (
              `vin`,
              `model`,
              `km`,
              `color`,
              `power`,
              `engine_capacity`,
              `transmission`,
              `price`,
              `owners`
            ) VALUES (
              :vin,
              :model,
              :km,
              :color,
              :power,
              :engine_capacity,
              :transmission,
              :price,
              :owners
            )
            ON DUPLICATE KEY UPDATE
              `model` = VALUES(`model`),
              `km` = VALUES(`km`),
              `color` = VALUES(`color`),
              `power` = VALUES(`power`),
              `engine_capacity` = VALUES(`engine_capacity`),
              `transmission` = VALUES(`transmission`),
              `price` = VALUES(`price`),
              `owners` = VALUES(`owners`)
        ";

        try {
            $stmt = $this->pdo->prepare($sql);

            $result = $stmt->execute([
                ':vin'              => $car->getVin(),
                ':model'            => $car->getModel(),
                ':km'               => $car->getKm(),
                ':color'            => $car->getColor(),
                ':power'            => $car->getPower(),
                ':engine_capacity'  => $car->getEngineCapacity(),
                ':transmission'     => $car->getTransmission(),
                ':price'            => $car->getPrice(),
                ':owners'           => $car->getOwners(),
            ]);

            if ($result === false) {
                throw new RuntimeException('Car add error');
            }
        } catch (PDOException $e) {
            $this->lastError = $e->getMessage();

            return false;
        }

        return true;
    }

    /**
     * @param array|null $filter
     * @param DB $db
     *
     * @return StdClass[]
     */
    public static function getAll(array $filter = null, DB $db)
    {
        self::$filterErrors = [];

        self::checkFilter($filter);

        if (self::$filterErrors) {
            return [];
        }

        $sqlFilter = self::prepareFilterForSql($filter);

        $where = '';
        if ($sqlFilter['sql']) {
            $where =  'WHERE ' . implode(' AND ', $sqlFilter['sql']);
        }

        $tableName = self::$tableName;

        $sql = "
            SELECT *
              FROM `{$tableName}`
                   {$where}
        ";

        $stmt = $db->getPDO()->prepare($sql);

        $stmt->execute($sqlFilter['stmt']);

        return $stmt->fetchAll(PDO::FETCH_CLASS);
    }

    /**
     * @param array|null $filter
     */
    private static function checkFilter(array &$filter = null)
    {
        foreach ($filter as $key => &$value) {
            switch ($key) {
                case 'owners':
                case 'power':
                    self::validateInteger($key, $value, true);

                    break;
                case 'price':
                case 'km':
                case 'engineCapacity':
                    self::validateFloat($key, $value, true);

                    break;
                case 'id':
                    self::validateInteger($key, $value);

                    break;
                case 'vin':
                case 'model':
                case 'color':
                case 'transmission':
                    self::validateString($key, $value);

                    break;
                default:
                    self::$filterErrors[] = "wrong filter field '{$key}'";

                    break;
            }
        }
    }

    /**
     * @param string $key
     * @param string $value
     *
     * @param bool $range
     */
    private static function validateInteger(
        string $key,
        string &$value,
        bool $range = false
    ) {
        if ($range) {
            $rangedData = \explode(',', $value);

            if (\count($rangedData) > 2) {
                self::$filterErrors[] = "Too many values for field '{$key}'";

                return;
            }

            $value = $rangedData;
        } else {
            $value = [
                $value,
            ];
        }

        foreach ($value as &$data) {
            $data = \strtolower(\trim($data));

            if ($data === 'inf') {
                continue;
            }

            if (!self::isInt($data)) {
                self::$filterErrors[] = "'{$key}' must be float";

                return;
            }
        }

        if (\count($value) === 1) {
            $value[1] = $value[0];
        }

        if (!self::normalizeRangeValue($value)) {
            self::$filterErrors[] = "'{$key}' has wrong range";
        }
    }

    /**
     * @param string $key
     * @param string $value
     *
     * @param bool $range
     */
    private static function validateFloat(
        string $key,
        string &$value,
        bool $range = false
    ) {
        if ($range) {
            $rangedData = \explode(',', $value);

            if (\count($rangedData) > 2) {
                self::$filterErrors[] = "Too many values for field '{$key}'";

                return;
            }

            $value = $rangedData;
        } else {
            $value = [
                $value,
            ];
        }

        foreach ($value as &$data) {
            $data = \strtolower(\trim($data));

            if ($data === 'inf') {
                continue;
            }

            if (!self::isFloat($data)) {
                self::$filterErrors[] = "'{$key}' must be float";

                return;
            }
        }

        if (\count($value) === 1) {
            $value[1] = $value[0];
        }

        if (!self::normalizeRangeValue($value)) {
            self::$filterErrors[] = "'{$key}' has wrong range";
        }
    }

    /**
     * Проверяет, что значение переменной является числом типа int
     *
     * @param mixed $value
     *
     * @return bool Возвращает true, если значение переменной число типа int,
     *              иначе возвращает false
     */
    private static function isInt($value)
    {
        if (!\is_numeric($value)
            || $value != \intval($value)
            || $value < 0
        ) {
            return false;
        }

        return true;
    }

    /**
     * Проверяет, что значение переменной является числом типа float
     *
     * @param mixed $value
     *
     * @return bool Возвращает true, если значение переменной число типа float,
     *              иначе возвращает false
     */
    private static function isFloat($value)
    {
        if (!\is_numeric($value)
            || $value != \floatval($value)
            || $value != round($value, 2)
            || $value < 0
        ) {
            return false;
        }

        return true;
    }

    /**
     * Нормализует данные типа "диапазон"
     *
     * @param array $value
     *
     * @return bool Если данные успешно нормализованы, то возвращает true,
     *              иначе - false
     */
    private static function normalizeRangeValue(array &$value)
    {
        if ($value[0] === 'inf'
            && $value[1] === 'inf'
        ) {
            return false;
        } elseif ($value[0] === 'inf'
            || (
                $value[1] !== 'inf'
                && $value[1] < $value[0]
            )
        ) {
            $value[2] = $value[1];
            $value[1] = $value[0];
            $value[0] = $value[2];

            unset($value[2]);
        }

        return true;
    }

    /**
     * @param string $key
     * @param string $value
     *
     * @param bool $range
     */
    private static function validateString(
        string $key,
        string &$value,
        bool $range = false
    ) {
        $value = \trim($value);

        if (\strlen($value) === 0) {
            self::$filterErrors[] = "{$key} must be no empty string";
        }
    }

    /**
     * @param array|null $filter
     *
     * @return array
     */
    private static function prepareFilterForSql(array $filter = null)
    {
        $arWhere = [
            'sql'   => [],
            'stmt'  => [],
        ];

        foreach ($filter as $key => $value) {
            switch ($key) {
                case 'owners':
                case 'power':
                case 'price':
                case 'km':
                    if ($value[1] === 'inf') {
                        $arWhere['sql'][] = "`{$key}` >= :{$key}";
                        $arWhere['stmt'][":{$key}"] = $value[0];
                    } else {
                        $arWhere['sql'][] = "`{$key}` BETWEEN :{$key}0 AND :{$key}1";
                        $arWhere['stmt'][":{$key}0"] = $value[0];
                        $arWhere['stmt'][":{$key}1"] = $value[1];
                    }

                    break;
                case 'engineCapacity':
                    if ($value[1] === 'inf') {
                        $arWhere['sql'][] = "`engine_capacity` >= :{$key}";
                        $arWhere['stmt'][":{$key}"] = $value[0];
                    } else {
                        $arWhere['sql'][] = "`engine_capacity` BETWEEN :engine_capacity0 AND :engine_capacity1";
                        $arWhere['stmt'][":engine_capacity0"] = $value[0];
                        $arWhere['stmt'][":engine_capacity1"] = $value[1];
                    }

                    break;
                case 'id':
                case 'vin':
                case 'model':
                case 'color':
                case 'transmission':
                    $arWhere['sql'][] = "`{$key}` = :{$key}";
                    $arWhere['stmt'][":{$key}"] = $value;

                    break;
                default:
                    break;
            }
        }

        return $arWhere;
    }
}
