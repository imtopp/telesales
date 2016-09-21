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
Route::controller('/auth','Auth\AuthController');
//Route::get('/login', ["as"=>"login_page","uses"=>"MainController@login"]);
//Route::post('/signup',['as'=>'user_registration','uses'=>'MainController@userRegistration']);
//Route::post('/signin',['as'=>'user_login','uses'=>'MainController@userLogin']);
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
Route::group(['middleware' => 'auth','middleware' => 'admin'], function(){
  Route::get('/administrator', ["as"=>"backend_home","uses"=>"Backend\MainController@administrator"]);
  Route::get('/administrator/home', ["as"=>"backend_home","uses"=>"Backend\MainController@home"]);
  //settings route
  Route::get('/administrator/settings/application_properties', ["as"=>"backend_settings_application_properties","uses"=>"Backend\SettingsController@applicationProperties"]);
  Route::post('/administrator/settings/application_properties-update', ["as"=>"backend_settings_application_properties_update","uses"=>"Backend\SettingsController@updateApplicationProperties"]);
  Route::get('/administrator/settings/manage_users', ["as"=>"backend_settings_manage_users","uses"=>"Backend\SettingsController@manageUsers"]);
  Route::post('/administrator/settings/manage_users-read', ["as"=>"backend_settings_manage_users_read","uses"=>"Backend\SettingsController@manageUserRead"]);
  Route::post('/administrator/settings/manage_users-add', ["as"=>"backend_settings_manage_users_create","uses"=>"Backend\SettingsController@manageUserCreate"]);
  Route::post('/administrator/settings/manage_users-update', ["as"=>"backend_settings_manage_users_update","uses"=>"Backend\SettingsController@manageUserUpdate"]);
  Route::post('/administrator/settings/manage_users-destroy', ["as"=>"backend_settings_manage_users_destroy","uses"=>"Backend\SettingsController@manageUserDestroy"]);
  //content route
  Route::get('/administrator/manage-product/category', ["as"=>"backend_manage_product_category","uses"=>"Backend\ManageProductController@category"]);
  Route::post('/administrator/manage-product/category-read', ["as"=>"backend_manage_product_category_read","uses"=>"Backend\ManageProductController@categoryRead"]);
  Route::post('/administrator/manage-product/category-create', ["as"=>"backend_manage_product_category_create","uses"=>"Backend\ManageProductController@categoryCreate"]);
  Route::post('/administrator/manage-product/category-update', ["as"=>"backend_manage_product_category_update","uses"=>"Backend\ManageProductController@categoryUpdate"]);
  Route::post('/administrator/manage-product/category-destroy', ["as"=>"backend_manage_product_category_destroy","uses"=>"Backend\ManageProductController@categoryDestroy"]);
});
