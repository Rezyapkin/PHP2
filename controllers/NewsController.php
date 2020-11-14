<?php

namespace app\controllers;

use app\model\News;

class NewsController extends Controller
{
    const PAGE_SIZE = 20;

    public function actionIndex() {
        echo $this->render('news', ['page_size' => static::PAGE_SIZE]);
    }

    public function actionCard($params) {
        echo $this->actionByIdCard('\News', 'newsOne', $params);
    }

    public function actionApiDynamicList($params) {
        $query = \News::orderBy('id DESC');
        echo $this->getJSONDynamicList($query, $params);
    }

}