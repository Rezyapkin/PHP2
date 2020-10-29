<?php

namespace app\interfaces;

interface IModel
{
    public function first($id);
    public static function get();
    public static function getTableName();
}