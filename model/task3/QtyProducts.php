<?php

namespace app\model\task3;

class QtyProducts extends Products
{
    public $quantity;
    
    public function __construct($name='Undefined', $price=0, $quantity=1)
    {
        $this->quantity = $quantity;
        parent::__construct($name, $price);
    }

    public function getTemplate() {
        $total = $this->getPrice();
        return "<div class='product'>{$this->name} - <b>{$this->quantity} X {$this->price} &#8381; = {$total} &#8381;</b></div>";
    }

    public function getPrice() {
        return $this->price * $this->quantity;
    }
}