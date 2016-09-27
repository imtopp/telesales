<?php

namespace App\Http\Controllers\Frontend;

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
use App\Models\Transaction as TransactionModel;
use App\Models\CustomerLocationProvince as CustomerLocationProvinceModel;
use App\Models\CustomerLocationCity as CustomerLocationCityModel;
use App\Models\CustomerLocationDistrict as CustomerLocationDistrictModel;
use App\Models\PaymentMethodLocationMapping as PaymentMethodLocationMappingModel;
use App\Models\PaymentMethod as PaymentMethodModel;
use App\Models\TotalPriceCategory as TotalPriceCategoryModel;
use App\Models\DeliveryPrice as DeliveryPriceModel;
use File;
use DateTime;
use Mail;

class ProductController extends BaseController
{
  use AuthorizesRequests, AuthorizesResources, DispatchesJobs, ValidatesRequests;

  //local function that find all active product or specific fg_code
  private function getActiveProduct($find_product_id = null,$find_fg_code = null){
    $all_data = array(); //initialize all active product array

    //trace active product model from category to fg_code
    $categories = ProductCategoryModel::where(['status'=>'active'])->get();
    foreach($categories as $category){ //looping active category
      if(!isset($find_product_id)){ //check if the request is not find on specific product_id
        $products = ProductModel::where(['status'=>'active','category_id'=>$category->id])->get();
        foreach($products as $product){ //looping active product
          $colours = ProductColourModel::where(['status'=>'active','product_id'=>$product->id])->get();
          foreach($colours as $colour){ //looping active colour
            //assumed there is only 1 fg_code active for 1 product colour and store it to all_data array.
            if(!isset($find_fg_code)){ //check if the request is not find on specific fg_code
              $fg_code = ProductFgCodeModel::where(['status'=>'active','product_colour_id'=>$colour->id])->first();
              if(isset($fg_code) && count($fg_code)!=0) //fg_code is available
              $all_data[] = array('category'=>$category,'product'=>$product,'colour'=>$colour,'fg_code'=>$fg_code);
            }else{ //finding specific fg_code
              $fg_code = ProductFgCodeModel::where(['status'=>'active','product_colour_id'=>$colour->id,'fg_code'=>$find_fg_code])->first();
              if(isset($fg_code) && count($fg_code)!=0){ //specific fg_code is found
                $all_data = array('category'=>$category,'product'=>$product,'colour'=>$colour,'fg_code'=>$fg_code);
                return $all_data; //stop the function and return the data after found;
              }
            }
          }
        }
      }else{
        $products = ProductModel::where(['status'=>'active','category_id'=>$category->id,'id'=>$find_product_id])->get();
        foreach($products as $product){ //looping active product
          $colours = ProductColourModel::where(['status'=>'active','product_id'=>$product->id])->get();
          foreach($colours as $colour){ //looping active colour
            //assumed there is only 1 fg_code active for 1 product colour and store it to all_data array.
            if(!isset($find_fg_code)){ //check if the request is not find on specific fg_code
              $fg_code = ProductFgCodeModel::where(['status'=>'active','product_colour_id'=>$colour->id])->first();
              if(isset($fg_code) && count($fg_code)!=0) //fg_code is available
              $all_data[] = array('category'=>$category,'product'=>$product,'colour'=>$colour,'fg_code'=>$fg_code);
            }else{ //finding specific fg_code
              $fg_code = ProductFgCodeModel::where(['status'=>'active','product_colour_id'=>$colour->id,'fg_code'=>$find_fg_code])->first();
              if(isset($fg_code) && count($fg_code)!=0){ //specific fg_code is found
                $all_data = array('category'=>$category,'product'=>$product,'colour'=>$colour,'fg_code'=>$fg_code);
                return $all_data; //stop the function and return the data after found;
              }
            }
          }
        }
      }
    }

    return $all_data; //return all data whether data is not found or request is collection of data
  }

  //public function for showing all product at frontpage
  public function showAllProduct(){
    $all_category = array(); //initialize all category array
    $all_data = $this->getActiveProduct(); //get all data array from getActiveProduct local function

    //creating all active product & category array by looping at all_data array
    foreach($all_data as $data){
      $all_category[] = array('name'=>$data['category']->name);

      $price = 0;
      $all_product_colour = $this->getActiveProduct($data['product']->id);
      foreach($all_product_colour as $colour){
        if($price==0){
          $price=$colour['fg_code']->price;
        }
        if($price>$colour['fg_code']->price){
          $price=$colour['fg_code']->price;
        }
      }
      $all_product[] = array('name'=>$data['product']->name,'category'=>$data['category']->name,'image_url'=>$data['product']->image_url,'id'=>$data['product']->id,'price'=>$price);
    }
    $all_category = array_unique($all_category,SORT_REGULAR); //create unique category
    $all_product = array_unique($all_product,SORT_REGULAR); //create unique product

    return view('frontend/list_product',['all_category'=>$all_category,'all_product'=>$all_product]); //display list_product view with all_category and all_product
  }

