<?php

namespace app\model;

class Users extends DBModel
{
    public $id;
    public $login;
    public $name;
    public $password_hash;
    public $is_admin;

    protected $props = [
        'login' => false,
        'name' => false,
        'password_hash' => false,
        'is_admin' => false
    ];

    public function __construct($login = null, $password = null, $name = null)
    {
        $this->login = strtolower($login);
        $this->name = $name;
        $this->is_admin = false;
        $this->setPasswordHash($password);
    }

    public function setPasswordHash($pass) {
        return $this->password_hash = ($pass) ? password_hash($pass, PASSWORD_DEFAULT) : $pass; 
    }


    public function __set($name, $value) {
        if ($name == 'login') {
            $value = strtolower($value);
        }
        parent::__set($name, $value);
    }

    
}
