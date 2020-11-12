<?php

namespace app\engine;

class Application extends Container
{

    public function __construct() {
        $this->bind('app', get_class($this), true);
    }

    public function start() {
        $this->make('session')->start();   
        $this->make('router')->action();   
    }


}