<?php

namespace app\model;

class Users extends Model
{
    const TABLE_NAME = "users";

    public $id;
    public $login;
    public $pass;
    protected $db;


    public static function getTableName() {
        return "users";
    }
}