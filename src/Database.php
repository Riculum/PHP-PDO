<?php
namespace Riculum;

use PDO;

class Database
{
    private static ?PDO $PDO;

    public static function connect()
    {
        if (empty(self::$PDO)) {
            self::$PDO = new PDO('mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'], $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD']);
            self::$PDO->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            self::$PDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
    }

    public static function beginTransaction()
    {
        self::connect();
        self::$PDO->beginTransaction();
    }

    public static function commit()
    {
        self::$PDO->commit();
        self::$PDO = null;
    }

    public static function delete(string $query, $params = [])
    {
        self::connect();
        $statement = self::$PDO->prepare($query);
        $statement->execute($params);
    }

    public static function insert(string $query, $params = []): int
    {
        self::connect();
        $statement = self::$PDO->prepare($query);
        $statement->execute($params);
        return self::$PDO->lastInsertId();
    }

    public static function insertAssoc(string $table, $params = []): int
    {
        self::connect();
        $fields = array_keys($params);
        $values = array_values($params);
        $fieldList = implode(',', $fields);
        $qs = str_repeat("?,", count($fields) - 1);

        $sql = "INSERT INTO $table ($fieldList) values($qs?)";
        $statement = self::$PDO->prepare($sql);
        $statement->execute($values);

        return self::$PDO->lastInsertId();
    }

    public static function select(string $query, $params = [], $mode = PDO::FETCH_ASSOC): ?array
    {
        self::connect();
        $statement = self::$PDO->prepare($query);
        $statement->execute($params);
        return $statement->fetchAll($mode) ?: [];
    }

    public static function single(string $query, $params = [], $mode = PDO::FETCH_ASSOC): ?array
    {
        self::connect();
        $statement = self::$PDO->prepare($query);
        $statement->execute($params);
        return $statement->fetch($mode) ?: [];
    }

    public static function statement(string $query, $params = [])
    {
        self::connect();
        $statement = self::$PDO->prepare($query);
        $statement->execute($params);
    }


    public static function update(string $query, $params = [])
    {
        self::connect();
        $statement = self::$PDO->prepare($query);
        $statement->execute($params);
    }

    public static function updateAssoc(string $table, array &$params, array $conditions)
    {
        self::connect();
        $qs = "";
        $keys = array_keys($params);

        $i = 0;
        foreach ($keys as $key => $value) {
            if (++$i !== count($keys)) {
                $qs .= $value . " = :" . $value . ", ";
            } else {
                $qs .= $value . " = :" . $value;
            }
        }

        $cs = "";

        if (count($conditions) == count($conditions, COUNT_RECURSIVE)) {
            $cs = $conditions['key'] . " " . $conditions['operator'] . " :" . $conditions['key'];
            $params[$conditions['key']] = $conditions['value'];
        } else {
            foreach ($conditions as $condition) {
                $cs .= $condition['key'] . " " . $condition['operator'] . " :" . $condition['key'] . " " . $condition['condition'] . " ";
                $params[$condition['key']] = $condition['value'];
            }
        }

        $sql = "UPDATE $table SET $qs WHERE $cs";

        $statement = self::$PDO->prepare($sql);
        $statement->execute($params);
    }
}
