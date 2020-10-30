<?php

namespace app\model;

use app\interfaces\IModel;
use app\engine\Db;


// Сделал Model на свой манер, которая подстраивается сама под ту таблицу с которой работает... 
abstract class Model implements IModel
{
    //Тут будем хранить измененные свойства, только те, что отличаются от dbProps
    public $changedProps = [];

    //Тут будем хранить прочитанные из базы данных свойства
    protected $dbProps = [];

    //Тут будем хранить доступные свойства для всех дочерних классов. 
    //!!! Можно было один статический массив определить, но тогда бы его пришлось переопределять в каждом дочернем классе !!!
    protected static $childProps = [];

    //Свойство в котором хранится имя поля с id-ником
    protected static $idName = 'id';

    //Вызывается один раз при попытке прочитать или записать свойства в любом экземпляре класса. Один раз в пределах одного дочернего класса!
    protected static function fillStructuredbProps() {
        $className = get_called_class();

        //Если массив свойств заполнен - больше не вызываем его
        if (isset(static::$childProps[$className])) {
            return;
        }

        static::$childProps[$className] = [];

        $sql = "SHOW COLUMNS FROM " . static::getTableName();
        $columns = Db::getInstance()->queryAll($sql);
        
        foreach ($columns as $column) {
            static::$childProps[$className][] = $column['Field'];
            //Это на тот случай, если нет документации о том, что id в таблице должен называться именно id. На уровне соглашений можно убрать. Просто тренировался)))
            if ($column['Key'] == 'PRI') {
                static::$idName = $column['Field'];
            }
        }
    } 

    public function getPropertiesList() {
        static::fillStructuredbProps();
        return static::$childProps[get_called_class()];
    }

    public function __get($property)
    {
        static::fillStructuredbProps();
        return (is_null($this->changedProps[$property])) ? $this->dbProps[$property] : $this->changedProps[$property];
    }
 
    public function __set($property, $value)
    {
        static::fillStructuredbProps();
        if ($property == static::$idName) {
            //Не даем изменить id
            throw new \Exception("Поле '" . static::$idName . "' нельзя изменить для данного экземляра класса '" . get_class($this) . "'");
        } elseif (!array_search($property, $this->getPropertiesList())) {
            //У связанной с классом таблицы нет такого поля.
            throw new \Exception("Свойство '{$property}' в классе '" . get_class($this) . "' отсутсвует.");
        }
        
        //У связанной с классом таблицы есть такое поле раз исключения не отработали.
        if ($this->dbProps[$property] === $value) {
            //Если новое значение равно тому, что содержится в базе данны, то удалим запись из таблицы changedProps
            unset($this->changedProps[$property]);
            $this->changedProps = array_values($this->changedProps); 
        } else {           
            $this->changedProps[$property] = $value;
        }
    }

    private function clearChangedProps() {
        $this->changedProps = [];
    }

    public function isChanged() {
        return count($this->changedProps) > 0;
    }

    public function first($id)
    {
        $sql = sprintf( "SELECT * FROM %s WHERE %s = :id",
            static::getTableName(),
            static::$idName);

        $result = Db::getInstance()->queryOne($sql, ['id' => $id]);
        if ($result) {
            $this->clearChangedProps();
            $this->dbProps = $result;
        }
    }

    public function get()
    {
        $sql = "SELECT * FROM " . static::getTableName();
        return Db::getInstance()->queryAll($sql);
    }


    public function insert() {

        if (count($this->dbProps)>0) {
            throw new \Exception("Экземпляр класса '" . get_class($this) . "' связан с существующей записью БД, для обновления значений воспользуйтесь методом update.");
        }

        $keys = array_keys($this->changedProps);
        $sql = sprintf( "INSERT INTO %s(%s) VALUES (%s)",
            static::getTableName(),
            implode(",",$keys),
            ':' . implode(", :",$keys)
        );

        if (Db::getInstance()->execute($sql, $this->changedProps)) {
            $id = Db::getInstance()->lastInsertId();
            $this->first($id);
        }    

    }

    public function update() {
        if (count($this->changedProps) == 0) {
            return;
        }

        $id = $this->dbProps[static::$idName];
        if (!isset($id)) {
            return;
        }    

        $setStr = '';
        foreach ($this->changedProps as $key=>$value) {
            $setStr .= "{$key} = :{$key},";
        }
        $setStr = substr($setStr,0,-1);

        $sql = sprintf("UPDATE %s SET %s WHERE %s = %s",
            static::getTableName(),
            $setStr,
            static::$idName,
            $id //Инъекция не возможна, т.к. $dbProps заполняется из базы данных
        );        

        if (Db::getInstance()->execute($sql, $this->changedProps)) {
            $this->dbProps = array_merge($this->dbProps, $this->changedProps); //Можно конечно прочитать базу и обновить значения через метод first, но доверимся)
            $this->clearChangedProps();
        }    
    }

    public function delete() {
        $id = $this->dbProps[static::$idName];
        if (!isset($id)) {
            return;
        }

        $sql = sprintf("DELETE FROM %s WHERE %s = :id",
            static::getTableName(),
            static::$idName);

        if (Db::getInstance()->execute($sql, ['id' => $id])) {
            $this->clearChangedProps();
            $this->dbProps = [];
        }                

    }

    abstract public static function getTableName();
}