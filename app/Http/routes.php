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
Route::group(['middleware' => ['auth.admin']], function(){
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
  //management product
  //category
  Route::get('/administrator/manage-product/category', ["as"=>"backend_manage_product_category","uses"=>"Backend\ManageProductController@category"]);
  Route::post('/administrator/manage-product/category-read', ["as"=>"backend_manage_product_category_read","uses"=>"Backend\ManageProductController@categoryRead"]);
  Route::post('/administrator/manage-product/category-create', ["as"=>"backend_manage_product_category_create","uses"=>"Backend\ManageProductController@categoryCreate"]);
  Route::post('/administrator/manage-product/category-update', ["as"=>"backend_manage_product_category_update","uses"=>"Backend\ManageProductController@categoryUpdate"]);
  Route::post('/administrator/manage-product/category-destroy', ["as"=>"backend_manage_product_category_destroy","uses"=>"Backend\ManageProductController@categoryDestroy"]);
  //product
  Route::get('/administrator/manage-product/product', ["as"=>"backend_manage_product_product","uses"=>"Backend\ManageProductController@product"]);
  Route::post('/administrator/manage-product/product-read', ["as"=>"backend_manage_product_product_read","uses"=>"Backend\ManageProductController@productRead"]);
  Route::post('/administrator/manage-product/product-create', ["as"=>"backend_manage_product_product_create","uses"=>"Backend\ManageProductController@productCreate"]);
  Route::post('/administrator/manage-product/product-update', ["as"=>"backend_manage_product_product_update","uses"=>"Backend\ManageProductController@productUpdate"]);
  Route::post('/administrator/manage-product/product-destroy', ["as"=>"backend_manage_product_product_destroy","uses"=>"Backend\ManageProductController@productDestroy"]);
  Route::post('/administrator/manage-product/product-upload', ["as"=>"backend_manage_product_product_upload","uses"=>"Backend\ManageProductController@productUpload"]);
  Route::post('/administrator/manage-product/product-description', ["as"=>"backend_manage_product_product_description","uses"=>"Backend\ManageProductController@productDescription"]);
  //colour
  Route::get('/administrator/manage-product/colour', ["as"=>"backend_manage_product_colour","uses"=>"Backend\ManageProductController@colour"]);
  Route::post('/administrator/manage-product/colour-read', ["as"=>"backend_manage_product_colour_read","uses"=>"Backend\ManageProductController@colourRead"]);
  Route::post('/administrator/manage-product/colour-create', ["as"=>"backend_manage_product_colour_create","uses"=>"Backend\ManageProductController@colourCreate"]);
  Route::post('/administrator/manage-product/colour-update', ["as"=>"backend_manage_product_colour_update","uses"=>"Backend\ManageProductController@colourUpdate"]);
  Route::post('/administrator/manage-product/colour-destroy', ["as"=>"backend_manage_product_colour_destroy","uses"=>"Backend\ManageProductController@colourDestroy"]);
  Route::post('/administrator/manage-product/colour-upload', ["as"=>"backend_manage_product_colour_upload","uses"=>"Backend\ManageProductController@colourUpload"]);
  //fg_code
  Route::get('/administrator/manage-product/fg_code', ["as"=>"backend_manage_product_fg_code","uses"=>"Backend\ManageProductController@fg_code"]);
  Route::post('/administrator/manage-product/fg_code-read', ["as"=>"backend_manage_product_fg_code_read","uses"=>"Backend\ManageProductController@fg_codeRead"]);
  Route::post('/administrator/manage-product/fg_code-create', ["as"=>"backend_manage_product_fg_code_create","uses"=>"Backend\ManageProductController@fg_codeCreate"]);
  Route::post('/administrator/manage-product/fg_code-update', ["as"=>"backend_manage_product_fg_code_update","uses"=>"Backend\ManageProductController@fg_codeUpdate"]);
  Route::post('/administrator/manage-product/fg_code-destroy', ["as"=>"backend_manage_product_fg_code_destroy","uses"=>"Backend\ManageProductController@fg_codeDestroy"]);
  //management location
  //province
  Route::get('/administrator/manage-location/province', ["as"=>"backend_manage_location_province","uses"=>"Backend\ManageLocationController@province"]);
  Route::post('/administrator/manage-location/province-read', ["as"=>"backend_manage_location_province_read","uses"=>"Backend\ManageLocationController@provinceRead"]);
  Route::post('/administrator/manage-location/province-create', ["as"=>"backend_manage_location_province_create","uses"=>"Backend\ManageLocationController@provinceCreate"]);
  Route::post('/administrator/manage-location/province-update', ["as"=>"backend_manage_location_province_update","uses"=>"Backend\ManageLocationController@provinceUpdate"]);
  Route::post('/administrator/manage-location/province-destroy', ["as"=>"backend_manage_location_province_destroy","uses"=>"Backend\ManageLocationController@provinceDestroy"]);
  //province
  Route::get('/administrator/manage-location/city', ["as"=>"backend_manage_location_city","uses"=>"Backend\ManageLocationController@city"]);
  Route::post('/administrator/manage-location/city-read', ["as"=>"backend_manage_location_city_read","uses"=>"Backend\ManageLocationController@cityRead"]);
  Route::post('/administrator/manage-location/city-create', ["as"=>"backend_manage_location_city_create","uses"=>"Backend\ManageLocationController@cityCreate"]);
  Route::post('/administrator/manage-location/city-update', ["as"=>"backend_manage_location_city_update","uses"=>"Backend\ManageLocationController@cityUpdate"]);
  Route::post('/administrator/manage-location/city-destroy', ["as"=>"backend_manage_location_city_destroy","uses"=>"Backend\ManageLocationController@cityDestroy"]);
  //district
  Route::get('/administrator/manage-location/district', ["as"=>"backend_manage_location_district","uses"=>"Backend\ManageLocationController@district"]);
  Route::post('/administrator/manage-location/district-read', ["as"=>"backend_manage_location_district_read","uses"=>"Backend\ManageLocationController@districtRead"]);
  Route::post('/administrator/manage-location/district-create', ["as"=>"backend_manage_location_district_create","uses"=>"Backend\ManageLocationController@districtCreate"]);
  Route::post('/administrator/manage-location/district-update', ["as"=>"backend_manage_location_district_update","uses"=>"Backend\ManageLocationController@districtUpdate"]);
  Route::post('/administrator/manage-location/district-destroy', ["as"=>"backend_manage_location_district_destroy","uses"=>"Backend\ManageLocationController@districtDestroy"]);
});
