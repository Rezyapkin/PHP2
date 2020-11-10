<?php

abstract class Facade {
    
    protected static $app;

    public static function setFacadeApplication($app) {
        static::$app = $app;
    }

    public static function __callStatic($method, $parameters) {
        if (!isset(static::$app)) {
            return;
        }
        
        $instance = static::$app->make(static::getFacadeAccesor());
        if ($instance && is_callable(array($instance, $method))) {
            return call_user_func_array([$instance, $method], $parameters);
        }
         
    }

    abstract protected static function getFacadeAccesor();

}