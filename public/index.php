<?php

use app\model\{Products, Users};
use app\engine\{Autoload};
include "../config/config.php";
include "../engine/Autoload.php";

spl_autoload_register([new Autoload(), 'loadClass']);

/* 
Я на уроке писал, что мне кажется не логичным получение списка продуктов вызовом метода экземпляра класса. 
Экзепляр класса - это конкретный товар, у это товара получается есть метод "Дай мне все товары". Не очень бьется с объектной моделью.
Поэтому по мне логичнее сделать статический метод get, который будет вызываться для класса. Но тогда нужно менять механику работы с Db.
Я решил Db сделать трейтом. У нас по сути будет всегда один экземпляр класса Db. Трейт, я как понял из методички, именно для этого и нужен.
*/

$product = new Products();
$user = new Users();

echo $product->first(5);
echo Users::get();

var_dump($product);