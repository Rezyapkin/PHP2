<?php

namespace app\model;

use app\interfaces\IRepository;
use app\engine\Db;


abstract class Repository implements IRepository
{

    protected $query = null;

    protected $db = null;

    public function __construct(Db $db)
    {
        $this->db = $db;
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

    public function newQuery() {
        if (!isset($this->query)) {
            $this->query = new QueryBuilder($this);
        }
        return $this->query;
    }


    public function getDb() {
        return $this->db;
    }

    protected function insert(Model $entity) {

        $params = [];

        foreach ($entity->props as $key=>$value) {
            $params["{$key}"] = $this->$key;
        }

        $columns = "`" . implode("`, `", array_keys($params)) . "`";
        $values = ":" . implode(", :", array_keys($params));

        $sql = "INSERT INTO {$this->getTableName()} ({$columns}) VALUES ({$values})";

        if ($this->db->execute($sql, $params)) {
            $entity->setKeyValue($this->db->lastInsertId());
        }

        return $this;
    }

    protected function update(Model $entity) {
        $id_name = $entity->getKeyFieldName();
        $id = $entity->getKeyValue();
        $sets = [];
        $params = [];

        foreach ($entity->props as $key=>$value) {
            if ($value) {
                $params["{$key}"] = $entity->$key;
                $sets[] = "`{$key}` = :{$key}";
            }    
        }

        if (isset($id) && count($sets) > 0) {
            $set_str = implode(", ", $sets);
            $sql = "UPDATE {$this->getTableName()} SET {$set_str} WHERE {$id_name} = '{$id}'";
            if ($this->db->execute($sql, $params)) {
                $entity->clearProps();
            }
        }
    }


    public function save(Model $entity) {
        if (is_null($entity->getKeyValue()))
            $this->insert($entity);
        else
            $this->update($entity);
    }

    public function delete(Model $entity) {
        $sql = "DELETE FROM {$this->getTableName()} WHERE id = :id";
        return $this->db->execute($sql, ['id' => $entity->getKeyValue()])->rowCount();
    }

    abstract public function getEntityClass();
    abstract public function getTableName();


}