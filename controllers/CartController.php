<?php

namespace app\controllers;

use app\model\entities\CartItem;

class CartController extends Controller
{

    public function getQuery() {
        $userInfo = \Auth::getUserInfo();
        if (isset($userInfo)) {
            $query = \Cart::where('user_id', $userInfo['userId']);
        } else {
            $query = \Cart::where('session_id', \Session::getId()); 
        } 
        return $query->where('quantity','>',0);
    }

    public function actionIndex() {
        echo $this->render('cart', ['page_size' => \App::getConfig('pageSize')]);
    }


    public function actionApi($params) {
        $result = [];
        $query = $this->getQuery()->orderBy('id DESC');

        if (isset($params['id'])) {
            $item = $query->find($params['id']);
        } elseif (isset($params['product_id'])) {
            $item = $query->where('product_id', $params['product_id'])->first();
        }
        switch ($params['action']) {
            case 'getItems': 
                echo $this->getJSONDynamicList($query, $params);
                return;

            case 'getCount':
                $result['result'] = 'ok';
                break;    

            case 'deleteItem': 
                if ($item) {
                    $item->quantity = 0;
                }
                break;

            case 'addItem': 
                if ($item) {
                    $item->quantity++;
                } elseif ($params['product_id']) {
                    $item = new CartItem($params['product_id']);
                    if ($item->product->id !== $params['product_id']) {
                        $item = null;
                    }
                };
                break;

            case 'subItem': 
                if ($item) {
                    $item->quantity--;
                }
                break;

            default: 
                $result['error'] = 'Не существующий метод';
        }

        if (empty($result['error']) && $item) {
            $res = ($item->quantity > 0) ? \Cart::save($item) : \Cart::delete($item);
            if ($res) {
                $result = [
                    'result' => 'ok',
                    'item' => $item->getDataFields(),
                ];
            } else {
                $result['error'] = 'error';
            }
        }
        
        if (empty($result['error'])) {
            $result['count'] = (int) $this->getQuery()->sum('quantity');
        }

        echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

}