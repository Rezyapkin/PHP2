<?php

namespace app\model;

use app\engine\Db;

abstract class Model
{
    protected $keyFieldName = 'id';
    protected $props = [];

    protected function isProperties($name) {
         return $name == $this->keyFieldName || array_key_exists($name, $this->props);
    }

    protected function clearProps() {
        foreach ($this->props as $key=>$value) {
            $this->props[$key] = false;
        }
    }
    
    public static function __callStatic($method, $parameters)
    {
        $instance = new static;
     
        return call_user_func_array([$instance, $method], $parameters);
    }

    public function __call($method, $parameters)
    {
        return call_user_func_array([$this, $method], $parameters);
    }

    public function __set($name, $value) {
        if (array_key_exists($name, $this->props)) {
            $this->props[$name] = true;
            $this->$name = $value;
        }    
    }

    public function __get($name)
    {
        if ($this->isProperties($name)) {
            return $this->$name;
        }              
    }

    public function __isset($name)
    {
        return $this->isProperties($name);     
    }


}