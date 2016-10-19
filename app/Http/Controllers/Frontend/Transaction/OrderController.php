<?php

namespace App\Http\Controllers\Frontend\Transaction;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesResources;
use App\Models\ProductFgCode as ProductFgCodeModel;
use App\Models\ProductColour as ProductColourModel;
use App\Models\Product as ProductModel;
use App\Models\ProductCategory as ProductCategoryModel;
use App\Models\CustomerInfo as CustomerInfoModel;
use App\Models\LocationProvince as LocationProvinceModel;
use App\Models\LocationCity as LocationCityModel;
use App\Models\LocationDistrict as LocationDistrictModel;
use App\Models\Courier as CourierModel;
use App\Models\CourierPackage as CourierPackageModel;
use App\Models\CourierLocationMapping as CourierLocationMappingModel;
use App\Models\CourierPriceCategory as CourierPriceCategoryModel;
use App\Models\CourierDeliveryPrice as CourierDeliveryPriceModel;
use App\Models\PaymentMethod as PaymentMethodModel;
use App\Models\PaymentMethodLocationMapping as PaymentMethodLocationMappingModel;
use App\Models\ViewActiveProduct as ViewActiveProductModel;
use App\Models\ViewActiveLocation as ViewActiveLocationModel;
use App\Models\Transaction as TransactionModel;
use App\Models\TransactionStatus as TransactionStatusModel;
use File;
use DateTime;
use Mail;
use Request;

class OrderController extends BaseController
{
  use AuthorizesRequests, AuthorizesResources, DispatchesJobs, ValidatesRequests;

  //public function for showing form to customer when they want buy product
  public function index(){
    if(isset($_GET['colour_id']) && $_GET['colour_id']!=""){
      $fg_code = $_GET['colour_id'];

      $product = ViewActiveProductModel::where(['fg_code'=>$fg_code])->first();
      $data_product = array('name'=>$product->product.' - '.$product->colour,'image_url'=>$product->colour_image_url,'fg_code'=>$product->fg_code,'price'=>$product->price);

      $message = File::get('assets/disclaimer/disclaimer.txt'); //message for showing disclaimer
      $message_title = "Syarat & Ketentuan Smartfren Online Shop";
    }else{ //the fg_code is not found so it cant find the product or product not found
      $message = "Maaf produk yang anda cari tidak dapat ditemukan.";
      $message_title = "Product Tidak Ditemukan";
    }

    return view('frontend/transaction/order',['product'=>isset($data_product)?$data_product:null,'message'=>$message,'message_title'=>$message_title]); //display customer_form for buying product with the product,payment_method and the message
  }

  public function getProvinceDropdown(){
    return ViewActiveLocationModel::lists('province','province_id');
  }

  public function getCityDropdown(){
    if(isset($_POST['province_id'])){
      $city = ViewActiveLocationModel::where(['province_id'=>$_POST['province_id']])->lists('city','city_id');
    }else{
      $city = null;
    }

    return $city;
  }

  public function getDistrictDropdown(){
    if(isset($_POST['city_id'])){
      $district = ViewActiveLocationModel::where(['city_id'=>$_POST['city_id']])->lists('district','district_id');
    }else{
      $district = null;
    }

    return $district;
  }

  public function getPaymentMethodDropdown(){
    if(isset($_POST['district_id'])){
      $payment_method = PaymentMethodModel::join('payment_method_location_mapping','payment_method_location_mapping.payment_method_id','=','payment_method.id')
                                          ->join('view_active_location','view_active_location.district_id','=','payment_method_location_mapping.location_district_id')
                                          ->where(['view_active_location.district_id'=>$_POST['district_id']])
                                          ->lists('payment_method.name','payment_method.id');
    }else{
      $payment_method = null;
    }

    return $payment_method;
  }

  public function getCourierDropdown(){
    if(isset($_POST['district_id']) && isset($_POST['payment_method_id'])){
      $payment_method = PaymentMethodModel::where(['id'=>$_POST['payment_method_id']])->first();

      $courier = CourierModel::join('courier_package','courier_package.courier_id','=','courier.id')
                            ->join('courier_location_mapping','courier_location_mapping.courier_package_id','=','courier_package.id')
                            ->join('view_active_location','view_active_location.district_id','=','courier_location_mapping.location_district_id')
                            ->where(['view_active_location.district_id'=>$_POST['district_id']])
                            ->whereRaw($payment_method->id=='1'?'courier.id="1"':'courier.id<>"1"')
                            ->lists('courier.name','courier.id');
    }else{
      $courier = null;
    }
    return $courier;
  }

