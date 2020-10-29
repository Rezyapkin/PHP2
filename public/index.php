<?php

use app\model\{Products, Users};
use app\engine\{Autoload};
use app\model\task3 as task3;
include "../config/config.php";
include "../engine/Autoload.php";

spl_autoload_register([new Autoload(), 'loadClass']);

/* 
Я на уроке писал, что мне кажется не логичным получение списка продуктов вызовом метода экземпляра класса. 
Экзепляр класса - это конкретный товар, у это товара получается есть метод "Дай мне все товары". Не очень бьется с объектной моделью.
Поэтому по мне логичнее сделать статический метод get, который будет вызываться для класса. Но тогда нужно менять механику работы с Db.
Я решил Db сделать трейтом. У нас по сути будет всегда один экземпляр класса Db. Трейт, я как понял из методички, именно для этого и нужен.
Также смысл плодить для экземпляров GettableName. В пределах класса - это одно и тоже значение. По мне, логично, это static метод класса, а не экземпляра.
*/

$product = new Products();
$user = new Users();

echo $product->first(5);
echo Users::get();

var_dump($product);

/*
Про digital товар не понял суть сущности, но думаю что на двух разновидностях показал то, что разобрался немного!)))
Начал читать PHP7 Котерова и Симдянова, очень интересное "чтиво")))
*/

$product1 = new task3\QtyProducts('Ноутбук Asus',40000,4);
$product2 = new task3\UnitProducts('Ноутбук на вес',50000,2,'кг');
$product3 = new task3\UnitProducts('Ноутбук на вес',50000,100,'г');

echo $product1->getTemplate();
echo $product2->getTemplate();
echo $product3->getTemplate();
echo task3\Products::getTotalTemplate();
