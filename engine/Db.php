<?php

namespace app\engine;

trait Db
{
    protected static function queryOne($sql) {
        //выполняем $sql
        return $sql . "<br>";
    }

    protected static function queryAll($sql) {
        return $sql . "<br>";
    }

}