  //public funtion for showing detail of product at frontpage
  public function showProductDetail(){
    if(isset($_GET['id']) && $_GET['id']!="") //checking if there is fg_code at $_GET['id'] from URL
    $all_data = $this->getActiveProduct($_GET['id']); //getting active product from the fg_code
    else //when the $_GET['id'] is not set / there is no fg_code in URL
    $all_data = null; //set all_data null

    if(isset($all_data) && count($all_data)!=0){ //check if the all_data is set for value and it's count of value is not 0
      $colours = array();
      $colours_dropdown = array();
      $product_data = ProductModel::where(['status'=>'active','id'=>$_GET['id']])->first();
      $product_data->hit_count = ++$product_data->hit_count;
      $product_data->save();
      foreach($all_data as $data){
        $product = array('name'=>$data['product']->name,'image_url'=>$data['product']->image_url,'description'=>$data['product']->description);
        $colours[] = array('id'=>$data['colour']->id,'name'=>$data['colour']->name,'image_url'=>$data['colour']->image_url,'fg_code'=>$data['fg_code']->fg_code,'price'=>$data['fg_code']->price);
        $colours_dropdown[$data['colour']->id] = $data['colour']->name;
      }
      $message = null;
    }else{ //the fg_code is not found so it cant find the product or product not found
      $message = "Maaf produk yang anda cari tidak dapat ditemukan.";
    }

    return view('frontend/detail_product',['product'=>isset($product)?$product:null,'colours'=>isset($colours)?$colours:null,'colours_dropdown'=>isset($colours_dropdown)?$colours_dropdown:null,'message'=>$message]); //display detail_product view with product property and the message
  }

  //public function for showing form to customer when they want buy product
  public function showCustomerForm(){
    if(isset($_POST['fg_code']) && $_POST['fg_code']!="") //checking if there is fg_code at $_GET['id'] from URL
    $product = $this->getActiveProduct(null,$_POST['fg_code']); //getting active product from the fg_code
    else //when the $_GET['id'] is not set / there is no fg_code in URL
    $product = null; //set all_data null

    if(isset($product) && count($product)!=0){ //check if the all_data is set for value and it's count of value is not 0
      $data_product = array('name'=>$product['product']->name." - ".$product['colour']->name,'image_url'=>$product['colour']->image_url,'fg_code'=>$product['fg_code']->fg_code,'price'=>$product['fg_code']->price,'total_price'=>$_POST['price'],'qty'=>$_POST['qty']);
      #$payment_method = array(); //initialize array for payment_method
       //getting available payment type
      #foreach($payment_methods as $type){ //creating array of payment_method
      #  $payment_method[$type->id] = $type->name;
      #}
      $message = File::get('assets/disclaimer/disclaimer.txt'); //message for showing disclaimer
      $message_title = "Syarat & Ketentuan Smartfren Online Shop";
    }else{ //the fg_code is not found so it cant find the product or product not found
      $message = "Maaf produk yang anda cari tidak dapat ditemukan.";
      $message_title = "Product Tidak Ditemukan";
    }

    return view('frontend/customer_form',['product'=>isset($data_product)?$data_product:null,'message'=>$message,'message_title'=>$message_title]); //display customer_form for buying product with the product,payment_method and the message
  }

  public function getProvinceDropdown(){
    $province_list = array();
    $district_list = array();
    $mapping = PaymentMethodLocationMappingModel::get();
    $districts = CustomerLocationDistrictModel::get();
    foreach($mapping as $map){
      foreach($districts as $district){
        if($map->location_district_id==$district->id){
          $district_list[] = array("id"=>$district->id,"name"=>$district->name,"city_id"=>$district->city_id);
        }
      }
    }
    $district_list = array_unique($district_list,SORT_REGULAR);
    foreach($district_list as $district){
      $cities = CustomerLocationCityModel::where(["id"=>$district['city_id']])->get();
      foreach($cities as $city){
        $provinces = CustomerLocationProvinceModel::where(["id"=>$city->province_id])->get();
        foreach($provinces as $province){
          $province_list[$province->id] = $province->name;
        }
      }
    }
    $province_list = array_unique($province_list,SORT_REGULAR);
    return $province_list;
  }

