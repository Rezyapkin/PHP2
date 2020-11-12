<?php

namespace app\engine;

class Container
{
    protected $bindings = [];

    protected $singletons = [];

    protected $buildStack = [];

    public function bind($abstract, $concrete = null, $singleton = false) {
        if (is_null($concrete)) {
            $concrete = $abstract;
        }
        $this->bindings[$abstract] = $concrete;
        if ($singleton) {
            $this->singletons[$concrete] = null; 
        }
    }

    public function singleton($abstract, $concrete = null) {
        $this->bind($abstract, $concrete, true);
    }


    protected function getConcrete($abstract) {
        return ($this->bindings[$abstract]) ?: $abstract;
    }

    public function make($abstract) {
        $this->buildStack = [];
        $concrete = $this->getConcrete($abstract);
        $result = $this->build($concrete);
        
        if (isset($result)){
            $this->setSingleton($concrete, $result);
        } 

        return $result;
    }

    

    public function build($concrete) {

        if ($concrete == get_class($this)) {
            return $this;
        }
     
        if (isset($this->singletons[$concrete])) {
            array_pop($this->buildStack);
            return $this->singletons[$concrete];
        }

        try {
            $reflector = new \ReflectionClass($concrete);
        } catch (\Exception $e) {
            throw new \Exception("Ошибка, класс {$concrete} не найден.");
        }

        if (!$reflector->isInstantiable()) {
            throw new \Exception("Ошибка, не возможно создать класс {$concrete}.");
        }

        $constructor = $reflector->getConstructor();

        if (is_null($constructor)) {
            array_pop($this->buildStack);
            return new $concrete;
        }

        $dependencies = $constructor->getParameters();

        try {
            $instances = $this->resolveDependencies($dependencies);
            $instance = $reflector->newInstanceArgs($instances);
        } catch (\Exception $e) {
            array_pop($this->buildStack);
            throw $e;
        }

        array_pop($this->buildStack);
        return $instance;
    }

    protected function setSingleton($className, $instance) {
        if (array_key_exists($className, $this->singletons)) {
            $this->singletons[$className] = $instance;   
        }

    }

    protected function resolveDependencies($dependencies)
    {
        if (count($this->buildStack) > 10) {
            throw new \Exception("Ошибка, переполнен стек bindings экземпляра класса App.");
        }
        $instances = [];
        try {
            foreach ($dependencies as $param) {
                $type = $param->getType()->getName();
                $this->buildStack[] = $type;
                $concrete = $this->getConcrete($type);
                if (class_exists($concrete)) {
                    $instances[$param->name] = $this->build($concrete);
                    $this->setSingleton($concrete, $instances[$param->name]);
                }    
            }
        } catch (\Exception $e) {
            throw $e;
        }

        return $instances;
    }

}