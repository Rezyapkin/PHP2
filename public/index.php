<?php
//TODO сделать все пути абсолютными
include "../config/config.php";

use app\model\{Products, Users};
use app\engine\Autoload;
include "../engine/Autoload.php";



spl_autoload_register([new Autoload(), 'loadClass']);

//READ
$product = new Products();
$product->first(5);
echo $product;

//UPDATE
$product->name = 'Ноутбук Apple MacBook Pro 13 i5 1,4/8Gb/512SSD Sil new';
echo $product;
$product->update();
echo $product;
echo 'Обвноили!!! <br>';

//Вернем старые значения
$product->name = 'Ноутбук Apple MacBook Pro 13 i5 1,4/8Gb/512SSD Sil';
$product->update();

//CREATE
$prodNew = new Products('Чай','Цейлонский', 23);
$prodNew->insert();
echo $prodNew . ' - создан новый товар в БД!';

echo '<hr>Новый список товаров: <br>';
var_dump($product->get());
echo '<hr>';

//DELETE
$prodNew->delete();
echo 'Новый товар удален!<br>';

echo '<hr>Новый список товаров: <br>';
var_dump($product->get());
echo '<hr>';

