<?php

use app\engine\Route;

Route::get('/','SimplePage.Index');
Route::post('/api/products/getItems','Product.ApiCatalog');
Route::get('/catalog','Product.Index');
Route::get('/catalog/{id}','Product.Card');