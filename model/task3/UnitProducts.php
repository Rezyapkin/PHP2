<?php

namespace app\model\task3;

class UnitProducts extends QtyProducts
{
    protected $unit;
    
    public function __construct($name='Undefined', $price=0, $quantity=1, $unit='кг')
    {
        if (!isset(static::getUnits()[$unit])) {
            throw new Exception('Не возможность создать экземпляр класса UnitProducts с указанной единицей измерения!');
        }
        $this->unit = $unit;
        parent::__construct($name, $price, $quantity);
    }

    public function getTemplate() {
        $total = $this->getPrice();
        return "<div class='product'>{$this->name} - <b>{$this->quantity} {$this->unit} за {$total} &#8381;</b></div>";
    }

    public function getPrice() {
        return $this->price * static::getUnits()[$this->unit] * $this->quantity;
    }

    public static function getUnits() {
        return [
            'кг' => 1,
            'г' => 0.0001,
        ];
    }
}