  public function getCourierPackageDropdown(){
    if(isset($_POST['district_id']) && isset($_POST['courier_id'])){
      $courier_package = CourierPackageModel::join('courier_location_mapping','courier_location_mapping.courier_package_id','=','courier_package.id')
                                            ->join('courier','courier.id','=','courier_package.courier_id')
                                            ->join('view_active_location','view_active_location.district_id','=','courier_location_mapping.location_district_id')
                                            ->where(['view_active_location.district_id'=>$_POST['district_id']])
                                            ->where(['courier.id'=>$_POST['courier_id']])
                                            ->lists('courier_package.name','courier_package.id');
    }else{
      $courier_package = null;
    }

    return $courier_package;
  }

  public function getDeliveryPrice($courier_package_id=NULL,$district_id=NULL,$fg_code=NULL){
    if(Request::ajax() && isset($_POST) && count($_POST)!=0){
      $data = $_POST;
    }else if(isset($courier_package_id) && isset($district_id) && isset($fg_code)){
      $data['courier_package_id'] = $courier_package_id;
      $data['district_id'] = $district_id;
      $data['fg_code'] = $fg_code;
    }

    if(isset($data['courier_package_id']) && isset($data['district_id']) && isset($data['fg_code'])){
      $courier_location_mapping = CourierLocationMappingModel::where(['courier_package_id'=>$data['courier_package_id'],'location_district_id'=>$data['district_id']])->first();

      $product = ProductFgCodeModel::where(['fg_code'=>$data['fg_code']])->first();
      $price_category = CourierPriceCategoryModel::where('status','=','active')
                                                    ->whereRaw('min_price <='.$product->price.' AND (max_price >= '.$product->price.' OR max_price = 0)')
                                                    ->first();
      $delivery_price = CourierDeliveryPriceModel::where(['courier_location_mapping_id'=>$courier_location_mapping->id,'courier_price_category_id'=>$price_category->id])
                                                    ->first();

      return response()->json(["delivery_price"=>isset($delivery_price->price)?$delivery_price->price:"Null"]);
    }else{
      return null;
    }
  }

