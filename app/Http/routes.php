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
//Authentication Route
Route::controller('/auth','Auth\AuthController');
Route::get('/login', ["as"=>"login","uses"=>"Auth\AuthController@getLogin"]);
Route::get('/logout', ["as"=>"logout","uses"=>"Auth\AuthController@getLogout"]);

//Frontend Route

//Product
//List Product
Route::get('/', ["as"=>"show_all_product","uses"=>"Frontend\Product\ListProductController@index"]);
//Detail Product
Route::get('/product_detail', ["as"=>"product_detail","uses"=>"Frontend\Product\DetailProductController@index"]);

//Transaction
//Order
Route::get('/order', ["as"=>"order","uses"=>"Frontend\Transaction\OrderController@index"]);
Route::post('/checkout', ["as"=>"checkout","uses"=>"Frontend\Transaction\OrderController@checkout"]);
Route::post('/get_province_dropdown', ["as"=>"order_get_province_dropdown","uses"=>"Frontend\Transaction\OrderController@getProvinceDropdown"]);
Route::post('/get_city_dropdown', ["as"=>"order_get_city_dropdown","uses"=>"Frontend\Transaction\OrderController@getCityDropdown"]);
Route::post('/get_district_dropdown', ["as"=>"order_get_district_dropdown","uses"=>"Frontend\Transaction\OrderController@getDistrictDropdown"]);
Route::post('/get_courier', ["as"=>"order_get_courier_dropdown","uses"=>"Frontend\Transaction\OrderController@getCourierDropdown"]);
Route::post('/get_courier_package', ["as"=>"order_get_courier_package_dropdown","uses"=>"Frontend\Transaction\OrderController@getCourierPackageDropdown"]);
Route::post('/get_payment_method', ["as"=>"order_get_payment_method_dropdown","uses"=>"Frontend\Transaction\OrderController@getPaymentMethodDropdown"]);
Route::post('/get_delivery_price', ["as"=>"order_get_delivery_price","uses"=>"Frontend\Transaction\OrderController@getDeliveryPrice"]);

//Route::get('/check_data', ["as"=>"check_data","uses"=>"Frontend\ProductController@checkData"]);

