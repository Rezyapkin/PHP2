<?php

namespace app\model\repositories;

use app\model\Repository;
use app\model\entities\Feedback;

class FeedbackRepository extends Repository
{
    
    protected $category = "";
    protected $groupId = "";

    protected const CATEGORIES = [
        'product' => 'product_id'
    ];

    public function getEntityClass()
    {
        return Feedback::class;
    }

    public function getTableName()
    {
        return "feedback" . ($this->category ? ("_".$this->category) : "");
    }

    public function setCategoty($category) {
        if (array_key_exists($category, static::CATEGORIES)) {
            $this->category = $category;
        }
    }

    public function __get($name) {
        if ($name == static::CATEGORIES[$this->category]) {
            return $this->groupId;
        }
    }

    public function setGroupId($groupId) {
        $this->groupId = $groupId;
    }

    public function getHiddenProps() {
        if (isset($this->category)) {
            return [static::CATEGORIES[$this->category]];
        } else {
            return [];
        }
    }

    public function getGroupFieldName() {
        return static::CATEGORIES[$this->category];
    }


    
}
