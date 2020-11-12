<?php

Route::get('/','SimplePage.Index');
Route::post('/api/products/getItems','Product.ApiDynamicList');
Route::get('/catalog','Product.Index');
Route::get('/catalog/{id}','Product.Card');
Route::post('/api/news/getItems','News.ApiDynamicList');
Route::get('/news','News.Index');
Route::get('/news/{id}','News.Card');
Route::match(['POST', 'GET'], '/login','Auth.Login');
Route::post('/register','Auth.Register');
Route::get('/register','Auth.Login');