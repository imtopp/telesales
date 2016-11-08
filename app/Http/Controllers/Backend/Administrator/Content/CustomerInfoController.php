<?php

namespace App\Http\Controllers\Backend\Administrator\Content;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesResources;
use Illuminate\Support\Facades\Auth;
use App\Models\CustomerInfo as CustomerInfoModel;
use DateTime;
use DB;

class CustomerInfoController extends BaseController
{
  use AuthorizesRequests, AuthorizesResources, DispatchesJobs, ValidatesRequests;

  //Render Page
  public function index(){
    return view('backend/administrator/content/customer_info');
  }

  //Read All Category
  public function read(){
    // storing  request (ie, get/post) global array to a variable
    $requestData= $_REQUEST;
    $key = "t3rs3r@h"; //key for encryption

    $columns = array(
    // datatable column index  => database column name
        0 => 'name',
        1 => 'address',
        2 => 'identity_type',
        3 => 'identity_number',
        4 => 'email',
        5 => 'mdn',
        6 => 'location_province',
        7 => 'location_city',
        8 => 'location_district',
        9 => 'delivery_address'
    );

    $totalData = CustomerInfoModel::count();
    $totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

    $model = DB::table('customer_info')
                ->select('customer_info.*',DB::raw('location_province.name AS location_province'),DB::raw('location_city.name AS location_city'),DB::raw('location_district.name AS location_district'))
                ->leftJoin('location_district','location_district.id','=','customer_info.location_district_id')
                ->leftJoin('location_city','location_city.id','=','location_district.city_id')
                ->leftJoin('location_province','location_province.id','=','location_city.province_id');

    if( !empty($requestData['search']['value']) ) {
      // if there is a search parameter
      $model = $model
                ->whereRaw('(decrypt_data("'.$key.'",customer_info.name) LIKE "'.$requestData['search']['value'].'%"')
                ->orWhereRaw('decrypt_data("'.$key.'",customer_info.address) LIKE "'.$requestData['search']['value'].'%"')
                ->orWhereRaw('decrypt_data("'.$key.'",customer_info.identity_type) LIKE "'.$requestData['search']['value'].'%"')
                ->orWhereRaw('decrypt_data("'.$key.'",customer_info.identity_number) LIKE "'.$requestData['search']['value'].'%"')
                ->orWhereRaw('decrypt_data("'.$key.'",customer_info.email) LIKE "'.$requestData['search']['value'].'%"')
                ->orWhereRaw('decrypt_data("'.$key.'",customer_info.mdn) LIKE "'.$requestData['search']['value'].'%"')
                ->orWhere('location_province.name','LIKE',$requestData['search']['value'].'%')
                ->orWhere('location_city.name','LIKE',$requestData['search']['value'].'%')
                ->orWhere('location_district.name','LIKE',$requestData['search']['value'].'%')
                ->orWhereRaw('decrypt_data("'.$key.'",customer_info.address) LIKE "'.$requestData['search']['value'].'%")');
      $totalFiltered = $model->count();
    }

    $query = $model
              ->orderBy($columns[$requestData['order'][0]['column']],$requestData['order'][0]['dir'])
              ->skip($requestData['start'])
              ->take($requestData['length'])
              ->get();

    $data = array();
    foreach($query as $row) {  // preparing an array
        $nestedData=array();

        $nestedData[$columns[0]] = $this->decrypt($key,$row->name);
        $nestedData[$columns[1]] = $this->decrypt($key,$row->address);
        $nestedData[$columns[2]] = $this->decrypt($key,$row->identity_type);
        $nestedData[$columns[3]] = $this->decrypt($key,$row->identity_number);
        $nestedData[$columns[4]] = $this->decrypt($key,$row->email);
        $nestedData[$columns[5]] = $this->decrypt($key,$row->mdn);
        $nestedData[$columns[6]] = $row->location_province;
        $nestedData[$columns[7]] = $row->location_city;
        $nestedData[$columns[8]] = $row->location_district;
        $nestedData[$columns[9]] = $this->decrypt($key,$row->delivery_address);

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
