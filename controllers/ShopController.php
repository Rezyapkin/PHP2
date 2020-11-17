<?php

namespace app\controllers;

class ShopController extends Controller
{

    public function actionOrderInfo($params) {

        $order = \Orders::where('uId', $params['uId'])->first();
        if ($order && $params['uId'] && $order->id) {
            $params = array_merge($params, $order->getDataFields());
            $params['statuses'] = \Orders::getAvaliableStatuses();
        } else {
            $params['error'] = 'error';
        }    
        echo $this->render('order', $params);
    }

    public function actionApiOrdersList($params) {
        if (\Auth::isAuth()) {
            $onlyCurUser = $params['partOrders'] !== 'all';
            $list = \Orders::getOrderList($params['count'], $params['offset'], $onlyCurUser);
            $query = \Orders::orderBy('id');
            if (!\Auth::isAdmin() || $onlyCurUser) {
                $query = $query->where('userId', \Auth::getUserInfo()['userId'])->where('userId', '!=', '0');
            }

            $answer = [
                'items' => $list,
                'totalCount' => $query->count(),
            ];

            echo json_encode($answer, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        } else 
        {
            $this->actionError();
        };
    }

}