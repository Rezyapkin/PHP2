<?php
//TODO сделать все пути абсолютными
include "../config/config.php";

use app\model\{Products, Users};
use app\engine\Autoload;
include "../engine/Autoload.php";



spl_autoload_register([new Autoload(), 'loadClass']);


$controllerName = $_GET['c'] ?: 'product';
$actionName = $_GET['a'];

$controllerClass = CONTROLLER_NAMESPACE . ucfirst($controllerName) . "Controller";

if (class_exists($controllerClass)) {
    $controller = new $controllerClass();
    $controller->runAction($actionName);
} else {
    die("Ошибка, контроллер не существует.");
}


//var_dump(Products::where('price',"<",50000)->orderBy('price DESC')->get());

die();

//$product = new Products();
//$users = new Users();

//CRUD

/** @var Products $product */

//UPDATE
$product = Products::first(2);
$product->name = "Чай 2";
$product->save();
var_dump($product);
die();
//READ
$user = new Users("user");
$user->save();
var_dump($user);


//CREATE
$prodNew = new Products('Чай','Цейлонский', 23);
$prodNew->save();
//$users->insert();
//var_dump(get_class_methods($users));
var_dump($prodNew);
die();
//DELETE
$product = $product->first(1);
$product->delete();



$product = $product->first(1);

var_dump($product->get());

