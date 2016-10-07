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
Route::group(['middleware' => ['auth.admin']], function(){

  //Administrator Dashboard
  Route::get('/administrator', ["as"=>"backend_home","uses"=>"Backend\MainController@administrator"]);
  Route::get('/administrator/home', ["as"=>"backend_home","uses"=>"Backend\MainController@home"]);

  //Settings Route
  //Application Properties
  Route::get('/administrator/settings/application_properties', ["as"=>"backend_settings_application_properties","uses"=>"Backend\Settings\ApplicationPropertiesController@index"]);
  Route::post('/administrator/settings/application_properties-update', ["as"=>"backend_settings_application_properties_update","uses"=>"Backend\Settings\ApplicationPropertiesController@update"]);

  //Manage Users
  Route::get('/administrator/settings/manage_users', ["as"=>"backend_settings_manage_users","uses"=>"Backend\Settings\ManageUsersController@index"]);
  Route::post('/administrator/settings/manage_users-read', ["as"=>"backend_settings_manage_users_read","uses"=>"Backend\Settings\ManageUsersController@read"]);
  Route::post('/administrator/settings/manage_users-add', ["as"=>"backend_settings_manage_users_create","uses"=>"Backend\Settings\ManageUsersController@create"]);
  Route::post('/administrator/settings/manage_users-update', ["as"=>"backend_settings_manage_users_update","uses"=>"Backend\Settings\ManageUsersController@update"]);
  Route::post('/administrator/settings/manage_users-destroy', ["as"=>"backend_settings_manage_users_destroy","uses"=>"Backend\Settings\ManageUsersController@destroy"]);

  //Content Route
  //Manage Product
  //Category
  Route::get('/administrator/manage-product/category', ["as"=>"backend_manage_product_category","uses"=>"Backend\Content\ManageProduct\CategoryController@index"]);
  Route::post('/administrator/manage-product/category-read', ["as"=>"backend_manage_product_category_read","uses"=>"Backend\Content\ManageProduct\CategoryController@read"]);
  Route::post('/administrator/manage-product/category-create', ["as"=>"backend_manage_product_category_create","uses"=>"Backend\Content\ManageProduct\CategoryController@create"]);
  Route::post('/administrator/manage-product/category-update', ["as"=>"backend_manage_product_category_update","uses"=>"Backend\Content\ManageProduct\CategoryController@update"]);
  Route::post('/administrator/manage-product/category-destroy', ["as"=>"backend_manage_product_category_destroy","uses"=>"Backend\Content\ManageProduct\CategoryController@destroy"]);
  //Product
  Route::get('/administrator/manage-product/product', ["as"=>"backend_manage_product_product","uses"=>"Backend\Content\ManageProduct\ProductController@index"]);
  Route::post('/administrator/manage-product/product-read', ["as"=>"backend_manage_product_product_read","uses"=>"Backend\Content\ManageProduct\ProductController@read"]);
  Route::post('/administrator/manage-product/product-create', ["as"=>"backend_manage_product_product_create","uses"=>"Backend\Content\ManageProduct\ProductController@create"]);
  Route::post('/administrator/manage-product/product-update', ["as"=>"backend_manage_product_product_update","uses"=>"Backend\Content\ManageProduct\ProductController@update"]);
  Route::post('/administrator/manage-product/product-destroy', ["as"=>"backend_manage_product_product_destroy","uses"=>"Backend\Content\ManageProduct\ProductController@destroy"]);
  Route::post('/administrator/manage-product/product-upload', ["as"=>"backend_manage_product_product_upload","uses"=>"Backend\Content\ManageProduct\ProductController@upload"]);
  Route::post('/administrator/manage-product/product-description', ["as"=>"backend_manage_product_product_description","uses"=>"Backend\Content\ManageProduct\ProductController@description"]);
  Route::post('/administrator/manage-product/product-get-category', ["as"=>"backend_manage_product_product_get_category","uses"=>"Backend\Content\ManageProduct\ProductController@getCategory"]);
  //Colour
  Route::get('/administrator/manage-product/colour', ["as"=>"backend_manage_product_colour","uses"=>"Backend\Content\ManageProduct\ColourController@index"]);
  Route::post('/administrator/manage-product/colour-read', ["as"=>"backend_manage_product_colour_read","uses"=>"Backend\Content\ManageProduct\ColourController@read"]);
  Route::post('/administrator/manage-product/colour-create', ["as"=>"backend_manage_product_colour_create","uses"=>"Backend\Content\ManageProduct\ColourController@create"]);
  Route::post('/administrator/manage-product/colour-update', ["as"=>"backend_manage_product_colour_update","uses"=>"Backend\Content\ManageProduct\ColourController@update"]);
  Route::post('/administrator/manage-product/colour-destroy', ["as"=>"backend_manage_product_colour_destroy","uses"=>"Backend\Content\ManageProduct\ColourController@destroy"]);
  Route::post('/administrator/manage-product/colour-upload', ["as"=>"backend_manage_product_colour_upload","uses"=>"Backend\Content\ManageProduct\ColourController@upload"]);
  Route::post('/administrator/manage-product/colour-get-category', ["as"=>"backend_manage_product_colour_get_category","uses"=>"Backend\Content\ManageProduct\ColourController@getCategory"]);
  Route::post('/administrator/manage-product/colour-get-product', ["as"=>"backend_manage_product_colour_get_product","uses"=>"Backend\Content\ManageProduct\ColourController@getProduct"]);
  //FGCODE
  Route::get('/administrator/manage-product/fg_code', ["as"=>"backend_manage_product_fg_code","uses"=>"Backend\Content\ManageProduct\FgcodeController@index"]);
  Route::post('/administrator/manage-product/fg_code-read', ["as"=>"backend_manage_product_fg_code_read","uses"=>"Backend\Content\ManageProduct\FgcodeController@read"]);
  Route::post('/administrator/manage-product/fg_code-create', ["as"=>"backend_manage_product_fg_code_create","uses"=>"Backend\Content\ManageProduct\FgcodeController@create"]);
  Route::post('/administrator/manage-product/fg_code-update', ["as"=>"backend_manage_product_fg_code_update","uses"=>"Backend\Content\ManageProduct\FgcodeController@update"]);
  Route::post('/administrator/manage-product/fg_code-destroy', ["as"=>"backend_manage_product_fg_code_destroy","uses"=>"Backend\Content\ManageProduct\FgcodeController@destroy"]);
  Route::post('/administrator/manage-product/fg_code-get-category', ["as"=>"backend_manage_product_fg_code_get_category","uses"=>"Backend\Content\ManageProduct\FgcodeController@getCategory"]);
  Route::post('/administrator/manage-product/fg_code-get-product', ["as"=>"backend_manage_product_fg_code_get_product","uses"=>"Backend\Content\ManageProduct\FgcodeController@getProduct"]);
  Route::post('/administrator/manage-product/fg_code-get-colour', ["as"=>"backend_manage_product_fg_code_get_colour","uses"=>"Backend\Content\ManageProduct\FgcodeController@getColour"]);

  //Manage Location
  //Province
  Route::get('/administrator/manage-location/province', ["as"=>"backend_manage_location_province","uses"=>"Backend\Content\ManageLocation\ProvinceController@index"]);
  Route::post('/administrator/manage-location/province-read', ["as"=>"backend_manage_location_province_read","uses"=>"Backend\Content\ManageLocation\ProvinceController@read"]);
  Route::post('/administrator/manage-location/province-create', ["as"=>"backend_manage_location_province_create","uses"=>"Backend\Content\ManageLocation\ProvinceController@create"]);
  Route::post('/administrator/manage-location/province-update', ["as"=>"backend_manage_location_province_update","uses"=>"Backend\Content\ManageLocation\ProvinceController@update"]);
  Route::post('/administrator/manage-location/province-destroy', ["as"=>"backend_manage_location_province_destroy","uses"=>"Backend\Content\ManageLocation\ProvinceController@destroy"]);
  //City
  Route::get('/administrator/manage-location/city', ["as"=>"backend_manage_location_city","uses"=>"Backend\Content\ManageLocation\CityController@index"]);
  Route::post('/administrator/manage-location/city-read', ["as"=>"backend_manage_location_city_read","uses"=>"Backend\Content\ManageLocation\CityController@read"]);
  Route::post('/administrator/manage-location/city-create', ["as"=>"backend_manage_location_city_create","uses"=>"Backend\Content\ManageLocation\CityController@create"]);
  Route::post('/administrator/manage-location/city-update', ["as"=>"backend_manage_location_city_update","uses"=>"Backend\Content\ManageLocation\CityController@update"]);
  Route::post('/administrator/manage-location/city-destroy', ["as"=>"backend_manage_location_city_destroy","uses"=>"Backend\Content\ManageLocation\CityController@destroy"]);
  //District
  Route::get('/administrator/manage-location/district', ["as"=>"backend_manage_location_district","uses"=>"Backend\Content\ManageLocation\DistrictController@index"]);
  Route::post('/administrator/manage-location/district-read', ["as"=>"backend_manage_location_district_read","uses"=>"Backend\Content\ManageLocation\DistrictController@read"]);
  Route::post('/administrator/manage-location/district-create', ["as"=>"backend_manage_location_district_create","uses"=>"Backend\Content\ManageLocation\DistrictController@create"]);
  Route::post('/administrator/manage-location/district-update', ["as"=>"backend_manage_location_district_update","uses"=>"Backend\Content\ManageLocation\DistrictController@update"]);
  Route::post('/administrator/manage-location/district-destroy', ["as"=>"backend_manage_location_district_destroy","uses"=>"Backend\Content\ManageLocation\DistrictController@destroy"]);
  Route::post('/administrator/manage-location/district-get-province', ["as"=>"backend_manage_location_district_get_province","uses"=>"Backend\Content\ManageLocation\DistrictController@get_province"]);
  Route::post('/administrator/manage-location/district-get-city', ["as"=>"backend_manage_location_district_get_city","uses"=>"Backend\Content\ManageLocation\DistrictController@get_city"]);

  //Manage Payment
  //Payment Method
  Route::get('/administrator/manage-payment/payment_method', ["as"=>"backend_manage_payment_method","uses"=>"Backend\Content\ManagePayment\PaymentMethodController@index"]);
  Route::post('/administrator/manage-payment/payment_method-read', ["as"=>"backend_manage_payment_method_read","uses"=>"Backend\Content\ManagePayment\PaymentMethodController@read"]);
  Route::post('/administrator/manage-payment/payment_method-update', ["as"=>"backend_manage_payment_method_update","uses"=>"Backend\Content\ManagePayment\PaymentMethodController@update"]);
  //Payment Method Location Mapping
  Route::get('/administrator/manage-payment/payment_method_location_mapping', ["as"=>"backend_manage_payment_method_location_mapping","uses"=>"Backend\Content\ManagePayment\LocationMappingController@index"]);
  Route::post('/administrator/manage-payment/payment_method_location_mapping-read', ["as"=>"backend_manage_payment_method_location_mapping_read","uses"=>"Backend\Content\ManagePayment\LocationMappingController@read"]);
  Route::post('/administrator/manage-payment/payment_method_location_mapping-create', ["as"=>"backend_manage_payment_method_location_mapping_create","uses"=>"Backend\Content\ManagePayment\LocationMappingController@create"]);
  Route::post('/administrator/manage-payment/payment_method_location_mapping-update', ["as"=>"backend_manage_payment_method_location_mapping_update","uses"=>"Backend\Content\ManagePayment\LocationMappingController@update"]);
  Route::post('/administrator/manage-payment/payment_method_location_mapping-destroy', ["as"=>"backend_manage_payment_method_location_mapping_destroy","uses"=>"Backend\Content\ManagePayment\LocationMappingController@destroy"]);
  Route::post('/administrator/manage-payment/payment_method_location_mapping-get-province', ["as"=>"backend_manage_payment_method_location_mapping_get_province","uses"=>"Backend\Content\ManagePayment\LocationMappingController@getProvince"]);
  Route::post('/administrator/manage-payment/payment_method_location_mapping-get-city', ["as"=>"backend_manage_payment_method_location_mapping_get_city","uses"=>"Backend\Content\ManagePayment\LocationMappingController@getCity"]);
  Route::post('/administrator/manage-payment/payment_method_location_mapping-get-district', ["as"=>"backend_manage_payment_method_location_mapping_get_district","uses"=>"Backend\Content\ManagePayment\LocationMappingController@getDistrict"]);

  //Manage Courier
  //Courier
  Route::get('/administrator/manage-courier/courier', ["as"=>"backend_manage_courier","uses"=>"Backend\Content\ManageCourier\CourierController@index"]);
  Route::post('/administrator/manage-courier/courier-read', ["as"=>"backend_manage_courier_read","uses"=>"Backend\Content\ManageCourier\CourierController@read"]);
  Route::post('/administrator/manage-courier/courier-update', ["as"=>"backend_manage_courier_update","uses"=>"Backend\Content\ManageCourier\CourierController@update"]);
  //Courier Package
  Route::get('/administrator/manage-courier/courier_package', ["as"=>"backend_manage_courier_package","uses"=>"Backend\Content\ManageCourier\CourierPackageController@index"]);
  Route::post('/administrator/manage-courier/courier_package-read', ["as"=>"backend_manage_courier_package_read","uses"=>"Backend\Content\ManageCourier\CourierPackageController@read"]);
  Route::post('/administrator/manage-courier/courier_package-update', ["as"=>"backend_manage_courier_package_update","uses"=>"Backend\Content\ManageCourier\CourierPackageController@update"]);
  Route::post('/administrator/manage-courier/courier_package-get-courier', ["as"=>"backend_manage_courier_package_get_courier","uses"=>"Backend\Content\ManageCourier\CourierPackageController@getCourier"]);
  //Courier Location Mapping
  Route::get('/administrator/manage-courier/courier_location_mapping', ["as"=>"backend_manage_courier_location_mapping","uses"=>"Backend\Content\ManageCourier\LocationMappingController@index"]);
  Route::post('/administrator/manage-courier/courier_location_mapping-read', ["as"=>"backend_manage_courier_location_mapping_read","uses"=>"Backend\Content\ManageCourier\LocationMappingController@read"]);
  Route::post('/administrator/manage-courier/courier_location_mapping-create', ["as"=>"backend_manage_courier_location_mapping_create","uses"=>"Backend\Content\ManageCourier\LocationMappingController@create"]);
  Route::post('/administrator/manage-courier/courier_location_mapping-destroy', ["as"=>"backend_manage_courier_location_mapping_destroy","uses"=>"Backend\Content\ManageCourier\LocationMappingController@destroy"]);
  Route::post('/administrator/manage-courier/courier_location_mapping-update', ["as"=>"backend_manage_courier_location_mapping_update","uses"=>"Backend\Content\ManageCourier\LocationMappingController@update"]);
  Route::post('/administrator/manage-courier/courier_location_mapping-get-province', ["as"=>"backend_manage_courier_location_mapping_get_province","uses"=>"Backend\Content\ManageCourier\LocationMappingController@getProvince"]);
  Route::post('/administrator/manage-courier/courier_location_mapping-get-city', ["as"=>"backend_manage_courier_location_mapping_get_city","uses"=>"Backend\Content\ManageCourier\LocationMappingController@getCity"]);
  Route::post('/administrator/manage-courier/courier_location_mapping-get-district', ["as"=>"backend_manage_courier_location_mapping_get_district","uses"=>"Backend\Content\ManageCourier\LocationMappingController@getDistrict"]);
  Route::post('/administrator/manage-courier/courier_location_mapping-get-courier', ["as"=>"backend_manage_courier_location_mapping_get_courier","uses"=>"Backend\Content\ManageCourier\LocationMappingController@getCourier"]);
  Route::post('/administrator/manage-courier/courier_location_mapping-get-courier-package', ["as"=>"backend_manage_courier_location_mapping_get_courier_package","uses"=>"Backend\Content\ManageCourier\LocationMappingController@getCourierPackage"]);
  //Courier Internal
  //Delivery Price
  Route::get('/administrator/manage-courier/internal/delivery_price', ["as"=>"backend_manage_courier_internal_delivery_price","uses"=>"Backend\Content\ManageCourier\Internal\DeliveryPriceController@index"]);
  Route::post('/administrator/manage-courier/internal/delivery_price-read', ["as"=>"backend_manage_courier_internal_delivery_price_read","uses"=>"Backend\Content\ManageCourier\Internal\DeliveryPriceController@read"]);
  Route::post('/administrator/manage-courier/internal/delivery_price-create', ["as"=>"backend_manage_courier_internal_delivery_price_create","uses"=>"Backend\Content\ManageCourier\Internal\DeliveryPriceController@create"]);
  Route::post('/administrator/manage-courier/internal/delivery_price-destroy', ["as"=>"backend_manage_courier_internal_delivery_price_destroy","uses"=>"Backend\Content\ManageCourier\Internal\DeliveryPriceController@destroy"]);
  Route::post('/administrator/manage-courier/internal/delivery_price-update', ["as"=>"backend_manage_courier_internal_delivery_price_update","uses"=>"Backend\Content\ManageCourier\Internal\DeliveryPriceController@update"]);
  Route::post('/administrator/manage-courier/internal/delivery_price-get-province', ["as"=>"backend_manage_courier_internal_delivery_price_get_province","uses"=>"Backend\Content\ManageCourier\Internal\DeliveryPriceController@getProvince"]);
  Route::post('/administrator/manage-courier/internal/delivery_price-get-city', ["as"=>"backend_manage_courier_internal_delivery_price_get_city","uses"=>"Backend\Content\ManageCourier\Internal\DeliveryPriceController@getCity"]);
  Route::post('/administrator/manage-courier/internal/delivery_price-get-district', ["as"=>"backend_manage_courier_internal_delivery_price_get_district","uses"=>"Backend\Content\ManageCourier\Internal\DeliveryPriceController@getDistrict"]);
  Route::post('/administrator/manage-courier/internal/delivery_price-get-courier-package', ["as"=>"backend_manage_courier_internal_delivery_price_get_courier_package","uses"=>"Backend\Content\ManageCourier\Internal\DeliveryPriceController@getCourierPackage"]);
  //GED
  //Price Category
  Route::get('/administrator/manage-courier/ged/price_category', ["as"=>"backend_manage_courier_ged_price_category","uses"=>"Backend\Content\ManageCourier\GED\PriceCategoryController@index"]);
  Route::post('/administrator/manage-courier/ged/price_category-read', ["as"=>"backend_manage_courier_ged_price_category_read","uses"=>"Backend\Content\ManageCourier\GED\PriceCategoryController@read"]);
  Route::post('/administrator/manage-courier/ged/price_category-update', ["as"=>"backend_manage_courier_ged_price_category_update","uses"=>"Backend\Content\ManageCourier\GED\PriceCategoryController@update"]);
  //Delivery Price
  Route::get('/administrator/manage-courier/ged/delivery_price', ["as"=>"backend_manage_courier_ged_delivery_price","uses"=>"Backend\Content\ManageCourier\GED\DeliveryPriceController@index"]);
  Route::post('/administrator/manage-courier/ged/delivery_price-read', ["as"=>"backend_manage_courier_ged_delivery_price_read","uses"=>"Backend\Content\ManageCourier\GED\DeliveryPriceController@read"]);
  Route::post('/administrator/manage-courier/ged/delivery_price-create', ["as"=>"backend_manage_courier_ged_delivery_price_create","uses"=>"Backend\Content\ManageCourier\GED\DeliveryPriceController@create"]);
  Route::post('/administrator/manage-courier/ged/delivery_price-destroy', ["as"=>"backend_manage_courier_ged_delivery_price_destroy","uses"=>"Backend\Content\ManageCourier\GED\DeliveryPriceController@destroy"]);
  Route::post('/administrator/manage-courier/ged/delivery_price-update', ["as"=>"backend_manage_courier_ged_delivery_price_update","uses"=>"Backend\Content\ManageCourier\GED\DeliveryPriceController@update"]);
  Route::post('/administrator/manage-courier/ged/delivery_price-get-province', ["as"=>"backend_manage_courier_ged_delivery_price_get_province","uses"=>"Backend\Content\ManageCourier\GED\DeliveryPriceController@getProvince"]);
  Route::post('/administrator/manage-courier/ged/delivery_price-get-city', ["as"=>"backend_manage_courier_ged_delivery_price_get_city","uses"=>"Backend\Content\ManageCourier\GED\DeliveryPriceController@getCity"]);
  Route::post('/administrator/manage-courier/ged/delivery_price-get-district', ["as"=>"backend_manage_courier_ged_delivery_price_get_district","uses"=>"Backend\Content\ManageCourier\GED\DeliveryPriceController@getDistrict"]);
  Route::post('/administrator/manage-courier/ged/delivery_price-get-courier-package', ["as"=>"backend_manage_courier_ged_delivery_price_get_courier_package","uses"=>"Backend\Content\ManageCourier\GED\DeliveryPriceController@getCourierPackage"]);
  Route::post('/administrator/manage-courier/ged/delivery_price-get-courier-price-category', ["as"=>"backend_manage_courier_ged_delivery_price_get_price_category","uses"=>"Backend\Content\ManageCourier\GED\DeliveryPriceController@getPriceCategory"]);
});
