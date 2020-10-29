<?php

namespace app\engine;

class Db
{
    public function queryOne($sql) {
        //выполняем $sql
        return $sql . "<br>";
    }

    public function queryAll($sql) {
        return $sql . "<br>";
    }

}