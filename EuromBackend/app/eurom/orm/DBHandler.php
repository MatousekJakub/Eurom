<?php

namespace Eurom\Orm;

use PDO;
use PDOException;


class DBHandler
{
    /**
     * @var PDO
     */
    private static $db = null;
    public static function create()
    {
        if (self::$db === null) {
            try {
                self::$db = new PDO('mysql:host=' . DB_HOSTNAME . ';dbname=' . DB_NAME . ';charset=utf8', DB_USER, DB_PASS);
                self::$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                throw $e;
            }
        }
        return self::$db;
    }
}
