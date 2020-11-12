<?php

namespace app\engine;

use app\interfaces\IRenderer;

class Router
{
    const METHODS = ['GET', 'POST', 'PUT', 'DELETE'];

    public $routes = [];
    protected $request;
    protected $renderer;

    public function __construct(Request $request, IRenderer $renderer) {
        $this->request = $request;
        $this->renderer = $renderer;
    }

    public function __call($method, $parameters) {
        if (method_exists($this, $method)) {
            return call_user_func_array([$this, $method], $parameters);
        }   
        $up_method = strtoupper($method);
        if (array_search($up_method, static::METHODS) !== false) {
            return $this->match([$up_method], $parameters[0], $parameters[1]);
        }
    }
    
    protected function checkUriWithRegEx($regEx) {
        preg_match($regEx, $this->request->requestString, $matches);
        return count($matches) > 0;
    }

    protected function getParamsForControllerAction($route) {
        $params = [];
        preg_match($route['regEx'], $this->request->requestString, $matches, PREG_OFFSET_CAPTURE);
        for ($i = 1; $i < count($matches); $i++) {
            $params[$route['paramsName'][$i - 1]] = $matches[$i][0];
        }
        return $params;
    }

    function getDefaultControllerAndAction() {
        return [
            'name' => CONTROLLER_NAMESPACE . 'Controller',
            'method' => 'errorAction'
        ];        
    }

    protected function checkLogout($params) {
        if (key_exists('logout', $params)) {
            \Auth::logout();
        }
    }

    protected function action() {
        $default = $this->getDefaultControllerAndAction();
        $controllerName = $default['name'];
        $method = $default['method'];
        $params = $this->request->params;

        foreach ($this->routes as $route) {
            if ($route['method'] !== $this->request->method)  {
                continue;
            }

            if ($this->checkUriWithRegEx($route['regEx'])) {
                $controllerName = $route['controllerName'];
                $method = $route['controllerMethod'];
                $params = array_merge($params, $this->getParamsForControllerAction($route));
                break;
            }
        }

        $this->checkLogout($params);
        $controller = new $controllerName($this->renderer);
        $controller->$method($params);

    }

    protected function getRexExAndParams($uri) {
        $regEx = "/^" . str_replace(['/'],['\/'], $uri) . "$/";
        $paramsName = [];
        preg_match_all("/{([A-Za-z]+[A-Za-z0-9]*)}/", $regEx, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $paramsName[] = $match[1];
        }
        $regEx = preg_replace("/{[A-Za-z]+[A-Za-z0-9]*}/", "([A-Za-z0-9]+)", $regEx);
        return compact("regEx", "paramsName");
    } 

    protected function match($methods, $uri, $controllerAndMethod, $renderer = '') {
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

            $up_method = strtoupper($method);
            if (array_search($up_method, static::METHODS) !== false) {
                $result = $this->getRexExAndParams($uri);
                $this->routes[] = [
                    'method' => $up_method,
                    'regEx' => $result['regEx'],
                    'controllerName' => $controllerClass,
                    'controllerMethod' => $action,
                    'paramsName' => $result['paramsName'],
                ];
            }
        }

    }

}