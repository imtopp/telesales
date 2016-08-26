<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', ["as"=>"show_all_product","uses"=>"FrontendProductController@showAllProduct"]);
Route::get('/product_detail', ["as"=>"product_detail","uses"=>"FrontendProductController@showProductDetail"]);
Route::post('/buy_product', ["as"=>"buy_product","uses"=>"FrontendProductController@showCustomerForm"]);
Route::post('/checkout', ["as"=>"checkout","uses"=>"FrontendProductController@storeCustomerForm"]);
Route::get('/check_data', ["as"=>"check_data","uses"=>"FrontendProductController@checkData"]);
