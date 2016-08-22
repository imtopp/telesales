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
Route::get('/buy_product', ["as"=>"customer_form","uses"=>"FrontendProductController@showCustomerForm"]);
