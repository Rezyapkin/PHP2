<?php

namespace app\model\entities;

use app\model\Model;

class Order extends Model
{
    protected $id;
    protected $date;
    protected $userId;
    protected $status;
    protected $name;
    protected $phone;
    protected $address;
    protected $uId;



    protected $props = [
        'date' => false,
        'userId' => false,
        'status' => false,
        'name' => false,
        'phone' => false,
        'address' => false,
        'uId' => false       
    ];

    protected $protectedProps = [
        'uId',
        'date',
    ];

    /*
    protected $realatedModels = [
        'user' => [
            'fieldName' => 'userId',
            'className' => '\\Users' 
            ]
    ];  
    */

    protected function fillProtectedProps() {
        $this->u_id = uniqid(rand(), true); 
    }

    public function __construct($name = null, $phone = null, $addrees = null, $status=null, $userId = null) 
    {
        $this->name = $name;
        $this->phone = $phone;
        $this->userId = $userId;
        $this->status = $status;
        $this->fillProtectedProps();
    }

}