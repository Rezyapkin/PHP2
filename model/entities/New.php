<?php

namespace app\model\entities;


class New extends Model
{
    protected $id;
    protected $title;
    protected $text;   

    protected $props = [
            'title' => false,
            'text' => false,
    ];


    public function __construct($title = null, $text = null)
    {
        $this->title = $name;
        $this->text = $text;
    }


    public function getTableName() {
        return "news";
    }



}
