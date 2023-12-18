<?php

namespace Eurom\Orm;

use Exception;
use PDOException;
use ReflectionClass;

class BaseFactory
{
    static function getAll(): array
    {
        $out = [];
        $db = DBHandler::create();
        $statement = $db->prepare("SELECT * FROM `" . self::getTableName() . "`");
        $statement->execute();
        $entries = $statement->fetchAll();
        foreach ($entries as $entry) {
            $out[] = new (self::getClassNameSpace())($entry);
        }
        return $out;
    }
    static function getAllWhere(string | array $target, $value = null): array
    {
        $out = [];
        $db = DBHandler::create();
        if (gettype($target) === "string") {
            $statement = $db->prepare("SELECT * FROM `" . self::getTableName() . "` WHERE " . $target . " = ?");
            $statement->execute([$value]);
        } elseif (gettype($target) === "array") {
            $queryString = "";
            $values = [];
            foreach ($target as $column => $value) {
                if ($value === null) {
                    $queryString .= $column . " IS NULL AND ";
                    continue;
                }
                $queryString .= $column . " = ? AND ";
                $values[] = $value;
            }
            $queryString = substr($queryString, 0, -5);
            $statement = $db->prepare("SELECT * FROM `" . self::getTableName() . "` WHERE " . $queryString);
            $statement->execute($values);
        } else throw new Exception("Invalid argument type");
        $entries = $statement->fetchAll();
        foreach ($entries as $entry) {
            $out[] = new (self::getClassNameSpace())($entry);
        }
        return $out;
    }
    static function create($body): bool | int
    {
        $rfl = new ReflectionClass(self::getClassNameSpace());
        $props = $rfl->getProperties();
        $columns = "";
        $qMarks = "";
        $params = [];
        foreach ($props as $prop) {
            if ($prop->name === "id") continue;
            $type = $prop->getType()->getName();
            $questionMark = substr($prop->getType(), 0, 1);
            if (!isset($body[$prop->getName()]) && $prop->getDefaultValue() === null && $questionMark !== "?") return false;
            $propName = $prop->getName();
            if (!isset($body[$propName])) continue;
            $propValue = $body[$propName];
            $params[] = $type !== "bool" ? $propValue : ($propValue ? "1" : "0");
            $columns .= "," . $propName;
            $qMarks .= ",?";
        }
        $columns = substr($columns, 1);
        $qMarks = substr($qMarks, 1);
        $db = DBHandler::create();
        try {
            $statement = $db->prepare("INSERT INTO `" . self::getTableName() . "` (" . $columns . ") VALUES (" . $qMarks . ")");
            $statement->execute($params);
        } catch (PDOException $e) {
            var_dump($e);
            return false;
        }
        return $db->lastInsertId();
    }
    static function deleteWhere(string $column, $value): bool
    {
        $db = DBHandler::create();
        try {
            $statement = $db->prepare("DELETE FROM `" . self::getTableName() . "` WHERE " . $column . " = ?");
            $statement->execute([$value]);
        } catch (PDOException) {
            return false;
        }
        return true;
    }
    static function getById(int $id): DBObject | false
    {
        //TODO: refactor (if exists)
        $db = DBHandler::create();
        $statement = $db->prepare("SELECT * FROM `" . self::getTableName() . "` WHERE id = ?");
        $statement->execute([$id]);
        $body = $statement->fetch();
        $out = false;
        if (self::exists($id))
            $out = new (self::getClassNameSpace())($body);
        return $out;
    }
    static function exists(int $id): bool
    {
        $db = DBHandler::create();
        $statement = $db->prepare("SELECT COUNT(id) FROM `" . self::getTableName() . "` WHERE id = ?");
        $statement->execute([$id]);
        $body = $statement->fetch();
        return $body["COUNT(id)"] ? true : false;
    }
    static function getMax(string $column): float | int
    {
        $db = DBHandler::create();
        $statement = $db->query("SELECT MAX(" . $column . ") FROM `" . self::getTableName() . "`");
        $statement->execute();
        $out = $statement->fetch()["MAX(orderId)"];
        if ($out === null)
            return 0;
        return $out;
    }
    static function getMaxWhere(string $maxColumn, string $columnTarget, $targetValue): float | int
    {
        $db = DBHandler::create();
        $statement = $db->prepare("SELECT MAX(" . $maxColumn . ") FROM `" . self::getTableName() . "` WHERE `" . $columnTarget . "` = ?");
        $statement->execute([$targetValue]);
        $out = $statement->fetch()["MAX(orderId)"];
        if ($out === null)
            return 0;
        return $out;
    }
    private static function getTableName()
    {
        $tableName = strrev(implode(strrev(""), explode(strrev("Factory"), strrev(lcfirst(explode("\\", get_called_class())[1])), 2)));
        return $tableName;
    }
    private static function getClassNameSpace()
    {
        $tableName = explode("\\", get_called_class())[0] . "\\" . strrev(implode(strrev(""), explode(strrev("Factory"), strrev(explode("\\", get_called_class())[1]), 2)));
        return $tableName;
    }
}