  //public function for storing customer form input when they want buy product
  public function checkout(){
    $date = DateTime::createFromFormat("Y-m-d H:i:s", date("Y-m-d H:i:s")); //initialize date parameter
    $key = "t3rs3r@h"; //key for encryption

    if(isset($_POST) && count($_POST)!=0){
      $customer_info = new CustomerInfoModel; //creating model for customer_info

      //fill customer_info model
      $customer_info->name = $this->encrypt($key,$_POST['name']);
      $customer_info->address = $this->encrypt($key,$_POST['address']);
      $customer_info->identity_type = $this->encrypt($key,$_POST['identity_type']);
      $customer_info->identity_number = $this->encrypt($key,$_POST['identity_number']);
      $customer_info->email = $this->encrypt($key,$_POST['email']);
      $customer_info->mdn = $this->encrypt($key,$_POST['mdn']);
      $customer_info->location_district_id = $_POST['district_id'];
      $customer_info->delivery_address = $this->encrypt($key,$_POST['delivery_address']);
      $customer_info->input_date = $date->format("Y-m-d H:i:s");
      $customer_info->input_by = "Self Order";
      $customer_info->update_date = $date->format("Y-m-d H:i:s");
      $customer_info->update_by = "Self Order";

      try {
        $success = $customer_info->save(); //save the customer_info model to database
      } catch (Exception $ex) {
        $success = false;
        $message = $ex->getMessage();
      }

      if($success){
        $location = ViewActiveLocationModel::where(['district_id'=>$_POST['district_id']])->first();
        $product = ViewActiveProductModel::where(['fg_code'=>$_POST['fg_code']])->first();
        $payment_method = PaymentMethodModel::where(['id'=>$_POST['payment_method_id']])->first(); //find payment_method model
        $courier = CourierModel::where(['id'=>$_POST['courier_id']])->first();
        $courier_package = CourierPackageModel::where(['id'=>$_POST['courier_package_id']])->first();
        $delivery = $this->getDeliveryPrice($_POST['courier_package_id'],$_POST['district_id'],$_POST['fg_code'])->getData();
        $total_transaction = TransactionModel::whereRaw('DATE(input_date)=DATE(CURRENT_TIMESTAMP)')->count();
        $transaction = new TransactionModel; //creating model for transaction

        $transaction->customer_name = $customer_info->name;
        $transaction->customer_address = $customer_info->address;
        $transaction->customer_identity_type = $customer_info->identity_type;
        $transaction->customer_identity_number = $customer_info->identity_number;
        $transaction->customer_email = $customer_info->email;
        $transaction->customer_mdn = $customer_info->mdn;
        $transaction->customer_location_province = $location->province;
        $transaction->customer_location_city = $location->city;
        $transaction->customer_location_district = $location->district;
        $transaction->customer_delivery_address = $customer_info->delivery_address;
        $transaction->product_category = $product->category;
        $transaction->product_name = $product->product;
        $transaction->product_colour = $product->colour;
        $transaction->product_fg_code = $product->fg_code;
        $transaction->product_price = $product->price;
        $transaction->payment_method = $payment_method->name;
        $transaction->courier = $courier->name;
        $transaction->courier_package = $courier_package->name;
        $transaction->delivery_price = $delivery->delivery_price;
        $transaction->total_price = $product->price+$delivery->delivery_price;
        $transaction->refference_number = $date->format("ymd").++$total_transaction;
        $transaction->input_date = $date->format("Y-m-d H:i:s");
        $transaction->input_by = "Self Order";
        $transaction->update_date = $date->format("Y-m-d H:i:s");
        $transaction->update_by = "Self Order";

        try {
          $success = $transaction->save();
          $message = "Tim Kami Akan Mengghubungi anda dalam 1x24 Jam.";
        } catch (Exception $ex) {
          $success = false;
          $message = $ex->getMessage();
        }
      }

      if($success){
        $transaction_status = new TransactionStatusModel;

        $transaction_status->transaction_id = $transaction->id;
        $transaction_status->status = "Order Received";
        $transaction_status->input_date = $date->format("Y-m-d H:i:s");
        $transaction_status->input_by = "Self Order";
        $transaction_status->update_date = $date->format("Y-m-d H:i:s");
        $transaction_status->update_by = "Self Order";
        try {
          $success = $transaction_status->save();
        } catch (Exception $ex) {
          $success = false;
          $message = $ex->getMessage();
        }
      }

      if($success){
        Mail::send('frontend.emails.transaction_notification_administrator', ['customer_name'=>$_POST['name'],'customer_mdn'=>$_POST['mdn'],'delivery_address'=>$_POST['delivery_address'].", ".$location->district.", ".$location->city.", ".$location->province."."], function($msg) {
           $msg->from('administrator-'.str_replace(' ','_',strtolower(config('settings.app_name'))).'@smartfren.com', "Administrator - ".config('settings.app_name'));
           $msg->to("taufiq.putra@smartfren.com", 'taufiq okta pratama putra')->subject('Transaction notifications');
        });

        Mail::send('frontend.emails.transaction_notification_customer', [], function($msg) {
          $msg->from('administrator-'.str_replace(' ','_',strtolower(config('settings.app_name'))).'@smartfren.com', "Administrator - ".config('settings.app_name'));
          $msg->to($_POST['email'], $_POST['name'])->subject('Transaction notifications');
        });
      }

      return response()->json(["success"=>$success,"message"=>$message]);
    }
  }

  private function encrypt($key,$string){
    $iv = mcrypt_create_iv(
      mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC),
      MCRYPT_DEV_URANDOM
    );

    $encrypted = base64_encode(
    $iv .
    mcrypt_encrypt(
    MCRYPT_RIJNDAEL_128,
    hash('sha256',$key,true),
    $string,
    MCRYPT_MODE_CBC,
    $iv
    )
  );

  return $encrypted;
  }

  private function decrypt($key,$encrypted){
    $data = base64_decode($encrypted);
    $iv = substr($data, 0, mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128,MCRYPT_MODE_CBC));

    $decrypted = rtrim(
      mcrypt_decrypt(
        MCRYPT_RIJNDAEL_128,
        hash('sha256',$key,true),
        substr($data, mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC)),
        MCRYPT_MODE_CBC,
        $iv
      ),
      "\0"
    );

    return $decrypted;
  }
}
