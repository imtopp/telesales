<?php

namespace App\Http\Controllers\Backend\Administrator\Content\ManagePayment;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesResources;
use Illuminate\Support\Facades\Auth;
use App\Models\PaymentMethod as PaymentMethodModel;
use App\Models\CodLocationMapping as LocationMappingModel;
use App\Models\ViewLocation as ViewLocationModel;
use DateTime;
use DB;
use URL;

class LocationMappingController extends BaseController
{
  use AuthorizesRequests, AuthorizesResources, DispatchesJobs, ValidatesRequests;

  //Render Page
  public function index(){
    return view('backend/administrator/content/payment/location_mapping'); //display list_product view with all_category and all_product
  }

  //Read All Payment Method Location Mapping
  public function read(){
    // storing  request (ie, get/post) global array to a variable
    $requestData= $_REQUEST;

    $columns = array(
    // datatable column index  => database column name
        0 => 'province',
        1 => 'city',
        2 => 'district',
        3 => 'status'
    );

    $totalData = LocationMappingModel::count();
    $totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

    if( !empty($requestData['search']['value']) ) {
      // if there is a search parameter
      $model = DB::table('cod_location_mapping')
                ->select('cod_location_mapping.id', 'location_province.name AS province', 'location_province.id AS province_id', 'location_city.name AS city', 'location_city.id AS city_id', 'location_district.name AS district','location_district.id AS district_id', 'cod_location_mapping.status')
                ->join('location_district','location_district.id','=','cod_location_mapping.location_district_id')
                ->join('location_city','location_city.id','=','location_district.city_id')
                ->join('location_province','location_province.id','=','location_city.province_id')
                ->where('location_province.name','LIKE',$requestData['search']['value'].'%')
                ->orWhere('location_city.name','LIKE',$requestData['search']['value'].'%')
                ->orWhere('location_district.name','LIKE',$requestData['search']['value'].'%')
                ->orWhere('cod_location_mapping.status','LIKE',$requestData['search']['value'].'%');
      $totalFiltered = $model->count();
      $query = $model
                ->orderBy($columns[$requestData['order'][0]['column']],$requestData['order'][0]['dir'])
                ->skip($requestData['start'])
                ->take($requestData['length'])
                ->get();
    } else {
      $query = DB::table('cod_location_mapping')
                ->select('cod_location_mapping.id', 'location_province.name AS province', 'location_province.id AS province_id', 'location_city.name AS city', 'location_city.id AS city_id', 'location_district.name AS district','location_district.id AS district_id', 'cod_location_mapping.status')
                ->join('location_district','location_district.id','=','cod_location_mapping.location_district_id')
                ->join('location_city','location_city.id','=','location_district.city_id')
                ->join('location_province','location_province.id','=','location_city.province_id')
                ->orderBy($columns[$requestData['order'][0]['column']],$requestData['order'][0]['dir'])
                ->skip($requestData['start'])
                ->take($requestData['length'])
                ->get();
    }

    $data = array();
    foreach($query as $row) {  // preparing an array
        $nestedData=array();

        $nestedData[$columns[0]] = $row->province;
        $nestedData[$columns[1]] = $row->city;
        $nestedData[$columns[2]] = $row->district;
        $nestedData[$columns[3]] = $row->status;
        $nestedData['action'] = '<td><center>
                           <a data-id="'.$row->id.'" data-province_id="'.$row->province_id.'" data-city_id="'.$row->city_id.'" data-district_id="'.$row->district_id.'" data-status="'.$row->status.'" data-toggle="tooltip" title="Edit" class="btn btn-sm btn-warning edit" onClick="edit(this)"> <i class="fa fa-pencil"></i> </a>
                           <a data-id="'.$row->id.'" data-name="'.$row->province.' - '.$row->city.' - '.$row->district.'" data-toggle="tooltip" title="Hapus" class="btn btn-sm btn-danger destroy" onClick="destroy(this)"> <i class="fa fa-trash"></i> </a>
                           </center></td>';

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

  //Create New Payment Method Location Mapping
  public function create(){
    $date = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s')); //initialize date parameter
    DB::beginTransaction();
    $model = new LocationMappingModel;

    $model->location_district_id = $_POST['district_id'];
    $model->status = $_POST['status'];
    $model->input_date = $date->format('Y-m-d H:i:s');
    $model->input_by = Auth::User()->email;
    $model->update_date = $date->format('Y-m-d H:i:s');
    $model->update_by = Auth::User()->email;

    try {
      $success = $model->save();
      $message = 'Create new data is success!';
    } catch (\Exception $ex) {
      DB::rollback();
      $success = false;
      $message = $ex->getMessage();
    }

    if($success){
      DB::commit();
    }

    return response()->json(['success'=>$success,'message'=>$message]);
  }

  //Update Existing Payment Method Location Mapping
  public function update(){
    $date = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s')); //initialize date parameter
    DB::beginTransaction();
    $model = LocationMappingModel::where(['id'=>$_POST['id']])->first();

    $model->location_district_id = $_POST['district_id'];
    $model->status = $_POST['status'];
    $model->update_date = $date->format('Y-m-d H:i:s');
    $model->update_by = Auth::User()->email;

    try {
      $success = $model->save();
      $message = 'Edit data is success!';
    } catch (\Exception $ex) {
      DB::rollback();
      $success = false;
      $message = $ex->getMessage();
    }

    if($success){
      DB::commit();
    }

    return response()->json(['success'=>$success,'message'=>$message]);
  }

  //Destroy Existing Payment Method Location Mapping
  public function destroy(){
    DB::beginTransaction();

    try {
      $success = LocationMappingModel::destroy($_POST['id']);
      $message = 'Delete data is success!';
      $error_message = null;
    } catch (\Exception $ex) {
      DB::rollback();
      $success = false;
      $error_message = $ex->getMessage();
      if($ex->getCode()=="23000"){
        $message = "Maaf data tidak dapat dihapus karena masih memiliki relasi dengan data lain.";
      }else{
        $message = $error_message;
      }
    }

    if($success){
      DB::commit();
    }

    return response()->json(['success'=>$success,'message'=>$message]);
  }

  public function getProvince(){
    $isset_province = "";
    if(isset($_POST['province_id'])){
      $isset_province = ' OR location_payment_method.province_id='.$_POST['province_id'];
    }

    return DB::table(DB::raw('(SELECT	view_location.* FROM `view_location`) location_payment_method'))
              ->leftJoin('cod_location_mapping',function($join){
                                                   $join->on('cod_location_mapping.location_district_id','=','location_payment_method.district_id');
                                                 })
              ->whereRaw('(cod_location_mapping.location_district_id IS NULL'.$isset_province.')')
              ->lists('location_payment_method.province','location_payment_method.province_id');
  }

  public function getCity(){
    $isset_city = "";
    if(isset($_POST['city_id'])){
      $isset_city = ' OR location_payment_method.city_id='.$_POST['city_id'];
    }

    if(isset($_POST['province_id']))
      $city = DB::table(DB::raw('(SELECT	view_location.* FROM `view_location`) location_payment_method'))
                ->leftJoin('cod_location_mapping',function($join){
                                                     $join->on('cod_location_mapping.location_district_id','=','location_payment_method.district_id');
                                                   })
                ->where('location_payment_method.province_id','=',$_POST['province_id'])
                ->whereRaw('(cod_location_mapping.location_district_id IS NULL'.$isset_city.')')
                ->lists('location_payment_method.city','location_payment_method.city_id');
    else
      $city = null;
    return $city;
  }

  public function getDistrict(){
    $isset_district = "";
    if(isset($_POST['district_id'])){
      $isset_district = ' OR location_payment_method.district_id='.$_POST['district_id'];
    }

    if(isset($_POST['city_id']))
      $district = DB::table(DB::raw('(SELECT	view_location.* FROM `view_location`) location_payment_method'))
                    ->leftJoin('cod_location_mapping',function($join){
                                                         $join->on('cod_location_mapping.location_district_id','=','location_payment_method.district_id');
                                                       })
                    ->where('location_payment_method.city_id','=',$_POST['city_id'])
                    ->whereRaw('(cod_location_mapping.location_district_id IS NULL'.$isset_district.')')
                    ->lists('location_payment_method.district','location_payment_method.district_id');
    else
      $district = null;
    return $district;
  }
}
