<?php

namespace app\interfaces;

interface IRepository
{
    public function getTableName();
    public function getDb();
    public function getFields();
    public function getKeyFieldName();
    public function isProperties($name);
    public function getDataFields();
}
