<?php

namespace app\model;

class OrderItems extends CartItems;
{

    public static function getTableName() {
        return "orders";
    }

}