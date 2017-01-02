<?php

namespace App\Http\Controllers\Backend\Telesales\Content\ManageOrder;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesResources;
use Illuminate\Support\Facades\Auth;
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
use App\Models\Transaction as TransactionModel;
use App\Models\TransactionStatus as TransactionStatusModel;
use App\Models\ViewActiveProduct as ViewActiveProductModel;
use App\Models\ViewActiveLocation as ViewActiveLocationModel;
use App\Models\ViewActivePaymentMethod as ViewActivePaymentMethodModel;
use App\Models\ViewActiveCourier as ViewActiveCourierModel;
use DateTime;
use DB;
use URL;
use Mail;
use Request;

class OrderController extends BaseController
{
  use AuthorizesRequests, AuthorizesResources, DispatchesJobs, ValidatesRequests;

  //Render Page
  public function index(){
    return view('backend/telesales/content/order/order');
  }

  //Read All Data
  public function read(){
    // storing  request (ie, get/post) global array to a variable
    $requestData= $_REQUEST;
    $key = "t3rs3r@h"; //key for encryption

    $columns = array(
    // datatable column index  => database column name
        0 => 'refference_number',
        1 => 'customer_name',
        2 => 'customer_address',
        3 => 'customer_identity_type',
        4 => 'customer_identity_number',
        5 => 'customer_email',
        6 => 'customer_mdn',
        7 => 'customer_location_province',
        8 => 'customer_location_city',
        9 => 'customer_location_district',
        10 => 'customer_delivery_address',
        11 => 'product_category',
        12 => 'product_name',
        13 => 'product_colour',
        14 => 'product_fg_code',
        15 => 'product_price',
        16 => 'payment_method',
        17 => 'courier',
        18 => 'courier_package',
        19 => 'delivery_price',
        20 => 'total_price',
        21 => 'status',
        22 => 'payment_number',
        23 => 'airwaybill'
    );

    $model = TransactionModel::select('transaction.*','transaction_status.status')
                              ->join(DB::raw('(SELECT transaction_status.* FROM transaction_status LEFT JOIN (SELECT transaction_id,MAX(input_date) AS input_date FROM transaction_status GROUP BY transaction_id) x ON transaction_status.transaction_id = x.transaction_id AND transaction_status.input_date = x.input_date WHERE x.input_date IS NOT NULL) transaction_status'),'transaction_status.transaction_id','=','transaction.id')
                              ->where('transaction.input_by','=',Auth::User()->email);

    $totalData = $model->count();
    $totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

    if( !empty($requestData['search']['value']) ) {
      // if there is a search parameter
      $model = $model
                ->whereRaw('(decrypt_data("'.$key.'",`customer_name`) LIKE "'.$requestData['search']['value'].'%"')
                ->orWhereRaw('decrypt_data("'.$key.'",customer_address) LIKE "'.$requestData['search']['value'].'%"')
                ->orWhereRaw('decrypt_data("'.$key.'",customer_identity_type) LIKE "'.$requestData['search']['value'].'%"')
                ->orWhereRaw('decrypt_data("'.$key.'",customer_identity_number) LIKE "'.$requestData['search']['value'].'%"')
                ->orWhereRaw('decrypt_data("'.$key.'",customer_email) LIKE "'.$requestData['search']['value'].'%"')
                ->orWhereRaw('decrypt_data("'.$key.'",customer_mdn) LIKE "'.$requestData['search']['value'].'%"')
                ->orWhere('customer_location_province','LIKE',$requestData['search']['value'].'%')
                ->orWhere('customer_location_city','LIKE',$requestData['search']['value'].'%')
                ->orWhere('customer_location_district','LIKE',$requestData['search']['value'].'%')
                ->orWhereRaw('decrypt_data("'.$key.'",customer_delivery_address) LIKE "'.$requestData['search']['value'].'%"')
                ->orWhere('product_category','LIKE',$requestData['search']['value'].'%')
                ->orWhere('product_name','LIKE',$requestData['search']['value'].'%')
                ->orWhere('product_colour','LIKE',$requestData['search']['value'].'%')
                ->orWhere('product_fg_code','LIKE',$requestData['search']['value'].'%')
                ->orWhere('payment_method','LIKE',$requestData['search']['value'].'%')
                ->orWhere('courier','LIKE',$requestData['search']['value'].'%')
                ->orWhere('courier_package','LIKE',$requestData['search']['value'].'%')
                ->orWhere('delivery_price','LIKE',$requestData['search']['value'].'%')
                ->orWhere('refference_number','LIKE',$requestData['search']['value'].'%')
                ->orWhereRaw('`status` LIKE "'.$requestData['search']['value'].'%")');

      $totalFiltered = $model->count();
    }

    $query = $model
              ->orderBy($columns[$requestData['order'][0]['column']],$requestData['order'][0]['dir'])
              ->skip($requestData['start'])
              ->take($requestData['length'])
              ->get();

    $data = array();

    $date = DateTime::createFromFormat("Y-m-d H:i:s", date("Y-m-d H:i:s")); //initialize date parameter

    foreach($query as $row) {  // preparing an array
        $nestedData=array();

        $nestedData[$columns[0]] = $row->refference_number;
        $nestedData[$columns[1]] = $this->decrypt($key,$row->customer_name);
        $nestedData[$columns[2]] = $this->decrypt($key,$row->customer_address);
        $nestedData[$columns[3]] = $this->decrypt($key,$row->customer_identity_type);
        $nestedData[$columns[4]] = $this->decrypt($key,$row->customer_identity_number);
        $nestedData[$columns[5]] = $this->decrypt($key,$row->customer_email);
        $nestedData[$columns[6]] = $this->decrypt($key,$row->customer_mdn);
        $nestedData[$columns[7]] = $row->customer_location_province;
        $nestedData[$columns[8]] = $row->customer_location_city;
        $nestedData[$columns[9]] = $row->customer_location_district;
        $nestedData[$columns[10]] = $this->decrypt($key,$row->customer_delivery_address);
        $nestedData[$columns[11]] = $row->product_category;
        $nestedData[$columns[12]] = $row->product_name;
        $nestedData[$columns[13]] = $row->product_colour;
        $nestedData[$columns[14]] = $row->product_fg_code;
        $nestedData[$columns[15]] = "Rp ".number_format($row->product_price,0,",",".");;
        $nestedData[$columns[16]] = $row->payment_method;
        $nestedData[$columns[17]] = $row->courier;
        $nestedData[$columns[18]] = $row->courier_package;
        $nestedData[$columns[19]] = "Rp ".number_format($row->delivery_price,0,",",".");
        $nestedData[$columns[20]] = "Rp ".number_format($row->total_price,0,",",".");
        $nestedData[$columns[21]] = $row->status;
        $nestedData[$columns[22]] = $row->payment_number;
        $nestedData[$columns[23]] = $row->airwaybill;
        if($date->format("Y-m-d") == date("Y-m-d",strtotime($row->input_date)) && $row->status == "Order Received"){
          $nestedData['action'] = '<td><center>
                             <a data-id="'.$row->id.'" data-refference_number="'.$row->refference_number.'" data-toggle="tooltip" title="Cancel Order" class="btn btn-sm btn-danger" onClick="cancel(this)"> <span class="fa-stack fa-lg"><i class="fa fa-cart-plus fa-stack-1x"></i><i class="fa fa-ban fa-stack-2x text-danger"></i></span> </a>
                             </center></td>';
        }else{
          $nestedData['action'] = '';
        }

        $data[] = $nestedData;
    }

    $json_data = array(
      "draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
      "recordsTotal"    => intval( $totalData ),  // total number of records
      "recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
      "data"            => $data   // total data array
    );

    return response()->json($json_data);
  }

