<?php

namespace app\model;

class CartItems extends Products;
{

    public $quantity;

    public static function getTableName() {
        return "cart";
    }

}
