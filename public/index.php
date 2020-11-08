<?php
include "../config/config.php";

use app\engine\Autoload;
use app\engine\Route;
include ROOT_DIR . "/engine/Autoload.php";
spl_autoload_register([new Autoload(), 'loadClass']);


// просто увидел, что через файл map.php в Laravel реализовано, поэтому роутинг там
include ROOT_DIR . "/routes/map.php";

Route::action();




