<?php

namespace app\controllers;

use app\model\Products;

class ProductController extends Controller
{
    

    public function actionIndex() {

        $catalog = Products::get($this->products_page_count, $offset);

        echo $this->render('catalog', [
            'catalog' => $catalog,
            'page_size' => $this->products_page_count,
        ]);
    }

    public function actionCard($params) {
        $product = Products::find($params['id']);
        if ($product->id){
            echo $this->render('card', [
                'product' => $product
            ]);
        } else {
            return $this->errorAction();
        }
    }

    public function actionApiCatalog($params) {
        $offset = $params['offset'];
        $count = Products::count();        
        $catalog = Products::get($params['count'], $offset);
        $items = [];
        foreach ($catalog as $product) {
            $items[] = $product->getDataFields();
            if (!end($items)['id']) {
                $id = $product->getKeyFieldName();
                end($items)['id'] = $product->$id;
            }    
        }

        $answer = [
            'items' => $items,
            'totalCount' => $count
        ];

        echo json_encode($answer, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

}