//Backend Route Group
Route::group(['middleware' => ['auth.administrator']], function(){

  //Administrator Dashboard
  Route::get('/administrator', ["uses"=>"Backend\Administrator\MainController@administrator"]);
  Route::get('/administrator/home', ["as"=>"administrator_home","uses"=>"Backend\Administrator\MainController@home"]);

  //Settings Route
  //Application Properties
  Route::get('/administrator/settings/application_properties', ["as"=>"administrator_settings_application_properties","uses"=>"Backend\Administrator\Settings\ApplicationPropertiesController@index"]);
  Route::post('/administrator/settings/application_properties-update', ["as"=>"administrator_settings_application_properties_update","uses"=>"Backend\Administrator\Settings\ApplicationPropertiesController@update"]);

  //Manage Users
  Route::get('/administrator/settings/manage_users', ["as"=>"administrator_settings_manage_users","uses"=>"Backend\Administrator\Settings\ManageUsersController@index"]);
  Route::post('/administrator/settings/manage_users-read', ["as"=>"administrator_settings_manage_users_read","uses"=>"Backend\Administrator\Settings\ManageUsersController@read"]);
  Route::post('/administrator/settings/manage_users-add', ["as"=>"administrator_settings_manage_users_create","uses"=>"Backend\Administrator\Settings\ManageUsersController@create"]);
  Route::post('/administrator/settings/manage_users-update', ["as"=>"administrator_settings_manage_users_update","uses"=>"Backend\Administrator\Settings\ManageUsersController@update"]);
  Route::post('/administrator/settings/manage_users-destroy', ["as"=>"administrator_settings_manage_users_destroy","uses"=>"Backend\Administrator\Settings\ManageUsersController@destroy"]);

  //Content Route
  //Manage Product
  //Category
  Route::get('/administrator/manage-product/category', ["as"=>"administrator_manage_product_category","uses"=>"Backend\Administrator\Content\ManageProduct\CategoryController@index"]);
  Route::post('/administrator/manage-product/category-read', ["as"=>"administrator_manage_product_category_read","uses"=>"Backend\Administrator\Content\ManageProduct\CategoryController@read"]);
  Route::post('/administrator/manage-product/category-create', ["as"=>"administrator_manage_product_category_create","uses"=>"Backend\Administrator\Content\ManageProduct\CategoryController@create"]);
  Route::post('/administrator/manage-product/category-update', ["as"=>"administrator_manage_product_category_update","uses"=>"Backend\Administrator\Content\ManageProduct\CategoryController@update"]);
  Route::post('/administrator/manage-product/category-destroy', ["as"=>"administrator_manage_product_category_destroy","uses"=>"Backend\Administrator\Content\ManageProduct\CategoryController@destroy"]);
  //Product
  Route::get('/administrator/manage-product/product', ["as"=>"administrator_manage_product_product","uses"=>"Backend\Administrator\Content\ManageProduct\ProductController@index"]);
  Route::post('/administrator/manage-product/product-read', ["as"=>"administrator_manage_product_product_read","uses"=>"Backend\Administrator\Content\ManageProduct\ProductController@read"]);
  Route::post('/administrator/manage-product/product-create', ["as"=>"administrator_manage_product_product_create","uses"=>"Backend\Administrator\Content\ManageProduct\ProductController@create"]);
  Route::post('/administrator/manage-product/product-update', ["as"=>"administrator_manage_product_product_update","uses"=>"Backend\Administrator\Content\ManageProduct\ProductController@update"]);
  Route::post('/administrator/manage-product/product-destroy', ["as"=>"administrator_manage_product_product_destroy","uses"=>"Backend\Administrator\Content\ManageProduct\ProductController@destroy"]);
  Route::post('/administrator/manage-product/product-upload', ["as"=>"administrator_manage_product_product_upload","uses"=>"Backend\Administrator\Content\ManageProduct\ProductController@upload"]);
  Route::post('/administrator/manage-product/product-description', ["as"=>"administrator_manage_product_product_description","uses"=>"Backend\Administrator\Content\ManageProduct\ProductController@description"]);
  Route::post('/administrator/manage-product/product-get-category', ["as"=>"administrator_manage_product_product_get_category","uses"=>"Backend\Administrator\Content\ManageProduct\ProductController@getCategory"]);
  //Colour
  Route::get('/administrator/manage-product/colour', ["as"=>"administrator_manage_product_colour","uses"=>"Backend\Administrator\Content\ManageProduct\ColourController@index"]);
  Route::post('/administrator/manage-product/colour-read', ["as"=>"administrator_manage_product_colour_read","uses"=>"Backend\Administrator\Content\ManageProduct\ColourController@read"]);
  Route::post('/administrator/manage-product/colour-create', ["as"=>"administrator_manage_product_colour_create","uses"=>"Backend\Administrator\Content\ManageProduct\ColourController@create"]);
  Route::post('/administrator/manage-product/colour-update', ["as"=>"administrator_manage_product_colour_update","uses"=>"Backend\Administrator\Content\ManageProduct\ColourController@update"]);
  Route::post('/administrator/manage-product/colour-destroy', ["as"=>"administrator_manage_product_colour_destroy","uses"=>"Backend\Administrator\Content\ManageProduct\ColourController@destroy"]);
  Route::post('/administrator/manage-product/colour-upload', ["as"=>"administrator_manage_product_colour_upload","uses"=>"Backend\Administrator\Content\ManageProduct\ColourController@upload"]);
  Route::post('/administrator/manage-product/colour-get-category', ["as"=>"administrator_manage_product_colour_get_category","uses"=>"Backend\Administrator\Content\ManageProduct\ColourController@getCategory"]);
  Route::post('/administrator/manage-product/colour-get-product', ["as"=>"administrator_manage_product_colour_get_product","uses"=>"Backend\Administrator\Content\ManageProduct\ColourController@getProduct"]);
  //FGCODE
  Route::get('/administrator/manage-product/fg_code', ["as"=>"administrator_manage_product_fg_code","uses"=>"Backend\Administrator\Content\ManageProduct\FgcodeController@index"]);
  Route::post('/administrator/manage-product/fg_code-read', ["as"=>"administrator_manage_product_fg_code_read","uses"=>"Backend\Administrator\Content\ManageProduct\FgcodeController@read"]);
  Route::post('/administrator/manage-product/fg_code-create', ["as"=>"administrator_manage_product_fg_code_create","uses"=>"Backend\Administrator\Content\ManageProduct\FgcodeController@create"]);
  Route::post('/administrator/manage-product/fg_code-update', ["as"=>"administrator_manage_product_fg_code_update","uses"=>"Backend\Administrator\Content\ManageProduct\FgcodeController@update"]);
  Route::post('/administrator/manage-product/fg_code-destroy', ["as"=>"administrator_manage_product_fg_code_destroy","uses"=>"Backend\Administrator\Content\ManageProduct\FgcodeController@destroy"]);
  Route::post('/administrator/manage-product/fg_code-get-category', ["as"=>"administrator_manage_product_fg_code_get_category","uses"=>"Backend\Administrator\Content\ManageProduct\FgcodeController@getCategory"]);
  Route::post('/administrator/manage-product/fg_code-get-product', ["as"=>"administrator_manage_product_fg_code_get_product","uses"=>"Backend\Administrator\Content\ManageProduct\FgcodeController@getProduct"]);
  Route::post('/administrator/manage-product/fg_code-get-colour', ["as"=>"administrator_manage_product_fg_code_get_colour","uses"=>"Backend\Administrator\Content\ManageProduct\FgcodeController@getColour"]);

  //Manage Location
  //Province
  Route::get('/administrator/manage-location/province', ["as"=>"administrator_manage_location_province","uses"=>"Backend\Administrator\Content\ManageLocation\ProvinceController@index"]);
  Route::post('/administrator/manage-location/province-read', ["as"=>"administrator_manage_location_province_read","uses"=>"Backend\Administrator\Content\ManageLocation\ProvinceController@read"]);
  Route::post('/administrator/manage-location/province-create', ["as"=>"administrator_manage_location_province_create","uses"=>"Backend\Administrator\Content\ManageLocation\ProvinceController@create"]);
  Route::post('/administrator/manage-location/province-update', ["as"=>"administrator_manage_location_province_update","uses"=>"Backend\Administrator\Content\ManageLocation\ProvinceController@update"]);
  Route::post('/administrator/manage-location/province-destroy', ["as"=>"administrator_manage_location_province_destroy","uses"=>"Backend\Administrator\Content\ManageLocation\ProvinceController@destroy"]);
  //City
  Route::get('/administrator/manage-location/city', ["as"=>"administrator_manage_location_city","uses"=>"Backend\Administrator\Content\ManageLocation\CityController@index"]);
  Route::post('/administrator/manage-location/city-read', ["as"=>"administrator_manage_location_city_read","uses"=>"Backend\Administrator\Content\ManageLocation\CityController@read"]);
  Route::post('/administrator/manage-location/city-create', ["as"=>"administrator_manage_location_city_create","uses"=>"Backend\Administrator\Content\ManageLocation\CityController@create"]);
  Route::post('/administrator/manage-location/city-update', ["as"=>"administrator_manage_location_city_update","uses"=>"Backend\Administrator\Content\ManageLocation\CityController@update"]);
  Route::post('/administrator/manage-location/city-destroy', ["as"=>"administrator_manage_location_city_destroy","uses"=>"Backend\Administrator\Content\ManageLocation\CityController@destroy"]);
  //District
  Route::get('/administrator/manage-location/district', ["as"=>"administrator_manage_location_district","uses"=>"Backend\Administrator\Content\ManageLocation\DistrictController@index"]);
  Route::post('/administrator/manage-location/district-read', ["as"=>"administrator_manage_location_district_read","uses"=>"Backend\Administrator\Content\ManageLocation\DistrictController@read"]);
  Route::post('/administrator/manage-location/district-create', ["as"=>"administrator_manage_location_district_create","uses"=>"Backend\Administrator\Content\ManageLocation\DistrictController@create"]);
  Route::post('/administrator/manage-location/district-update', ["as"=>"administrator_manage_location_district_update","uses"=>"Backend\Administrator\Content\ManageLocation\DistrictController@update"]);
  Route::post('/administrator/manage-location/district-destroy', ["as"=>"administrator_manage_location_district_destroy","uses"=>"Backend\Administrator\Content\ManageLocation\DistrictController@destroy"]);
  Route::post('/administrator/manage-location/district-get-province', ["as"=>"administrator_manage_location_district_get_province","uses"=>"Backend\Administrator\Content\ManageLocation\DistrictController@get_province"]);
  Route::post('/administrator/manage-location/district-get-city', ["as"=>"administrator_manage_location_district_get_city","uses"=>"Backend\Administrator\Content\ManageLocation\DistrictController@get_city"]);

  //Manage Payment
  //Payment Method
  Route::get('/administrator/manage-payment/payment_method', ["as"=>"administrator_manage_payment_method","uses"=>"Backend\Administrator\Content\ManagePayment\PaymentMethodController@index"]);
  Route::post('/administrator/manage-payment/payment_method-read', ["as"=>"administrator_manage_payment_method_read","uses"=>"Backend\Administrator\Content\ManagePayment\PaymentMethodController@read"]);
  Route::post('/administrator/manage-payment/payment_method-update', ["as"=>"administrator_manage_payment_method_update","uses"=>"Backend\Administrator\Content\ManagePayment\PaymentMethodController@update"]);
  //Payment Method Location Mapping
  Route::get('/administrator/manage-payment/payment_method_location_mapping', ["as"=>"administrator_manage_payment_method_location_mapping","uses"=>"Backend\Administrator\Content\ManagePayment\LocationMappingController@index"]);
  Route::post('/administrator/manage-payment/payment_method_location_mapping-read', ["as"=>"administrator_manage_payment_method_location_mapping_read","uses"=>"Backend\Administrator\Content\ManagePayment\LocationMappingController@read"]);
  Route::post('/administrator/manage-payment/payment_method_location_mapping-create', ["as"=>"administrator_manage_payment_method_location_mapping_create","uses"=>"Backend\Administrator\Content\ManagePayment\LocationMappingController@create"]);
  Route::post('/administrator/manage-payment/payment_method_location_mapping-update', ["as"=>"administrator_manage_payment_method_location_mapping_update","uses"=>"Backend\Administrator\Content\ManagePayment\LocationMappingController@update"]);
  Route::post('/administrator/manage-payment/payment_method_location_mapping-destroy', ["as"=>"administrator_manage_payment_method_location_mapping_destroy","uses"=>"Backend\Administrator\Content\ManagePayment\LocationMappingController@destroy"]);
  Route::post('/administrator/manage-payment/payment_method_location_mapping-get-province', ["as"=>"administrator_manage_payment_method_location_mapping_get_province","uses"=>"Backend\Administrator\Content\ManagePayment\LocationMappingController@getProvince"]);
  Route::post('/administrator/manage-payment/payment_method_location_mapping-get-city', ["as"=>"administrator_manage_payment_method_location_mapping_get_city","uses"=>"Backend\Administrator\Content\ManagePayment\LocationMappingController@getCity"]);
  Route::post('/administrator/manage-payment/payment_method_location_mapping-get-district', ["as"=>"administrator_manage_payment_method_location_mapping_get_district","uses"=>"Backend\Administrator\Content\ManagePayment\LocationMappingController@getDistrict"]);

  //Manage Courier
  //Courier
  Route::get('/administrator/manage-courier/courier', ["as"=>"administrator_manage_courier","uses"=>"Backend\Administrator\Content\ManageCourier\CourierController@index"]);
  Route::post('/administrator/manage-courier/courier-read', ["as"=>"administrator_manage_courier_read","uses"=>"Backend\Administrator\Content\ManageCourier\CourierController@read"]);
  Route::post('/administrator/manage-courier/courier-create', ["as"=>"administrator_manage_courier_create","uses"=>"Backend\Administrator\Content\ManageCourier\CourierController@create"]);
  Route::post('/administrator/manage-courier/courier-update', ["as"=>"administrator_manage_courier_update","uses"=>"Backend\Administrator\Content\ManageCourier\CourierController@update"]);
  Route::post('/administrator/manage-courier/courier-destroy', ["as"=>"administrator_manage_courier_destroy","uses"=>"Backend\Administrator\Content\ManageCourier\CourierController@destroy"]);
  Route::post('/administrator/manage-courier/courier-price-category', ["as"=>"administrator_manage_courier_price_category","uses"=>"Backend\Administrator\Content\ManageCourier\CourierController@priceCategory"]);
  //Courier Package
  Route::get('/administrator/manage-courier/courier_package', ["as"=>"administrator_manage_courier_package","uses"=>"Backend\Administrator\Content\ManageCourier\CourierPackageController@index"]);
  Route::post('/administrator/manage-courier/courier_package-read', ["as"=>"administrator_manage_courier_package_read","uses"=>"Backend\Administrator\Content\ManageCourier\CourierPackageController@read"]);
  Route::post('/administrator/manage-courier/courier_package-create', ["as"=>"administrator_manage_courier_package_create","uses"=>"Backend\Administrator\Content\ManageCourier\CourierPackageController@create"]);
  Route::post('/administrator/manage-courier/courier_package-update', ["as"=>"administrator_manage_courier_package_update","uses"=>"Backend\Administrator\Content\ManageCourier\CourierPackageController@update"]);
  Route::post('/administrator/manage-courier/courier_package-destroy', ["as"=>"administrator_manage_courier_package_destroy","uses"=>"Backend\Administrator\Content\ManageCourier\CourierPackageController@destroy"]);
  Route::post('/administrator/manage-courier/courier_package-get-courier', ["as"=>"administrator_manage_courier_package_get_courier","uses"=>"Backend\Administrator\Content\ManageCourier\CourierPackageController@getCourier"]);
  //Courier Delivery Price
  Route::get('/administrator/manage-courier/courier_delivery_price', ["as"=>"administrator_manage_courier_delivery_price","uses"=>"Backend\Administrator\Content\ManageCourier\DeliveryPriceController@index"]);
  Route::post('/administrator/manage-courier/courier_delivery_price-read', ["as"=>"administrator_manage_courier_delivery_price_read","uses"=>"Backend\Administrator\Content\ManageCourier\DeliveryPriceController@read"]);
  Route::post('/administrator/manage-courier/courier_delivery_price-create', ["as"=>"administrator_manage_courier_delivery_price_create","uses"=>"Backend\Administrator\Content\ManageCourier\DeliveryPriceController@create"]);
  Route::post('/administrator/manage-courier/courier_delivery_price-update', ["as"=>"administrator_manage_courier_delivery_price_update","uses"=>"Backend\Administrator\Content\ManageCourier\DeliveryPriceController@update"]);
  Route::post('/administrator/manage-courier/courier_delivery_price-get-province', ["as"=>"administrator_manage_courier_delivery_price_get_province","uses"=>"Backend\Administrator\Content\ManageCourier\DeliveryPriceController@getProvince"]);
  Route::post('/administrator/manage-courier/courier_delivery_price-get-city', ["as"=>"administrator_manage_courier_delivery_price_get_city","uses"=>"Backend\Administrator\Content\ManageCourier\DeliveryPriceController@getCity"]);
  Route::post('/administrator/manage-courier/courier_delivery_price-get-district', ["as"=>"administrator_manage_courier_delivery_price_get_district","uses"=>"Backend\Administrator\Content\ManageCourier\DeliveryPriceController@getDistrict"]);
  Route::post('/administrator/manage-courier/courier_delivery_price-get-courier', ["as"=>"administrator_manage_courier_delivery_price_get_courier","uses"=>"Backend\Administrator\Content\ManageCourier\DeliveryPriceController@getCourier"]);
  Route::post('/administrator/manage-courier/courier_delivery_price-get-courier-package', ["as"=>"administrator_manage_courier_delivery_price_get_courier_package","uses"=>"Backend\Administrator\Content\ManageCourier\DeliveryPriceController@getCourierPackage"]);
  Route::post('/administrator/manage-courier/courier_delivery_price-get-courier-price-category', ["as"=>"administrator_manage_courier_delivery_price_get_price_category","uses"=>"Backend\Administrator\Content\ManageCourier\DeliveryPriceController@getPriceCategory"]);
});

