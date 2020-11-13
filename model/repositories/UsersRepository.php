<?php

namespace app\model\repositories;

use app\model\Repository;
use app\model\entities\User;


class NewsRepository extends Repository
{
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
    
    public function getEntityClass()
    {
        return User::class;
    }

    public function getTableName()
    {
        return "users";
    }
}
