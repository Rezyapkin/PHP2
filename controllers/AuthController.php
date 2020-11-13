<?php

namespace app\controllers;

use app\model\entities\User;

class AuthController extends Controller
{

    public function actionLogin($params) {
        if (isset($params['login']) && isset($params['password'])) {
            \Auth::auth($params['login'], $params['password'], $params['save']);    
            if (!\Auth::isAuth()) {
                $params['message'] = 'Не верная пара логин/пароль!'; 
            } else {
                header('Location: /');
                Die();
            }
        }
        echo $this->render('auth', $params);
    }

    public function actionRegister($params) {
        $message = "";
        $header = "";

        if (empty($params['login']) || empty($params['name']) || empty($params['password'])) {
            $message = "Не заполнены все поля необходимые для регистрации пользователя!";
        } elseif (\Auth::isLoginExist($params['login'])) {
            $message = "Пользователь с таким логином уже зарегистирован!";
        } else {
            $user = new User($params['login'], $params['password'], $params['name']);
            \Users::save($user);
            if ($user->id) {
                $header = "Пользователь {$params['login']} успешно зарегистирован. Вы можете авторизоваться на сайте.";
            } else {
                $message = "Произошла ошибка. Пользователь не был зарегистрирован. Повторите попытку!";
            }
        }
        echo $this->render('auth', [
            'header' => $header,
            'message' => $message
        ]);
    }


}