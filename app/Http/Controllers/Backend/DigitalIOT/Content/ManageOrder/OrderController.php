<?php

namespace App\Http\Controllers\Backend\DigitalIOT\Content\ManageOrder;

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
    return view('backend/digitaliot/content/order/order');
  }

  //Read All Data
  public function read(){
    // storing  request (ie, get/post) global array to a variable
    $requestData= $_REQUEST;
    $key = "t3rs3r@h"; //key for encryption

    $columns = array(
    // datatable column index  => database column name
        0 => 'refference_number',
        1 => 'channel',
        2 => 'customer_name',
        3 => 'customer_address',
        4 => 'customer_identity_type',
        5 => 'customer_identity_number',
        6 => 'customer_email',
        7 => 'customer_mdn',
        8 => 'customer_location_province',
        9 => 'customer_location_city',
        10 => 'customer_location_district',
        11 => 'customer_delivery_address',
        12 => 'product_category',
        13 => 'product_name',
        14 => 'product_colour',
        15 => 'product_fg_code',
        16 => 'product_price',
        17 => 'payment_method',
        18 => 'courier',
        19 => 'courier_package',
        20 => 'delivery_price',
        21 => 'total_price',
        22 => 'status',
        23 => 'payment_number',
        24 => 'airwaybill'
    );

    $model = TransactionModel::select('transaction.*',DB::raw('IF(transaction.input_by="Self Order",transaction.input_by,"Telesales") AS channel'),'transaction_status.status')
                              ->join(DB::raw('(SELECT transaction_status.* FROM transaction_status LEFT JOIN (SELECT transaction_id,MAX(input_date) AS input_date FROM transaction_status GROUP BY transaction_id) x ON transaction_status.transaction_id = x.transaction_id AND transaction_status.input_date = x.input_date WHERE x.input_date IS NOT NULL) transaction_status'),'transaction_status.transaction_id','=','transaction.id')
                              ->whereRaw('((transaction_status.status = "Order Received" AND (transaction.payment_method = "COD" OR transaction.payment_method = "Virtual Account BSM"))')
                              ->orWhere('transaction_status.status','=','Payment Complete')
                              ->orWhereRaw('transaction_status.status = "Order Completed")');

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
        $nestedData[$columns[1]] = $row->channel;
        $nestedData[$columns[2]] = $this->decrypt($key,$row->customer_name);
        $nestedData[$columns[3]] = $this->decrypt($key,$row->customer_address);
        $nestedData[$columns[4]] = $this->decrypt($key,$row->customer_identity_type);
        $nestedData[$columns[5]] = $this->decrypt($key,$row->customer_identity_number);
        $nestedData[$columns[6]] = $this->decrypt($key,$row->customer_email);
        $nestedData[$columns[7]] = $this->decrypt($key,$row->customer_mdn);
        $nestedData[$columns[8]] = $row->customer_location_province;
        $nestedData[$columns[9]] = $row->customer_location_city;
        $nestedData[$columns[10]] = $row->customer_location_district;
        $nestedData[$columns[11]] = $this->decrypt($key,$row->customer_delivery_address);
        $nestedData[$columns[12]] = $row->product_category;
        $nestedData[$columns[13]] = $row->product_name;
        $nestedData[$columns[14]] = $row->product_colour;
        $nestedData[$columns[15]] = $row->product_fg_code;
        $nestedData[$columns[16]] = "Rp ".number_format($row->product_price,0,",",".");;
        $nestedData[$columns[17]] = $row->payment_method;
        $nestedData[$columns[18]] = $row->courier;
        $nestedData[$columns[19]] = $row->courier_package;
        $nestedData[$columns[20]] = "Rp ".number_format($row->delivery_price,0,",",".");
        $nestedData[$columns[21]] = "Rp ".number_format($row->total_price,0,",",".");
        $nestedData[$columns[22]] = $row->status;
        $nestedData[$columns[23]] = $row->payment_number;
        $nestedData[$columns[24]] = $row->airwaybill;
        $nestedData['action'] = '<td><center>';

        if($row->status == "Payment Complete"){
          $nestedData['action'] = $nestedData['action'].'<a data-id="'.$row->id.'" data-refference_number="'.$row->refference_number.'" data-toggle="tooltip" title="Deliver Order" class="btn btn-sm btn-primary" onClick="deliver(this)"> <span class="fa-stack fa-lg"><i class="fa fa-send-o"></i></span> </a>';
        }

        if(($row->payment_method == "COD" || $row->payment_method == "Virtual Account BSM") && $row->status == "Order Received"){
          $nestedData['action'] = $nestedData['action'].'<a data-id="'.$row->id.'" data-refference_number="'.$row->refference_number.'" data-toggle="tooltip" title="Payment Received" class="btn btn-sm btn-warning" onClick="paymentReceived(this)"> <span class="fa-stack fa-lg"><i class="fa fa-money"></i></span> </a>';
        }

        if($row->payment_method == "COD" && $row->status == "Order Received"){
          $nestedData['action'] = $nestedData['action'].'<a data-id="'.$row->id.'" data-refference_number="'.$row->refference_number.'" data-toggle="tooltip" title="Cancel Order" class="btn btn-sm btn-danger" onClick="cancel(this)"> <span class="fa-stack fa-lg"><i class="fa fa-cart-plus fa-stack-1x"></i><i class="fa fa-ban fa-stack-2x text-danger"></i></span> </a>';
        }

        $nestedData['action'] = $nestedData['action'].'</center></td>';

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

  public function paymentReceived(){
    $date = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s')); //initialize date parameter
    DB::beginTransaction();
    $transaction_status = new TransactionStatusModel;

    $transaction_status->transaction_id = $_POST['id'];
    $transaction_status->status = "Payment Complete";
    $transaction_status->input_date = $date->format('Y-m-d H:i:s');
    $transaction_status->input_by = Auth::User()->email;
    $transaction_status->update_date = $date->format('Y-m-d H:i:s');
    $transaction_status->update_by = Auth::User()->email;

    try {
      $success = $transaction_status->save();
      $message = 'Payment update is success!';
    } catch (Exception $ex) {
      DB::rollback();
      $success = false;
      $message = $ex->getMessage();
    }

    if($success){
      DB::commit();
    }

    return response()->json(['success'=>$success,'message'=>$message]);
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

  public function deliverOrder(){
    $date = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s')); //initialize date parameter
    $key = "t3rs3r@h";
    DB::beginTransaction();
    $transaction = TransactionModel::where('id','=',$_REQUEST['id'])->first();

    $transaction->airwaybill = $_POST['airwaybill'];
    $transaction->update_date = $date->format('Y-m-d H:i:s');
    $transaction->update_by = Auth::User()->email;

    try {
      $success = $transaction->save();
    } catch (Exception $ex) {
      DB::rollback();
      $success = false;
      $message = $ex->getMessage();
    }

    if($success){
      $transaction_status = new TransactionStatusModel;

      $transaction_status->transaction_id = $_POST['id'];
      $transaction_status->status = "Order Completed";
      $transaction_status->input_date = $date->format('Y-m-d H:i:s');
      $transaction_status->input_by = Auth::User()->email;
      $transaction_status->update_date = $date->format('Y-m-d H:i:s');
      $transaction_status->update_by = Auth::User()->email;

      try {
        $success = $transaction_status->save();
        $message = 'Deliver order is success!';
      } catch (Exception $ex) {
        DB::rollback();
        $success = false;
        $message = $ex->getMessage();
      }
    }

    if($success){
      DB::commit();
    }

    if($success){
      try{
        $success = Mail::send('backend.digitaliot.emails.transaction_notification_customer_delivered', ["product"=>$transaction->product_name." - ".$transaction->product_colour,"date_created"=>$transaction->input_date,"courier"=>$transaction->courier,"airwaybill"=>$transaction->airwaybill], function($msg) use ($transaction,$key) {
          $msg->from('administrator-'.str_replace(' ','_',strtolower(config('settings.app_name'))).'@smartfren.com', "Administrator - ".config('settings.app_name'));
          $msg->to($this->decrypt($key,$transaction->customer_email), $this->decrypt($key,$transaction->customer_name))->subject('Transaction notifications');
        });
      } catch (Exception $ex) {
        $success = false;
        $message = $ex->getMessage();
      }
    }

    return response()->json(['success'=>$success,'message'=>$message]);
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
