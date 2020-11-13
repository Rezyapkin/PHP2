<?php

namespace app\model\repositories;

use app\model\Repository;
use app\model\entities\Product;

class NewsRepository extends Repository
{
    public function getEntityClass()
    {
        return Product::class;
    }

    public function getTableName()
    {
        return "products";
    }
}
