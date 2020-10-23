<?php

//Класс продукта
class Product {
    public $name;
    public $description;
    public $price;
    public $images = []; //В данном случае храним url изображений. Также могут быть также ID изображений в галерее
    public $properties = []; //Характеристики товара - ассоциативный массив.

    function __construct($name, $price, $description = "") {
        $this->name = $name;
        $this->price = $price;
        $this->description = $description;
    }

    public function render() {
        return "
        <div class='product'>
            <b>{$this->name}</b>
            <p>{$this->description}</p>
            <div class='product_price' style='font-weight: bold, font-size: 16px'>{$this->price} &#8381;</div>
            <hr>
        </div>
        ";
    }
        
}

class CartItem extends Product {
    public $quantity;

    function __construct($name, $price, $description = "", $quantity) {
        parent::__construct($name, $price, $description);
        $this->quantity = $quantity;
    }

    public function render() {
        $sum = $this->price * $this->quantity;
        return "
        <div class='cart-item'>
            <b>{$this->name}</b>
            <div class='product_price' style='font-weight: bold, font-size: 16px'>{$this->quantity} X {$this->price} &#8381; = {$sum} &#8381;</div>
            <hr>
        </div>
        ";
    }

}


$asus = new Product('Ноутбук Asus', 15000, 'отличный ноут');
echo $asus->render();

$acer = new Product('Ноутбук Acer', 10000, 'средний ноут');
echo $acer->render();

echo (new CartItem('Ноутбук Acer', 10000, 'средний ноут', 2))->render();
echo (new CartItem('Ноутбук Asus', 15000, 'отличный ноут', 3))->render();