  public function create(){
    $date = DateTime::createFromFormat("Y-m-d H:i:s", date("Y-m-d H:i:s")); //initialize date parameter
    $key = "t3rs3r@h"; //key for encryption

    if(isset($_POST) && count($_POST)!=0){
      try{
        DB::beginTransaction();
        $fg_code = ProductFgCodeModel::where(['fg_code'=>$_POST['fg_code']])->lockForUpdate()->first();
        if($fg_code->stock > 0){
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
          $customer_info->input_by = Auth::User()->email;
          $customer_info->update_date = $date->format("Y-m-d H:i:s");
          $customer_info->update_by = Auth::User()->email;

          try {
            $success = $customer_info->save(); //save the customer_info model to database
          } catch (Exception $ex) {
            DB::rollback();
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
            $payment_number = $payment_method->id == 2 ? "8899".$date->format("dmy").Auth::User()->id.str_pad(++$total_transaction,3,"0",STR_PAD_LEFT) : $date->format("ymd").++$total_transaction;

            $transaction->customer_id = $customer_info->id;
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
            $transaction->product_fg_code_id = $product->fg_code_id;
            $transaction->product_price = $product->price;
            $transaction->payment_method = $payment_method->name;
            $transaction->payment_method_id = $payment_method->id;
            $transaction->courier = $courier->name;
            $transaction->courier_package = $courier_package->name;
            $transaction->courier_package_id = $courier_package->id;
            $transaction->delivery_price = $delivery->delivery_price;
            $transaction->total_price = $product->price+$delivery->delivery_price;
            $transaction->refference_number = $date->format("ymd").++$total_transaction;
            $transaction->payment_number = $payment_number;
            $transaction->input_date = $date->format("Y-m-d H:i:s");
            $transaction->input_by = Auth::User()->email;
            $transaction->update_date = $date->format("Y-m-d H:i:s");
            $transaction->update_by = Auth::User()->email;

            try {
              $success = $transaction->save();
              $message = "New transaction is created successfully!.".($payment_method->id == 2 ? " The Virtual Account number was ".$transaction->payment_number : "");
            } catch (Exception $ex) {
              DB::rollback();
              $success = false;
              $message = $ex->getMessage();
            }
          }

          if($success){
            $transaction_status = new TransactionStatusModel;

            $transaction_status->transaction_id = $transaction->id;
            $transaction_status->status = "Order Received";
            $transaction_status->input_date = $date->format("Y-m-d H:i:s");
            $transaction_status->input_by = Auth::User()->email;
            $transaction_status->update_date = $date->format("Y-m-d H:i:s");
            $transaction_status->update_by = Auth::User()->email;
            try {
              $success = $transaction_status->save();
            } catch (Exception $ex) {
              DB::rollback();
              $success = false;
              $message = $ex->getMessage();
            }
          }

          if($success){
            $fg_code->stock -= 1;

            try {
              $success = $fg_code->save();
            } catch (Exception $ex) {
              DB::rollback();
              $success = false;
              $message = $ex->getMessage();
            }
          }

          if($success){
            DB::commit();
          }

          /*if($success){
            Mail::send('backend.telesales.emails.transaction_notification_administrator', ['customer_name'=>$_POST['name'],'customer_mdn'=>$_POST['mdn'],'delivery_address'=>$_POST['delivery_address'].", ".$location->district.", ".$location->city.", ".$location->province."."], function($msg) {
               $msg->from('administrator-'.str_replace(' ','_',strtolower(config('settings.app_name'))).'@smartfren.com', "Administrator - ".config('settings.app_name'));
               $msg->to(config('settings.digital_iot_email'), 'Digital & IOT Team')->subject('Transaction notifications');
            });

            Mail::send('backend.telesales.emails.transaction_notification_customer', [], function($msg) {
              $msg->from('administrator-'.str_replace(' ','_',strtolower(config('settings.app_name'))).'@smartfren.com', "Administrator - ".config('settings.app_name'));
              $msg->to($_POST['email'], $_POST['name'])->subject('Transaction notifications');
            });
          }*/
        }else{
          $success = false;
          $message = "Maaf stock barang sudah SOLD OUT.";
        }
      } catch (Exception $ex) {
        $success = false;
        $message = $ex->getMessage();
      }

      return response()->json(["success"=>$success,"message"=>$message]);
    }
  }

