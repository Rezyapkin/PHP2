<?php

namespace app\model;

use app\engine\Db;

abstract class DBModel extends Model
{

   
    protected function getSQLAndParams($limit=0, $offset=0, $id = null) {
        $result = [
            "params" => [],
            "sql" => "SELECT * FROM {$this->getTableName()}"
        ];

        if (!is_null($id)) {
            $result["params"][$this->keyFieldName] = $id;
            $result["sql"] .= " WHERE `{$this->keyFieldName}` = :{$this->keyFieldName}";
        }    

        if ($limit > 0) {
            $result["sql"] .= " LIMIT " . (int)$offset . ", " . (int)$limit;
        };

        return $result;
    }


    protected function first()
    {
        $query = $this->getSQLAndParams(1, 0);
        return Db::getInstance()->queryObject($query['sql'], $query['params'], static::class);
    }

    protected function find($id)
    {
        $query = $this->getSQLAndParams(1, 0, $id);
        return Db::getInstance()->queryObject($query['sql'], $query['params'], static::class);        
    }

    protected function get($limit=0, $offset=0)
    {
        $query = $this->getSQLAndParams($limit, $offset);
        return Db::getInstance()->queryAll($query['sql'],$query['params']);
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

    abstract protected function getTableName();
}