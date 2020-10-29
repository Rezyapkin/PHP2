<?php

namespace app\model;

class Users extends Model
{
    public $id;
    public $login;
    public $pass;
    protected $db;


    public function getTableName() {
        return "users";
    }
}