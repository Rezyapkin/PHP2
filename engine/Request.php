<?php

namespace app\engine;

class Request {

    protected $requestString;
    protected $method;
    protected $params = [];

    function __construct() {
        $this->requestString = explode('?',$_SERVER['REQUEST_URI'])[0];
        $this->method = $_SERVER['REQUEST_METHOD']; 
        $this->fillParams();

    }

    function fillParams() {
        $this->params = $_REQUEST;

        $data = json_decode(file_get_contents('php://input'));
        if (!is_null($data)) {
            foreach ($data as $key => $value) {
                $this->params[$key] = $value;
            }
        }

        $this->params['back_url'] = $_SERVER['HTTP_REFERER'];

    }

    function __get($name) {
        if (isset($this->$name)) {
            return $this->$name;
        }
        throw new \Exception("Ошибка, свойства {$name} не существует.");
    }
    
}
