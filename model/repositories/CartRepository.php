<?php

namespace app\model\repositories;

use app\model\Repository;
use app\model\entities\CartItem;

class CartRepository extends Repository
{
    protected $systemProps = [
        'user_id',
        'session_id',
    ];

    public function getEntityClass()
    {
        return CartItem::class;
    }

    public function getTableName()
    {
        return "cart";
    }

}
