<?php

namespace app\controllers;

class SimplePageController extends Controller
{

    public function actionIndex() {

        echo $this->render('index', ['twig' => ', это Twig']);
    }


}