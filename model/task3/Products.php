<?php

namespace app\model\task3;

abstract class Products
{
    public $name;
    public $price;
    public static $total;
    
    public function __construct($name='Undefined', $price=0)
    {
        $this->name = $name;
        $this->price = $price;
        self::$total += $this->getPrice();
    }

    public function getTemplate() {
        return "<div class='product'>{$name} - <b>{$price} &#8381;</b></div>";
    }

    public static function getTotalTemplate() {
        $total = static::$total;
        return "<div class='total-product'>Итого: <b>{$total} &#8381;</b></div>";
    }

    abstract public function getPrice();
}