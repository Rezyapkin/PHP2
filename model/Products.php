<?php

namespace app\model;


class Products extends Model
{
    
    public function __construct($name = 'Undefined', $description = '', $price = null, $image = '')
    {
        $this->name = $name;
        $this->description = $description;
        $this->price = $price;
        $this->image = $image;

    }

    public function __toString() {
        return $this->getTemplate();
    }

    public function getTemplate() {
        return sprintf("<div class='product'>({$this->id}) {$this->name} - <b>{$this->price} &#8381;</b>%s</div>",
        ($this->isChanged()) ? " (внесены изменения, но не сохранены в БД)" : "");
    }

    public static function getTableName() {
        return "products";
    }


}
