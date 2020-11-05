<?php

namespace app\controllers;

use app\model\Products;

class ProductController extends Controller
{
    
    protected $products_page_count = 6;

    public function actionIndex() {

        //TODO Если нужна пагинация
        $page = $_GET['page'];

        $catalog = Products::get($this->products_page_count);

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
        $catalog = Products::get();
        echo json_encode($catalog, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

}