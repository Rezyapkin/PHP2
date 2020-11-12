<?php
include "../config/config.php";

use app\engine\{AutoLoad, Application};
include ROOT_DIR . "/engine/Autoload.php";
require_once '../vendor/autoload.php';
spl_autoload_register([new Autoload(), 'loadClass']);


//Реализовал жалкое подобие контейнера и фасадов. Читал про контейнер Laravel. Упростил их container. Для нашего движка за глаза. Если потребуется больше, можно допилить. 
$app = new Application();
//Правильно ли я прописал приложение через статику для всех фасадов?
Facade::setFacadeApplication($app);

//А куда бы мне эту конфигурацию вынести?
App::bind('auth', '\\app\\engine\\Auth', true);
App::bind('render', '\\app\\engine\\Render', true);
App::bind('router', '\\app\\engine\\Router', true);
App::bind('request', '\\app\\engine\\Request', true);
App::bind('session', '\\app\\engine\\Session', true);
App::bind(\app\interfaces\IRenderer::class, '\\app\\engine\\TwigRender');  //Тут теперь легко поменять рендер на Twig

//А где правильно маршрутизацию подключать?
include ROOT_DIR . "/routes/map.php";

App::start(); 





