<?php
include "../config/const.php";

use app\engine\{AutoLoad, Application};
include ROOT_DIR . "/engine/Autoload.php";
require_once '../vendor/autoload.php';
spl_autoload_register([new Autoload(), 'loadClass']);


$app = new Application();
//Правильно ли я прописал приложение через статику для всех фасадов?
Facade::setFacadeApplication($app);

include ROOT_DIR . "/config/binding.php";
include ROOT_DIR . "/routes/map.php";

App::start(); 





