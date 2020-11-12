<?php

namespace app\model;


class News extends DBModel
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
