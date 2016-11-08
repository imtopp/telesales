<?php

namespace App\Http\Controllers\Backend\Administrator\Content;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesResources;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction as TransactionModel;
use App\Models\TransactionStatus as TransactionStatusModel;
use DateTime;
use DB;

class TransactionController extends BaseController
{
  use AuthorizesRequests, AuthorizesResources, DispatchesJobs, ValidatesRequests;

  //Render Page
  public function index(){
    return view('backend/administrator/content/transaction');
  }

  //Read All Data
  public function read(){
    // storing  request (ie, get/post) global array to a variable
    $requestData= $_REQUEST;
    $key = "t3rs3r@h";

    $columns = array(
    // datatable column index  => database column name
        0 => 'customer_name',
        1 => 'customer_address',
        2 => 'customer_identity_type',
        3 => 'customer_identity_number',
        4 => 'customer_email',
        5 => 'customer_mdn',
        6 => 'customer_location_province',
        7 => 'customer_location_city',
        8 => 'customer_location_district',
        9 => 'customer_delivery_address',
        10 => 'product_category',
        11 => 'product_name',
        12 => 'product_colour',
        13 => 'product_fg_code',
        14 => 'product_price',
        15 => 'payment_method',
        16 => 'courier',
        17 => 'courier_package',
        18 => 'delivery_price',
        19 => 'total_price',
        20 => 'refference_number',
        21 => 'recent_status',
        22 => 'created_date',
        23 => 'created_by'
    );

    $model = TransactionModel::select('transaction.*','transaction_status.status')
                              ->join(DB::raw('(SELECT transaction_status.* FROM transaction_status LEFT JOIN (SELECT transaction_id,MAX(input_date) AS input_date FROM transaction_status GROUP BY transaction_id) x ON transaction_status.transaction_id = x.transaction_id AND transaction_status.input_date = x.input_date WHERE x.input_date IS NOT NULL) transaction_status'),'transaction_status.transaction_id','=','transaction.id');

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
                ->orWhereRaw('`status` LIKE "'.$requestData['search']['value'].'%")')
                ->orWhere('transaction.input_date','LIKE',$requestData['search']['value'].'%')
                ->orWhere('transaction.input_by','LIKE',$requestData['search']['value'].'%');

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

        $nestedData[$columns[0]] = $this->decrypt($key,$row->customer_name);
        $nestedData[$columns[1]] = $this->decrypt($key,$row->customer_address);
        $nestedData[$columns[2]] = $this->decrypt($key,$row->customer_identity_type);
        $nestedData[$columns[3]] = $this->decrypt($key,$row->customer_identity_number);
        $nestedData[$columns[4]] = $this->decrypt($key,$row->customer_email);
        $nestedData[$columns[5]] = $this->decrypt($key,$row->customer_mdn);
        $nestedData[$columns[6]] = $row->customer_location_province;
        $nestedData[$columns[7]] = $row->customer_location_city;
        $nestedData[$columns[8]] = $row->customer_location_district;
        $nestedData[$columns[9]] = $this->decrypt($key,$row->customer_delivery_address);
        $nestedData[$columns[10]] = $row->product_category;
        $nestedData[$columns[11]] = $row->product_name;
        $nestedData[$columns[12]] = $row->product_colour;
        $nestedData[$columns[13]] = $row->product_fg_code;
        $nestedData[$columns[14]] = "Rp ".number_format($row->product_price,0,",",".");;
        $nestedData[$columns[15]] = $row->payment_method;
        $nestedData[$columns[16]] = $row->courier;
        $nestedData[$columns[17]] = $row->courier_package;
        $nestedData[$columns[18]] = "Rp ".number_format($row->delivery_price,0,",",".");
        $nestedData[$columns[19]] = "Rp ".number_format($row->total_price,0,",",".");
        $nestedData[$columns[20]] = $row->refference_number;
        $nestedData[$columns[21]] = '<td><center>
                                      <a data-id="'.$row->id.'" data-toggle="tooltip" title="Edit" class="btn btn-sm btn-primary" onClick="detailStatus(this)"> '.$row->status.' </a>
                                      </center></td>';
        $nestedData[$columns[22]] = $row->input_date;
        $nestedData[$columns[23]] = $row->input_by;

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

  //Read All Data
  public function readStatus(){
    // storing  request (ie, get/post) global array to a variable
    $requestData= $_REQUEST;

    $columns = array(
    // datatable column index  => database column name
        0 => 'status',
        1 => 'date',
        2 => 'updated_by'
    );

    $model = TransactionStatusModel::select('transaction_status.*')
                                    ->where('transaction_id','=',$requestData['id']);

    $totalData = $model->count();
    $totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

    if( !empty($requestData['search']['value']) ) {
      // if there is a search parameter
      $model = $model
                ->Where('status','LIKE',$requestData['search']['value'].'%')
                ->orWhere('input_date','LIKE',$requestData['search']['value'].'%')
                ->orWhere('input_by','LIKE',$requestData['search']['value'].'%');

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

        $nestedData[$columns[0]] = $row->status;
        $nestedData[$columns[1]] = $row->input_date;
        $nestedData[$columns[2]] = $row->input_by;

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
