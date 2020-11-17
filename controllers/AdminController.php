<?php

namespace app\controllers;

class AdminController extends Controller
{

    public function actionIndex() {
        if (\Auth::isAdmin()) {
            echo $this->render('admin', [
                'page_size' => \App::getConfig('pageSize'),
                'partOrders' => 'all'
                ]);
        } else {
            echo $this->render('accessDenited', []);
        }    
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