//Backend Route Group
Route::group(['middleware' => ['auth.telesales']], function(){

  //Administrator Dashboard
  Route::get('/telesales', ["uses"=>"Backend\Telesales\MainController@telesales"]);
  Route::get('/telesales/home', ["as"=>"telesales_home","uses"=>"Backend\Telesales\MainController@home"]);

  //Manage Order
  Route::get('/telesales/manage-order', ["as"=>"telesales_manage_order","uses"=>"Backend\Telesales\Content\ManageOrder\OrderController@index"]);
  Route::post('/telesales/manage-order-read', ["as"=>"telesales_manage_order_read","uses"=>"Backend\Telesales\Content\ManageOrder\OrderController@read"]);
  Route::post('/telesales/manage-order-create', ["as"=>"telesales_manage_order_create","uses"=>"Backend\Telesales\Content\ManageOrder\OrderController@create"]);
  Route::post('/telesales/manage-order-cancel-order', ["as"=>"telesales_manage_order_cancel_order","uses"=>"Backend\Telesales\Content\ManageOrder\OrderController@cancelOrder"]);
  Route::post('/telesales/manage-order-get-category', ["as"=>"telesales_manage_order_get_category","uses"=>"Backend\Telesales\Content\ManageOrder\OrderController@getCategory"]);
  Route::post('/telesales/manage-order-get-product', ["as"=>"telesales_manage_order_get_product","uses"=>"Backend\Telesales\Content\ManageOrder\OrderController@getProduct"]);
  Route::post('/telesales/manage-order-get-product-detail', ["as"=>"telesales_manage_order_get_product_detail","uses"=>"Backend\Telesales\Content\ManageOrder\OrderController@getProductDetail"]);
  Route::post('/telesales/manage-order-get-colour', ["as"=>"telesales_manage_order_get_colour","uses"=>"Backend\Telesales\Content\ManageOrder\OrderController@getColour"]);
  Route::post('/telesales/manage-order-get-colour-image', ["as"=>"telesales_manage_order_get_colour_image","uses"=>"Backend\Telesales\Content\ManageOrder\OrderController@getColourImage"]);
  Route::post('/telesales/manage-order-get-fg-code', ["as"=>"telesales_manage_order_get_fg_code","uses"=>"Backend\Telesales\Content\ManageOrder\OrderController@getFgCode"]);
  Route::post('/telesales/manage-order-get-province', ["as"=>"telesales_manage_order_get_province","uses"=>"Backend\Telesales\Content\ManageOrder\OrderController@getProvince"]);
  Route::post('/telesales/manage-order-get-city', ["as"=>"telesales_manage_order_get_city","uses"=>"Backend\Telesales\Content\ManageOrder\OrderController@getCity"]);
  Route::post('/telesales/manage-order-get-district', ["as"=>"telesales_manage_order_get_district","uses"=>"Backend\Telesales\Content\ManageOrder\OrderController@getDistrict"]);
  Route::post('/telesales/manage-order-get-courier', ["as"=>"telesales_manage_order_get_courier","uses"=>"Backend\Telesales\Content\ManageOrder\OrderController@getCourier"]);
  Route::post('/telesales/manage-order-get-courier-package', ["as"=>"telesales_manage_order_get_courier_package","uses"=>"Backend\Telesales\Content\ManageOrder\OrderController@getCourierPackage"]);
  Route::post('/telesales/manage-order-get-payment-method', ["as"=>"telesales_manage_order_get_payment_method","uses"=>"Backend\Telesales\Content\ManageOrder\OrderController@getPaymentMethod"]);
  Route::post('/telesales/manage-order-get-delivery-price', ["as"=>"telesales_manage_order_get_delivery_price","uses"=>"Backend\Telesales\Content\ManageOrder\OrderController@getDeliveryPrice"]);
});
