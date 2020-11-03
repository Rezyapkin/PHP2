<?php

namespace app\model;

use app\engine\Db;

abstract class Model
{

    public function __set($name, $value) {
        if (array_key_exists($name, $this->props)) {
            $this->props[$name] = true;
            $this->$name = $value;
        }    
    }

    public function __get($name)
    {
        if (array_key_exists($name, $this->props)) {
            return $this->$name;
        }              
    }


}