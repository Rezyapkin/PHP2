<?php

namespace app\controllers;

use app\model\Products;


//Это я удалю, просто чтобы показать функционал
class FunctionsController extends Controller
{

    public function actionIndex() {

        echo $this->render('functions', []);
        echo '<p>Реализована работа с агрегирующими функциями: count, max, min, agv, sum</p>';
        echo '<p>Реализована сортировка методом orderBy</p>';    
        echo '<p>Реализована фильтрация методами where и orWhere</p>';    
        echo '<p>Методы возвращающие объекты: find и first. Первый ищет элемент по id, второй возвращает первый результат</p>';    
        echo '<p>Метод get доработан для работы с where, orderby. Также у него появились два необязательный параметра limit и offset</p>';    


        echo "<ul>
        <li>Products::where('price','>','50000')->where('price','<','60000')->count();</li>
        <li>Products::where('price','>','50000')->max('price');</li>
        <li>Products::where('price','>','50000')->orWhere('id','3')->orderBy('price')->get(5,1);</li>
        <li>Products::where('price','>','50000')->where('price','<','100000')->orderBy('name')->fisrt();</li>  
        </ul>";      
        echo '<hr><br>';
        echo Products::where('price','>','50000')->where('price','<','60000')->count();
        echo '<hr><br>';
        echo Products::where('price','>','50000')->max('price');    
        echo '<hr><br>';
        print_r(Products::where('price','>','50000')->orWhere('id','3')->orderBy('price')->get(5,1));
        echo '<hr><br>';
        var_dump(Products::where('price','>','50000')->where('price','<','100000')->orderBy('name')->first());
    
    }

}