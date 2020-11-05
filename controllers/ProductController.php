<?php

namespace app\controllers;

use app\model\Products;

class ProductController extends Controller
{
    
    protected $products_page_count = 5;

    public function actionIndex() {

        $offset = (int)$_GET['offset'];

        $catalog = Products::get($this->products_page_count, $offset);

        echo $this->render('catalog', [
            'catalog' => $catalog,
            'page_size' => $this->products_page_count,
        ]);
    }

    public function actionCard() {
        $id = (int)$_GET['id'];
        $product = Products::find($id);
        echo $this->render('card', [
            'product' => $product
        ]);
    }

    public function actionApiCatalog() {
        $offset = (int)$_GET['offset'];
        $catalog = Products::get($this->products_page_count, $offset);
        echo json_encode($catalog, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

}