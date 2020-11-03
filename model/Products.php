<?php

namespace app\model;


class Products extends DBModel
{
    protected $id;
    protected $name;
    protected $description;
    protected $price;

    protected public $props = [
            'name' => false,
            'description' => false,
            'price' => false,
    ];


    public function __construct($name = null, $description = null, $price = null)
    {
        $this->name = $name;
        $this->description = $description;
        $this->price = $price;
    }


    protected function getTableName() {
        return "products";
    }


}
