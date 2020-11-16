<?php

namespace app\engine;

class Application extends Container
{

    public function __construct() {
        $this->bind('app', get_class($this), true);
    }

    public function start() {
        $auth = $this->make('auth'); 
        $session = $this->make('session');
        $cart = $this->make('cart');
        $session->start();   
        $cart->setSystemProp('session_id',$session->getId());
        $userInfo = $auth->getUserInfo();
        if (isset($userInfo)) {
            $cart->setSystemProp('user_id',$userInfo['userId']);
        };
        $this->make('router')->action();  
    }


}