  public function cancelOrder(){
    $date = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s')); //initialize date parameter
    DB::beginTransaction();
    $transaction_status = new TransactionStatusModel;

    $transaction_status->transaction_id = $_POST['id'];
    $transaction_status->status = "Order Canceled";
    $transaction_status->input_date = $date->format('Y-m-d H:i:s');
    $transaction_status->input_by = Auth::User()->email;
    $transaction_status->update_date = $date->format('Y-m-d H:i:s');
    $transaction_status->update_by = Auth::User()->email;

    try {
      $success = $transaction_status->save();
    } catch (Exception $ex) {
      DB::rollback();
      $success = false;
      $message = $ex->getMessage();
    }

    if($success){
      $transaction = TransactionModel::where('id','=',$_POST['id'])->first();

      $fg_code = ProductFgCodeModel::where('fg_code','=',$transaction->product_fg_code)->lockForUpdate()->first();
      $fg_code->stock+=1;

      try {
        $success = $fg_code->save();
        $message = 'Cancel order is success!';
      } catch (Exception $ex) {
        DB::rollback();
        $success = false;
        $message = $ex->getMessage();
      }
    }

    if($success){
      DB::commit();
    }

    return response()->json(['success'=>$success,'message'=>$message]);
  }

  public function getCategory(){
    return ViewActiveProductModel::lists('category','category_id');
  }