  public function getCityDropdown(){
    if(isset($_POST['province_id'])){
      $city_list = array();
      $district_list = array();
      $mapping = PaymentMethodLocationMappingModel::get();
      $districts = CustomerLocationDistrictModel::get();
      foreach($mapping as $map){
        foreach($districts as $district){
          if($map->location_district_id==$district->id){
            $district_list[] = array("id"=>$district->id,"name"=>$district->name,"city_id"=>$district->city_id);
          }
        }
      }
      $district_list = array_unique($district_list,SORT_REGULAR);
      foreach($district_list as $district){
        $cities = CustomerLocationCityModel::where(["id"=>$district['city_id']])->get();
        foreach($cities as $city){
          $provinces = CustomerLocationProvinceModel::where(["id"=>$city->province_id])->get();
          foreach($provinces as $province){
            if($province->id==$_POST['province_id'])
              $city_list[$city->id] = $city->name;
          }
        }
      }
      $city_list = array_unique($city_list,SORT_REGULAR);
      return $city_list;
    }else{
      return null;
    }
  }

  public function getDistrictDropdown(){
    if(isset($_POST['city_id'])){
      $district_list = array();
      $mapping = PaymentMethodLocationMappingModel::get();
      $districts = CustomerLocationDistrictModel::where(["city_id"=>$_POST['city_id']])->get();
      foreach($mapping as $map){
        foreach($districts as $district){
          if($map->location_district_id==$district->id){
            $district_list[$district->id] = $district->name;
          }
        }
      }
      $district_list = array_unique($district_list,SORT_REGULAR);
      return $district_list;
    }else{
      return null;
    }
  }

  public function getPaymentMethodDropdown(){
    if(isset($_POST['district'])){
      $models = PaymentMethodLocationMappingModel::where(['location_district_id'=>$_POST['district']])->get();
      $payment_method = array();
      foreach ($models as $model){
        $payment = PaymentMethodModel::where(['status'=>'active','id'=>$model->payment_method_id])->first();
        $payment_method[$payment->id] = $payment->name;
      }
      return $payment_method;
    }else{
      return null;
    }
  }

  public function getDeliveryPrice(){
    if(isset($_POST['payment_method'])){
      $qty = $_POST['qty'];
      $payment_method_location_mapping = PaymentMethodLocationMappingModel::where(['location_district_id'=>$_POST['district'],'payment_method_id'=>$_POST['payment_method']])->first();
      $fg_code = ProductFgCodeModel::where(['status'=>'active','fg_code'=>$_POST['fg_code']])->first();
      $total_price_category = TotalPriceCategoryModel::where('min_price','<=',$fg_code->price*$qty)->where(function($query)use($fg_code,$qty){return $query->where('max_price','>=',$fg_code->price*$qty)->orWhere('max_price','=','0');})->first();
      $delivery_price = DeliveryPriceModel::where(['payment_method_location_mapping_id'=>$payment_method_location_mapping->id,'total_price_category_id'=>$total_price_category->id])->first();

      return response()->json(["delivery_price"=>$delivery_price->price]);
    }else{
      return null;
    }
  }

  //public function for storing customer form input when they want buy product
  public function storeCustomerForm(){
    $customer_info = new CustomerInfoModel; //creating model for customer_info
    $transaction = new TransactionModel; //creating model for transaction
    $date = DateTime::createFromFormat("Y-m-d H:i:s", date("Y-m-d H:i:s")); //initialize date parameter
    $fg_codes = ProductFgCodeModel::where(['status'=>'active','fg_code'=>$_POST['user_form']['fg_code']])->first(); //find fg_code from the model
    $key = "t3rs3r@h"; //key for encryption
    if(isset($_POST) && count($_POST)!=0){
      $payment_method = PaymentMethodModel::where(['id'=>$_POST['user_form']['payment_method']])->first(); //find payment_method model

      //fill customer_info model
      $customer_info->name = $this->encrypt($key,$_POST['user_form']['name']);
      $customer_info->address = $this->encrypt($key,$_POST['user_form']['address']);
      $customer_info->identity_type = $this->encrypt($key,$_POST['user_form']['identity_type']);
      $customer_info->identity_number = $this->encrypt($key,$_POST['user_form']['identity_number']);
      $customer_info->email = $this->encrypt($key,$_POST['user_form']['email']);
      $customer_info->mdn = $this->encrypt($key,$_POST['user_form']['mdn']);
      $customer_info->location_district_id = $_POST['user_form']['district'];
      $customer_info->delivery_address = $this->encrypt($key,$_POST['user_form']['delivery_address']);
      $customer_info->input_date = $date->format("Y-m-d H:i:s");
      $customer_info->input_by = "System";
      $customer_info->input_date = $date->format("Y-m-d H:i:s");
      $customer_info->update_by = "System";

      try {
        $success = $customer_info->save(); //save the customer_info model to database
      } catch (Exception $ex) {
        $success = false;
        $message = $ex->getMessage();
      }

      $qty=$_POST['user_form']['qty'];
      $total_price_category = TotalPriceCategoryModel::where('min_price','<=',$fg_codes->price*$qty)->where(function($query)use($fg_codes,$qty){return $query->where('max_price','>=',$fg_codes->price*$qty)->orWhere('max_price','=','0');})->first();
      //fill transaction model
      $transaction->customer_info_id = $customer_info->id;
      $transaction->product_fg_code_id = $fg_codes->id;
      $transaction->qty = $qty;
      $transaction->payment_method_id = $payment_method->id;
      $transaction->total_price_category_id = $total_price_category->id;
      $transaction->input_date = $date->format("Y-m-d H:i:s");
      $transaction->input_by = "System";
      $transaction->input_date = $date->format("Y-m-d H:i:s");
      $transaction->update_by = "System";

      Mail::send('frontend.emails.transaction_notification_administrator', ['customer'=>'MUKIDI','product'=>'Hape baru','fg_code'=>'1432151'], function($msg) {
         $msg->from('admin@'.config('settings.app_name').'.com', config('settings.app_name'));
         $msg->to("taufiq.putra@smartfren.com", 'taufiq okta pratama putra')->subject('Transaction notifications');
      });

      Mail::send('frontend.emails.transaction_notification_customer', ['product'=>'Hape baru'], function($msg) {
         $msg->from('admin@'.config('settings.app_name').'.com', config('settings.app_name'));
         $msg->to("taufiq.putra@smartfren.com", 'taufiq okta pratama putra')->subject('Transaction notifications');
      });

      try {
        $success = $transaction->save();
        $message = "Tim Kami Akan Mengghubungi anda dalam 1x24 Jam.";
      } catch (Exception $ex) {
        $success = false;
        $message = $ex->getMessage();
      }

      return response()->json(["success"=>$success,"message"=>$message]);
    }

  }

