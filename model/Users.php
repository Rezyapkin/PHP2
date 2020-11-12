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

    public function getTableName() {
        return "users";
    }

    public function __set($name, $value) {
        if ($name == 'login') {
            $value = strtolower($value);
        }

        parent::__set($name, $value);
    }

    protected function update() {
        if ($this->props['login'] === True && isLoginExist($this->login)) {
            throw new \Exception("Пользователь с таким логином существует."); 
        } else {
            parent::update();
        }
    }

    protected function insert() {
        if ($this->isLoginExist($this->login)) {
            throw new \Exception("Пользователь с таким логином существует."); 
        } else {
            parent::insert();
        }
    }

    protected function isLoginExist($login) {
        $result = $this->newQuery()->where('login', strtolower($login))->first();
        return ($result && $result->login == $login);    
    }
    
}