  public function getProduct(){
    if(isset($_POST['category_id'])){
      $data = array();

      $products = ViewActiveProductModel::where(['category_id'=>$_POST['category_id']])->groupBy('product','product_id')->get();
      foreach($products as $product){
        $data[] = array("product"=>$product->product,"product_id"=>$product->product_id,"product_image_url"=>URL::asset($product->product_image_url));
      }

      return response()->json($data);
    }else{
      return null;
    }
  }

  public function getProductDetail(){
    if(isset($_POST['product_id'])){
      $product = ViewActiveProductModel::where(['product_id'=>$_POST['product_id']])->first();
      return response()->json(['image_url'=>URL::asset($product->product_image_url),'description'=>$product->product_description]);
    }else{
      return null;
    }
  }

  public function getColour(){
    if(isset($_POST['product_id'])){
      return ViewActiveProductModel::where(['product_id'=>$_POST['product_id']])->lists('colour','colour_id');
    }else{
      return null;
    }
  }

  public function getColourImage(){
    if(isset($_POST['colour_id'])){
      $colour = ViewActiveProductModel::where(['colour_id'=>$_POST['colour_id']])->first();
      return response()->json(['image_url'=>URL::asset($colour->colour_image_url)]);
    }else{
      return null;
    }
  }

  public function getFgCode(){
    if(isset($_POST['colour_id'])){
      $colour = ViewActiveProductModel::where(['colour_id'=>$_POST['colour_id']])->first();
      return response()->json(['fg_code'=>$colour->fg_code,'price'=>$colour->price]);
    }else{
      return null;
    }
  }

  public function getProvince(){
    return ViewActiveLocationModel::lists('province','province_id');
  }

  public function getCity(){
    if(isset($_POST['province_id'])){
      $city = ViewActiveLocationModel::where(['province_id'=>$_POST['province_id']])->lists('city','city_id');
    }else{
      $city = null;
    }

    return $city;
  }

  public function getDistrict(){
    if(isset($_POST['city_id'])){
      $district = ViewActiveLocationModel::where(['city_id'=>$_POST['city_id']])->lists('district','district_id');
    }else{
      $district = null;
    }

    return $district;
  }

  public function getPaymentMethod(){
    if(isset($_POST['district_id'])){
      $payment_method = ViewActivePaymentMethodModel::where('location_district_id','=',$_POST['district_id'])
                                              ->whereRaw('(id = 2 OR id = 1)')
                                              ->lists('name','id');
    }else{
      $payment_method = null;
    }

    return $payment_method;
  }

  public function getCourier(){
    if(isset($_POST['district_id']) && isset($_POST['payment_method_id'])){
      $payment_method = PaymentMethodModel::where(['id'=>$_POST['payment_method_id']])->first();

      $courier = ViewActiveCourierModel::where(['location_district_id'=>$_POST['district_id']])
                              ->whereRaw($payment_method->id=='1'?'courier_id="1"':'courier_id<>"1"')
                              ->lists('courier_name','courier_id');
    }else{
      $courier = null;
    }
    return $courier;
  }

  public function getCourierPackage(){
    if(isset($_POST['district_id']) && isset($_POST['courier_id'])){
      $courier_package = ViewActiveCourierModel::where(['location_district_id'=>$_POST['district_id'],'courier_id'=>$_POST['courier_id']])
                              ->lists('courier_package_name','courier_package_id');
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
      $product = ProductFgCodeModel::where(['fg_code'=>$data['fg_code']])->first();

      $delivery_price = ViewActiveCourierModel::where(['location_district_id'=>$_POST['district_id'],'courier_package_id'=>$data['courier_package_id']])
                                              ->whereRaw('(min_price <='.$product->price.' AND (max_price >= '.$product->price.' OR max_price = 0))')
                                              ->first();

      return response()->json(["delivery_price"=>isset($delivery_price->price)?$delivery_price->price:"Null"]);
    }else{
      return null;
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
