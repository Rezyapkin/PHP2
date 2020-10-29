<?php

namespace app\model;

use app\engine\Db;

class Products extends Model
{

    public $id;
    public $name;
    public $description;
    public $price;


    public static function getTableName() {
        return "products";
    }


}
