<?php

namespace app\interfaces;

interface IModel
{
    public static function first($id);
    public static function get();
    protected function getTableName();

}