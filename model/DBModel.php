<?php


namespace app\model;


use app\engine\Db;

abstract class DBModel extends Model
{
    //Уйдем от статики таким способом:
    public static function __callStatic($method, $parameters)
    {
        $instance = new static;
     
        return call_user_func_array([$instance, $method], $parameters);
    }

    public function first($id)
    {
        $sql = "SELECT * FROM {$this->getTableName()} WHERE id = :id";

        return Db::getInstance()->queryObject($sql, ['id' => $id], static::class);
    }

    public function get()
    {
        $sql = "SELECT * FROM {$this->getTableName()}";
        return Db::getInstance()->queryAll($sql);
    }

    public static function getLimit($page) {
        $tableName = static::getTableName();
        $sql = "SELECT * FROM {$this->getTableName()} LIMIT 0, :page";
        return Db::getInstance()->queryLimit($sql, ['page' => $page]);
    }

    //TODO реализовать insert
    public function insert() {

        $params = [];
        $columns = [];

        //TODO если закрыли поля переделать на $this->props
        foreach ($this as $key => $value) {
            if ($key == 'id') continue;
            $params[":{$key}"] = $value;
            $columns[] = "`$key`";
        }

        $columns = implode(", ", $columns);
        $values = implode(", ", array_keys($params));

        $sql = "INSERT INTO {$this->getTableName()} ({$columns}) VALUES ({$values})";
        var_dump($sql, $params);
        Db::getInstance()->execute($sql, $params);
        $this->id = Db::getInstance()->lastInsertId();

        return $this;
    }

    public function update() {

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

    abstract protected function getTableName();
}