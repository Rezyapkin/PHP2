<?php

namespace app\model;


class Products extends DBModel
{
    protected $id;
    protected $name;
    protected $description;
    protected $price;
    protected $image;    

    protected $props = [
            'name' => false,
            'description' => false,
            'price' => false,
            'image' => false
    ];


    public function __construct($name = null, $description = null, $price = null, $image = 'undefined.jpg')
    {
        $this->name = $name;
        $this->description = $description;
        $this->price = $price;
        $this->image = $image;
    }


    public function getTableName() {
        return "products";
    }


}
