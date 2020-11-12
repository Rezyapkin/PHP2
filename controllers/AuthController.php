<?php

namespace app\controllers;

use app\model\Users;

class AuthController extends Controller
{

    public function actionLogin($params) {
        if (isset($params['login']) && isset($params['current-password'])) {
            \Auth::auth($params['login'], $params['current-password'], $params['save']);    
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
        header("Location: ");
        if (empty($params['login']) || empty($params['name']) || empty($params['current-password'])) {
            $params['message2'] = "Не заполнены все поля необходимые для регистрации пользователя!";
        } elseif (\Auth::isLoginExist($params['login'])) {
            $params['message2'] = "Пользователь с таким логином уже зарегистирован!";
        } else {
            $user = new Users($params['login'], $params['current-password'], $params['name']);
            $user->save();
            if ($user->id) {
                $params['header'] = "Пользователь {$params['login']} успешно зарегистирован. Вы можете авторизоваться на сайте.";
            } else {
                $params['message2'] = "Произошла ошибка. Пользователь не был зарегистрирован. Повторите попытку!";
            }
        }
        echo $this->render('auth', $params);
    }


}