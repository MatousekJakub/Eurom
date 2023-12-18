<?php

namespace Eurom\Orm;

use Exception;
use PDOException;
use ReflectionClass;
use ReflectionProperty;

enum DBObjectProperties: string
{
    case id = "id";
}
class DBObject
{
    public int $id;

    function __construct($body)
    {
        $rfl = new ReflectionClass($this);
        $props = $rfl->getProperties();
        foreach ($props as $prop) {
            $type = substr($prop->getType(), 0, 1);
            if (!isset($body[$prop->getName()]) && $type !== "?")
                throw new Exception("Missing property " . $prop);
        }
        foreach ($body as $key => $value) {
            if (property_exists($this, $key))
                $this->$key = $value;
        }
    }

    function setAll($args): bool
    {
        $columns = "";
        $values = [];
        $valuesToUpdate = [];
        foreach ($args as $column => $value) {
            if ($column === "id" || !property_exists($this, $column)) continue;
            $rfl = new ReflectionProperty($this, $column);
            $type = $rfl->getType()->getName();
            $values[] = $type !== "bool" ? $value : ($value ? "1" : "0");
            $columns .= $column . " = ?, ";
            $valuesToUpdate[$column] = $value;
        }
        $columns = substr($columns, 0, -2);
        $db = DBHandler::create();
        $statement = $db->prepare("UPDATE `" . $this->getTableName() . "` SET " . $columns . " WHERE id = ?");
        try {
            $statement->execute([...$values, $this->id]);
        } catch (PDOException) {
            return false;
        }
        foreach ($valuesToUpdate as $key => $value) {
            $this->$key = $value;
        }
        return true;
    }
    function set(string $key, $value): bool
    {
        if ($key === "id") return true;
        if (!property_exists($this, $key))
            return false;
        $rfl = new ReflectionProperty($this, $key);
        $type = $rfl->getType()->getName();
        $value = $type !== "bool" ? $value : ($value ? "1" : "0");
        $db = DBHandler::create();
        $statement = $db->prepare("UPDATE `" . $this->getTableName() . "` SET " . $key . " = ? WHERE id = ?");
        try {
            $statement->execute([$value, $this->id]);
            $this->$key = $value;
        } catch (PDOException) {
            return false;
        }
        return true;
    }
    function delete(): bool
    {
        $db = DBHandler::create();
        $statement = $db->prepare("DELETE FROM `" . $this->getTableName() . "` WHERE id = ?");
        try {
            $statement->execute([$this->id]);
        } catch (PDOException $e) {
            return false;
        }
        return true;
    }
    private function getTableName()
    {
        $tableName = explode("\\", $this::class);
        $tableName = end($tableName);
        return lcfirst($tableName);
    }
}
