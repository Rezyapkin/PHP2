<?php

namespace app\model;

use app\interfaces\IModel;
use app\engine\Db;

abstract class Model implements IModel
{
    use Db;
    
    public function __construct()
    {
    }

    public function first($id)
    {
        $tableName = static::getTableName();
        $sql = "SELECT * FROM {$tableName} WHERE id = {$id}";
        return self::queryOne($sql);
    }

    public static function get()
    {
        $tableName = static::getTableName();
        $sql = "SELECT * FROM {$tableName}";
        return self::queryAll($sql);
    }

    abstract public static function getTableName();
}