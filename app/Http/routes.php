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

Route::get('/', ["as"=>"show_all_product","uses"=>"Frontend\ProductController@showAllProduct"]);
Route::get('/product_detail', ["as"=>"product_detail","uses"=>"Frontend\ProductController@showProductDetail"]);
Route::post('/buy_product', ["as"=>"buy_product","uses"=>"Frontend\ProductController@showCustomerForm"]);
Route::post('/checkout', ["as"=>"checkout","uses"=>"Frontend\ProductController@storeCustomerForm"]);
Route::post('/get_province_dropdown', ["as"=>"get_province_dropdown","uses"=>"Frontend\ProductController@getProvinceDropdown"]);
Route::post('/get_city_dropdown', ["as"=>"get_city_dropdown","uses"=>"Frontend\ProductController@getCityDropdown"]);
Route::post('/get_district_dropdown', ["as"=>"get_district_dropdown","uses"=>"Frontend\ProductController@getDistrictDropdown"]);
Route::post('/get_payment_method', ["as"=>"get_payment_method","uses"=>"Frontend\ProductController@getPaymentMethodDropdown"]);
Route::post('/get_delivery_price', ["as"=>"get_delivery_price","uses"=>"Frontend\ProductController@getDeliveryPrice"]);
Route::get('/check_data', ["as"=>"check_data","uses"=>"Frontend\ProductController@checkData"]);
Route::get('/administrator/home', ["as"=>"backend_home","uses"=>"Backend\MainController@home"]);
