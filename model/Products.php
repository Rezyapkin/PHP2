<?php

namespace app\model;

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
