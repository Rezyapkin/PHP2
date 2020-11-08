<?php

namespace app\model;

use app\interfaces\IDbModel;
use app\engine\Db;
use app\engine\QueryBuilder;


abstract class DBModel extends Model implements IDbModel
{
    
    protected $keyFieldName = 'id';

    protected $query = null;


    // Через это свойство реализуем связь один-к-одному с другими моделями
    //  ['model' => ['fieldName', 'className', 'instance']] 
    protected $realatedModels = [];

    protected function clearInstanceInRM($fieldName) {
        foreach ($realatedModels as $key => $value) {
            if ($value['fieldName'] == $fieldName) {
                unset($value['instance']); 
                break;
            }
        }
    }

    public function __set($name, $value) {
        //Если изменили значение поля, котрое связано с другой таблицей, очистим Instance
        $this->clearInstanceInRM($name);

        return parent::__set($name, $value);
    }

    public function newQuery() {
        if (!isset($this->query)) {
            $this->query = new QueryBuilder($this);
        }
        return $this->query;
    }

    public function getKeyFieldName() {
        return $this->keyFieldName;
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

        return parent::__get($name);
        
    }

    public function __call($method, $parameters)
    {
        if (method_exists($this, $method)) {
            return call_user_func_array([$this, $method], $parameters);
        } else {
            return call_user_func_array([$this->newQuery(), $method], $parameters);
        }    
    }

    public function __isset($name)
    {
        return (array_key_exists($name, $this->realatedModels)) ?: $this->isProperties($name);     
    }

    public function insert() {

        $params = [];

        foreach ($this->props as $key=>$value) {
            $params["{$key}"] = $this->$key;
        }

        $columns = "`" . implode("`, `", array_keys($params)) . "`";
        $values = ":" . implode(", :", array_keys($params));

        $sql = "INSERT INTO {$this->getTableName()} ({$columns}) VALUES ({$values})";

        Db::getInstance()->execute($sql, $params);
        $id_name = $this->keyFieldName;
        $this->$id_name = Db::getInstance()->lastInsertId();

        return $this;
    }

    public function update() {
        $id_name = $this->keyFieldName;
        $sets = [];
        $params = [];

        foreach ($this->props as $key=>$value) {
            if ($value) {
                $params["{$key}"] = $this->$key;
                $sets[] = "`{$key}` = :{$key}";
            }    
        }

        $id = $this->$id_name;

        if (!isset($id) || count($sets) == 0) {
            return $this;
        }

        $set_str = implode(", ", $sets);

        $sql = "UPDATE {$this->getTableName()} SET {$set_str} WHERE {$id_name} = '{$id}'";

        if (Db::getInstance()->execute($sql, $params)) {
            $this->clearProps();
        }
        return $this;
    }


    public function save() {
        if (is_null($this->id))
            $this->insert();
        else
            $this->update();
    }

    public function delete() {
        $sql = "DELETE FROM {$this->getTableName()} WHERE id = :id";
        return Db::getInstance()->execute($sql, ['id' => $this->id])->rowCount();
    }

    public function isProperties($name) {
        if ($name == $this->keyFieldName) {
            return True;
        }
        return parent::isProperties($name);
   }    

    public function getFields() {
        return array_merge([$this->keyFieldName], array_keys($this->props));
    }   

    public function getDataFields() {
        $result = [];
        foreach ($this->getFields() as $field) {
            $result[$field] = $this->$field;
        }
        return $result;
    }

    abstract public function getTableName();
}