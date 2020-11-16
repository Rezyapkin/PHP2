<?php

namespace app\controllers;

use app\model\Products;

class ProductController extends Controller
{

    const PAGE_SIZE = 10;

    public function actionIndex() {
        echo $this->render('catalog', ['page_size' => static::PAGE_SIZE]);
    }

    public function actionCard($params) {
        echo $this->actionByIdCard('\Products', 'card', $params, ['groupId' => $params['id'], 'categoryFeedback' => 'product']);
    }

    public function actionApiDynamicList($params) {
        $query = \Products::orderBy('price');
        echo $this->getJSONDynamicList($query, $params);
    }

}