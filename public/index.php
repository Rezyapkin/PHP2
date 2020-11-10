<?php
include "../config/config.php";

use app\engine\{AutoLoad, Application};
include ROOT_DIR . "/engine/Autoload.php";
require_once '../vendor/autoload.php';
spl_autoload_register([new Autoload(), 'loadClass']);


// просто увидел, что через файл map.php в Laravel реализовано, поэтому роутинг там
//include ROOT_DIR . "/routes/map.php";


//Реализовал жалкое подобие контейнера и фасадов. Читал про контейнер Laravel. Упростил их container. Для нашего движка за глаза. Если потребуется больше, можно допилить. 
$app = new Application();
Facade::setFacadeApplication($app);
App::bind('render', '\\app\\engine\\Render', true);
App::bind('router', '\\app\\engine\\Router', true);
App::bind('request', '\\app\\engine\\Request', true);
App::bind(\app\interfaces\IRenderer::class, '\\app\\engine\\Render');  //Тут теперь легко поменять рендер на Twig

include ROOT_DIR . "/routes/map.php";

Route::action();




