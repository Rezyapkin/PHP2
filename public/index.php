<?php

use app\model\{Products, Users};
use app\engine\{Autoload, Db};
include "../config/config.php";
include "../engine/Autoload.php";

spl_autoload_register([new Autoload(), 'loadClass']);


$product = new Products(new Db());
$user = new Users(new Db());

echo $product->first(5);
echo $user->get();

var_dump($product);