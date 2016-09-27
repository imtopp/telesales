<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesResources;
use Illuminate\Support\Facades\Auth;
use App\Models\CustomerLocationCity as CityModel;
use App\Models\CustomerLocationDistrict as DistrictModel;
use App\Models\CustomerLocationProvince as ProvinceModel;
use DateTime;
use DB;
use URL;

class ManageLocationController extends BaseController
{
  use AuthorizesRequests, AuthorizesResources, DispatchesJobs, ValidatesRequests;

  //Province
  public function province(){
    return view('backend/content/location/province'); //display list_product view with all_category and all_product
  }

  public function provinceRead(){
    // storing  request (ie, get/post) global array to a variable
    $requestData= $_REQUEST;

    $columns = array(
    // datatable column index  => database column name
        0 => 'name',
        1 => 'status'
    );

    $totalData = ProvinceModel::count();
    $totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

    if( !empty($requestData['search']['value']) ) {
      // if there is a search parameter
      $model = DB::table('customer_location_province')
                ->select('customer_location_province.id', 'customer_location_province.name', 'customer_location_province.status')
                ->where('customer_location_province.name','LIKE',$requestData['search']['value'].'%')
                ->orWhere('customer_location_province.status','LIKE',$requestData['search']['value'].'%');
      $totalFiltered = $model->count();
      $query = $model
                ->orderBy($columns[$requestData['order'][0]['column']],$requestData['order'][0]['dir'])
                ->skip($requestData['start'])
                ->take($requestData['length'])
                ->get();
    } else {
      $query = DB::table('customer_location_province')
                ->select('customer_location_province.id', 'customer_location_province.name', 'customer_location_province.status')
                ->orderBy($columns[$requestData['order'][0]['column']],$requestData['order'][0]['dir'])
                ->skip($requestData['start'])
                ->take($requestData['length'])
                ->get();
    }

    $data = array();
    foreach($query as $row) {  // preparing an array
        $nestedData=array();

        $nestedData[$columns[0]] = $row->name;
        $nestedData[$columns[1]] = $row->status;
        $nestedData['action'] = '<td><center>
                           <a href="#" data-id="'.$row->id.'" data-name="'.$row->name.'" data-status="'.$row->status.'" data-toggle="tooltip" title="Edit" class="btn btn-sm btn-warning edit" onClick="edit(this)"> <i class="fa fa-pencil"></i> </a>
                           <a href="#" data-id="'.$row->id.'" data-name="'.$row->name.'" data-toggle="tooltip" title="Hapus" class="btn btn-sm btn-danger destroy" onClick="destroy(this)"> <i class="fa fa-trash"></i> </a>
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

  public function provinceCreate(){
    $date = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s')); //initialize date parameter

    $model = new ProvinceModel;
    $model->name = $_POST['name'];
    $model->status = $_POST['status'];
    $model->input_date = $date->format('Y-m-d H:i:s');
    $model->input_by = Auth::User()->email;
    $model->update_date = $date->format('Y-m-d H:i:s');
    $model->update_by = Auth::User()->email;

    try {
      $success = $model->save();
      $message = 'Create new data is success!';
    } catch (\Exception $ex) {
      $success = false;
      $message = $ex->getMessage();
    }

    return response()->json(['success'=>$success,'message'=>$message]);
  }

  public function provinceUpdate(){
    $date = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s')); //initialize date parameter
    $model = ProvinceModel::where(['id'=>$_POST['id']])->first();

    $model->name = $_POST['name'];
    $model->status = $_POST['status'];
    $model->update_date = $date->format('Y-m-d H:i:s');
    $model->update_by = Auth::User()->email;

    try {
      $success = $model->save();
      $message = 'Edit data is success!';
    } catch (\Exception $ex) {
      $success = false;
      $message = $ex->getMessage();
    }

    return response()->json(['success'=>$success,'message'=>$message]);
  }

  public function provinceDestroy(){
    try {
      $success = ProvinceModel::destroy($_POST['id']);
      $message = 'Delete data is success!';
      $error_message = null;
    } catch (\Exception $ex) {
      $success = false;
      $error_message = $ex->getMessage();
      if($ex->getCode()=="23000"){
        $message = "Maaf data tidak dapat dihapus karena masih memiliki relasi dengan data lain.";
      }else{
        $message = $error_message;
      }
    }

    return response()->json(['success'=>$success,'message'=>$message,'error_message'=>$error_message]);
  }

  //City
  public function city(){
    $province = ProvinceModel::lists('name','id');
    return view('backend/content/location/city',['province'=>$province]); //display list_product view with all_category and all_product
  }

  public function cityRead(){
    // storing  request (ie, get/post) global array to a variable
    $requestData= $_REQUEST;

    $columns = array(
    // datatable column index  => database column name
        0 => 'name',
        1 => 'province',
        2 => 'status'
    );

    $totalData = CityModel::count();
    $totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

    if( !empty($requestData['search']['value']) ) {
      // if there is a search parameter
      $model = DB::table('customer_location_city')
                ->select('customer_location_city.id', 'customer_location_city.name', 'customer_location_province.name AS province', 'customer_location_province.id AS province_id', 'customer_location_city.status')
                ->join('customer_location_province','customer_location_province.id','=','customer_location_city.province_id')
                ->where('customer_location_city.name','LIKE',$requestData['search']['value'].'%')
                ->orWhere('customer_location_city.status','LIKE',$requestData['search']['value'].'%')
                ->orWhere('customer_location_province.name','LIKE',$requestData['search']['value'].'%');
      $totalFiltered = $model->count();
      $query = $model
                ->orderBy($columns[$requestData['order'][0]['column']],$requestData['order'][0]['dir'])
                ->skip($requestData['start'])
                ->take($requestData['length'])
                ->get();
    } else {
      $query = DB::table('customer_location_city')
                ->select('customer_location_city.id', 'customer_location_city.name', 'customer_location_province.name AS province', 'customer_location_province.id AS province_id', 'customer_location_city.status')
                ->join('customer_location_province','customer_location_province.id','=','customer_location_city.province_id')
                ->orderBy($columns[$requestData['order'][0]['column']],$requestData['order'][0]['dir'])
                ->skip($requestData['start'])
                ->take($requestData['length'])
                ->get();
    }

    $data = array();
    foreach($query as $row) {  // preparing an array
        $nestedData=array();

        $nestedData[$columns[0]] = $row->name;
        $nestedData[$columns[1]] = $row->province;
        $nestedData[$columns[2]] = $row->status;
        $nestedData['action'] = '<td><center>
                           <a href="#" data-id="'.$row->id.'" data-name="'.$row->name.'" data-province_id="'.$row->province_id.'" data-status="'.$row->status.'" data-toggle="tooltip" title="Edit" class="btn btn-sm btn-warning edit" onClick="edit(this)"> <i class="fa fa-pencil"></i> </a>
                           <a href="#" data-id="'.$row->id.'" data-name="'.$row->name.'" data-toggle="tooltip" title="Hapus" class="btn btn-sm btn-danger destroy" onClick="destroy(this)"> <i class="fa fa-trash"></i> </a>
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

  public function cityCreate(){
    $date = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s')); //initialize date parameter

    $model = new CityModel;
    $model->name = $_POST['name'];
    $model->province_id = $_POST['province_id'];
    $model->status = $_POST['status'];
    $model->input_date = $date->format('Y-m-d H:i:s');
    $model->input_by = Auth::User()->email;
    $model->update_date = $date->format('Y-m-d H:i:s');
    $model->update_by = Auth::User()->email;

    try {
      $success = $model->save();
      $message = 'Create new data is success!';
    } catch (\Exception $ex) {
      $success = false;
      $message = $ex->getMessage();
    }

    return response()->json(['success'=>$success,'message'=>$message]);
  }

  public function cityUpdate(){
    $date = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s')); //initialize date parameter
    $model = CityModel::where(['id'=>$_POST['id']])->first();

    $model->name = $_POST['name'];
    $model->province_id = $_POST['province_id'];
    $model->status = $_POST['status'];
    $model->update_date = $date->format('Y-m-d H:i:s');
    $model->update_by = Auth::User()->email;

    try {
      $success = $model->save();
      $message = 'Edit data is success!';
    } catch (\Exception $ex) {
      $success = false;
      $message = $ex->getMessage();
    }

    return response()->json(['success'=>$success,'message'=>$message]);
  }

  public function cityDestroy(){
    try {
      $success = CityModel::destroy($_POST['id']);
      $message = 'Delete data is success!';
      $error_message = null;
    } catch (\Exception $ex) {
      $success = false;
      $error_message = $ex->getMessage();
      if($ex->getCode()=="23000"){
        $message = "Maaf data tidak dapat dihapus karena masih memiliki relasi dengan data lain.";
      }else{
        $message = $error_message;
      }
    }

    return response()->json(['success'=>$success,'message'=>$message,'error_message'=>$error_message]);
  }

  //District
  public function district(){
    $city = CityModel::lists('name','id');
    return view('backend/content/location/district',['city'=>$city]); //display list_product view with all_category and all_product
  }

  public function districtRead(){
    // storing  request (ie, get/post) global array to a variable
    $requestData= $_REQUEST;

    $columns = array(
    // datatable column index  => database column name
        0 => 'name',
        1 => 'city',
        2 => 'status'
    );

    $totalData = DistrictModel::count();
    $totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

    if( !empty($requestData['search']['value']) ) {
      // if there is a search parameter
      $model = DB::table('customer_location_district')
                ->select('customer_location_district.id', 'customer_location_district.name', 'customer_location_city.name AS city', 'customer_location_city.id AS city_id', 'customer_location_district.status')
                ->join('customer_location_city','customer_location_city.id','=','customer_location_district.city_id')
                ->where('customer_location_district.name','LIKE',$requestData['search']['value'].'%')
                ->orWhere('customer_location_district.status','LIKE',$requestData['search']['value'].'%')
                ->orWhere('customer_location_city.name','LIKE',$requestData['search']['value'].'%');
      $totalFiltered = $model->count();
      $query = $model
                ->orderBy($columns[$requestData['order'][0]['column']],$requestData['order'][0]['dir'])
                ->skip($requestData['start'])
                ->take($requestData['length'])
                ->get();
    } else {
      $query = DB::table('customer_location_district')
                ->select('customer_location_district.id', 'customer_location_district.name', 'customer_location_city.name AS city', 'customer_location_city.id AS city_id', 'customer_location_district.status')
                ->join('customer_location_city','customer_location_city.id','=','customer_location_district.city_id')
                ->orderBy($columns[$requestData['order'][0]['column']],$requestData['order'][0]['dir'])
                ->skip($requestData['start'])
                ->take($requestData['length'])
                ->get();
    }

    $data = array();
    foreach($query as $row) {  // preparing an array
        $nestedData=array();

        $nestedData[$columns[0]] = $row->name;
        $nestedData[$columns[1]] = $row->city;
        $nestedData[$columns[2]] = $row->status;
        $nestedData['action'] = '<td><center>
                           <a href="#" data-id="'.$row->id.'" data-name="'.$row->name.'" data-city_id="'.$row->city_id.'" data-status="'.$row->status.'" data-toggle="tooltip" title="Edit" class="btn btn-sm btn-warning edit" onClick="edit(this)"> <i class="fa fa-pencil"></i> </a>
                           <a href="#" data-id="'.$row->id.'" data-name="'.$row->name.'" data-toggle="tooltip" title="Hapus" class="btn btn-sm btn-danger destroy" onClick="destroy(this)"> <i class="fa fa-trash"></i> </a>
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

  public function districtCreate(){
    $date = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s')); //initialize date parameter

    $model = new DistrictModel;
    $model->name = $_POST['name'];
    $model->city_id = $_POST['city_id'];
    $model->status = $_POST['status'];
    $model->input_date = $date->format('Y-m-d H:i:s');
    $model->input_by = Auth::User()->email;
    $model->update_date = $date->format('Y-m-d H:i:s');
    $model->update_by = Auth::User()->email;

    try {
      $success = $model->save();
      $message = 'Create new data is success!';
    } catch (\Exception $ex) {
      $success = false;
      $message = $ex->getMessage();
    }

    return response()->json(['success'=>$success,'message'=>$message]);
  }

  public function districtUpdate(){
    $date = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s')); //initialize date parameter
    $model = DistrictModel::where(['id'=>$_POST['id']])->first();

    $model->name = $_POST['name'];
    $model->city_id = $_POST['city_id'];
    $model->status = $_POST['status'];
    $model->update_date = $date->format('Y-m-d H:i:s');
    $model->update_by = Auth::User()->email;

    try {
      $success = $model->save();
      $message = 'Edit data is success!';
    } catch (\Exception $ex) {
      $success = false;
      $message = $ex->getMessage();
    }

    return response()->json(['success'=>$success,'message'=>$message]);
  }

  public function districtDestroy(){
    try {
      $success = DistrictModel::destroy($_POST['id']);
      $message = 'Delete data is success!';
      $error_message = null;
    } catch (\Exception $ex) {
      $success = false;
      $error_message = $ex->getMessage();
      if($ex->getCode()=="23000"){
        $message = "Maaf data tidak dapat dihapus karena masih memiliki relasi dengan data lain.";
      }else{
        $message = $error_message;
      }
    }

    return response()->json(['success'=>$success,'message'=>$message,'error_message'=>$error_message]);
  }
}
