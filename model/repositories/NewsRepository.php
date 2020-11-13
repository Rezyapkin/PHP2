<?php

namespace app\model\repositories;

use app\model\Repository;
use app\model\entities\New;


class NewsRepository extends Repository
{
    public function getEntityClass()
    {
        return New::class;
    }

    public function getTableName()
    {
        return "news";
    }
}