  public function checkData(){
    $transactions = TransactionModel::get();
    foreach($transactions as $transaction){
      $customer_info = CustomerInfoModel::where(["id"=>$transaction->customer_info_id])->get();
      $key = "t3rs3r@h";
      foreach($customer_info as $info){
        $item = ProductFgCodeModel::where(["id"=>$transaction->product_fg_code_id])->first();
        $colour = ProductColourModel::where(['id'=>$item->product_colour_id])->first();
        $product = ProductModel::where(['id'=>$colour->product_id])->first();
        $district = CustomerLocationDistrictModel::where(['id'=>$info->location_district_id])->first();
        $city = CustomerLocationCityModel::where(['id'=>isset($district->city_id)?$district->city_id:null])->first();
        $province = CustomerLocationProvinceModel::where(['id'=>isset($city->province_id)?$city->province_id:null])->first();
        $payment_method = PaymentMethodModel::where(['id'=>$transaction->payment_method_id])->first();
        $mapping = PaymentMethodLocationMappingModel::where(['payment_method_id'=>isset($payment_method->id)?$payment_method->id:null,'location_district_id'=>isset($district->id)?$district->id:null])->first();
        $total_price_category = TotalPriceCategoryModel::where('min_price','<=',$item->price*$transaction->qty)->where(function($query)use($item,$transaction){return $query->where('max_price','>=',$item->price*$transaction->qty)->orWhere('max_price','=','0');})->first();
        $delivery_price = DeliveryPriceModel::where(['payment_method_location_mapping_id'=>isset($mapping->id)?$mapping->id:null,'total_price_category_id'=>$total_price_category->id])->first();
        echo "Customer Name : ".$this->decrypt($key,$info->name)."<br/>";
        echo "Address : ".$this->decrypt($key,$info->address)."<br/>";
        echo "Identity Type : ".$this->decrypt($key,$info->identity_type)."<br/>";
        echo "Identity Number : ".$this->decrypt($key,$info->identity_number)."<br/>";
        echo "Email : ".$this->decrypt($key,$info->email)."<br/>";
        echo "MDN : ".$this->decrypt($key,$info->mdn)."<br/>";
        echo "Province : ".(isset($province->name)?$province->name:null)."<br/>";
        echo "City : ".(isset($city->name)?$city->name:null)."<br/>";
        echo "District : ".(isset($district->name)?$district->name:null)."<br/>";
        echo "Delivery Address : ".$this->decrypt($key,$info->delivery_address)."<br/>";
        echo "Product : ".$product->name." ".$colour->name."<br/>";
        echo "Product Price : Rp ".$item->price."<br/>";
        echo "Total QTY : ".$transaction->qty." unit<br/>";
        echo "Payment Method : ".(isset($payment_method->name)?$payment_method->name:null)."<br/>";
        echo "Delivery Price : ".(isset($delivery_price->price)?$delivery_price->price:null)."<br/>";
        echo "Total Price : Rp ".(($item->price*$transaction->qty)+(isset($delivery_price->price)?$delivery_price->price:0))."<br/>";
        echo "<br/>";
        echo "<br/>";
      }
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
