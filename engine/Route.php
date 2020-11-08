<?php

namespace app\engine;

use app\traits\Tsingletone;

class Route
{
    use TSingletone;

    const METHODS = ['GET', 'POST', 'PUT', 'DELETE'];

    protected $routes=[];

    public function __call($method, $parameters) {
        if (method_exists($this, $method)) {
            return call_user_func_array([$this, $method], $parameters);
        }   
        $up_method = strtoupper($method);
        if (array_search($up_method, static::METHODS) !== false) {
            return $this->mutch([$up_method], $parameters[0], $parameters[1]);
        }
    }



    public static function __callStatic($method, $parameters)
    {
        
        if (!isset(static::$instance)) {
             static::$instance = new static;
        }
     
        return call_user_func_array([static::$instance, $method], $parameters);
    }

    protected function action() {
        $controllerName = CONTROLLER_NAMESPACE . 'Controller';
        $method = 'errorAction';
        $params = [];
        $rendererName = ENGINE_NAMESPACE . DEFAULT_RENDERER;

        $uri = explode('?',$_SERVER['REQUEST_URI'])[0];

        foreach ($this->routes as $route) {
            if ($route['method'] !== $_SERVER['REQUEST_METHOD'])  {
                continue;
            }

            preg_match($route['uri'], $uri, $matches, PREG_OFFSET_CAPTURE);
            if (count($matches) > 0) {
                for($i=1; $i<count($matches); $i++) {
                    $params[] = $matches[$i][0];
                }
                
                if (count($params) == 0) {
                    $inputJSON = file_get_contents('php://input');
                    try {
                        $input = json_decode( $inputJSON, TRUE );
                        $params = $input;
                    } catch (Exception $e) {

                    }
                }    

                $controllerName = $route['controller'];
                $method = $route['controller_method'];

                break;
            }
        }

        //Костыль - просто сделаю пока только для главной Twig
        if ($controllerName != 'app\controllers\SimplePageController') {
            $rendererName = ENGINE_NAMESPACE . 'Render';
        }
        $controller = new $controllerName(new $rendererName());
        echo $controller->$method($params);

    }

    protected function mutch($methods, $uri, $controllerAndMethod, $renderer = '') {
        foreach ($methods as $method) {
            $ar = explode('.', $controllerAndMethod);
            if (count($ar) != 2) {
                throw new \Exception('Верный формат последнего параметра: Controller.Method');
            }

            $controllerClass = CONTROLLER_NAMESPACE . ucfirst($ar[0]) . "Controller";
            if (!class_exists($controllerClass)) {
                throw new \Exception("Ошибка, контроллер {$controllerClass} не существует.");
            }

            $action = 'action'. ucfirst($ar[1]);
            if (!method_exists($controllerClass, $action)) {
                throw new \Exception("Ошибка, метод {$ar[1]} в контроллере {$controllerClass} не существует.");
            }

            //preg_match("/([a-z]+)\([\'\`]?([\*a-z]+)[\'\`]?\)/i", $field, $matches, PREG_OFFSET_CAPTURE);
            $regEx = "/^" . str_replace(['/'],['\/'], $uri) . "$/";
            $regEx = preg_replace('/{[A-Za-z]+[A-Za-z0-9]*}/', '([A-Za-z0-9]+)', $regEx);
            $up_method = strtoupper($method);
            if (array_search($up_method, static::METHODS) !== false) {
                $this->routes[] = [
                    'method' => $up_method,
                    'uri' => $regEx,
                    'controller' => $controllerClass,
                    'controller_method' => $action,
                ];
            }
        }

    }

}