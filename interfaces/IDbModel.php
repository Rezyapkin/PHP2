<?php

namespace app\interfaces;

interface IDbModel
{
    public function getTableName();
    public function getFields();
    public function getKeyFieldName();
    public function isProperties($name);
    public function getDataFields();
}
