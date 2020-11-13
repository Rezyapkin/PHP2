<?php

namespace app\model;

use app\engine\Db;

abstract class Model
{
    protected $keyFieldName = 'id';    
    protected $props = [];

    // Через это свойство реализуем связь один-к-одному с другими моделями
    //  ['model' => ['fieldName', 'className', 'instance']] 
    protected $realatedModels = [];

    public function __set($name, $value) {
        if (array_key_exists($name, $this->props)) {
            $this->clearInstanceInRM($name);
            $this->props[$name] = true;
            $this->$name = $value;
        }    
    }

    public function __get($name)
    {
        //Код для работы со связанными таблицами, например в модели CartItem мы сможем обращатться к продукту как product.name
        if (array_key_exists($name, $this->realatedModels) && $this->realatedModels[$name]['fieldName']) {
            if (!isset($this->realatedModels[$name]['instance'])) {
                $className = ($this->realatedModels[$name]['className']) ?: $name;
                if (!strpos($className,'\\')) {
                    $className = MODEL_NAMESPACE . $className;
                }
                if (class_exists($className)) {
                    $this->realatedModels[$name]['instance'] = $className::find($this->realatedModels[$name]['fieldName']);
                }
            }
        
            return $this->realatedModels[$name]['instance'];
        };

        if ($this->isProperties($name)) {
            return $this->$name;
        }              
    }

    public function setKeyValue($value) {
        if (empty($this->getKeyValue())) {
            $id = $this->getKeyFieldName();
            $this->$id = $value;
        }
    }

    public function __isset($name)
    {
        return $this->isProperties($name);     
    }

    protected function clearInstanceInRM($fieldName) {
        foreach ($this->realatedModels as $key => $value) {
            if ($value['fieldName'] == $fieldName) {
                unset($value['instance']); 
                break;
            }
        }
    }

    public function clearProps() {
        foreach ($this->props as $key=>$value) {
            $this->props[$key] = false;
        }
    }
 
   public function getDataFields() {
       $result = [];
       foreach ($this->getFields() as $field) {
           $result[$field] = $this->$field;
        }
        return $result;
    }

    public function getFields() {
        return array_merge([$this->keyFieldName], array_keys($this->props));
    }   

    public function getKeyFieldName() {
        return $this->keyFieldName;
    }

    public function getKeyValue() {
        $id = $this->getKeyFieldName();
        return $this->$id;
    }    

    public function isProperties($name) {
        return ($name == $this->keyFieldName || key_exists($name, $this->props)); 
   }  

}