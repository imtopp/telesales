<?php

namespace App\Http\Controllers\Backend\Administrator\Content\ManageLocation;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesResources;
use Illuminate\Support\Facades\Auth;
use App\Models\LocationProvince as ProvinceModel;
use App\Models\LocationCity as CityModel;
use App\Models\LocationDistrict as DistrictModel;
use DateTime;
use DB;
use URL;

class DistrictController extends BaseController
{
  use AuthorizesRequests, AuthorizesResources, DispatchesJobs, ValidatesRequests;

  //Render Page
  public function index(){
    $city = CityModel::lists('name','id');
    return view('backend/administrator/content/location/district',['city'=>$city]);
  }

  //Read All District
  public function read(){
    // storing  request (ie, get/post) global array to a variable
    $requestData= $_REQUEST;

    $columns = array(
    // datatable column index  => database column name
        0 => 'name',
        1 => 'province',
        2 => 'city',
        3 => 'status'
    );

    $totalData = DistrictModel::count();
    $totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

    if( !empty($requestData['search']['value']) ) {
      // if there is a search parameter
      $model = DB::table('location_district')
                ->select('location_district.id', 'location_district.name', 'location_province.name AS province', 'location_province.id AS province_id', 'location_city.name AS city', 'location_city.id AS city_id', 'location_district.status')
                ->join('location_city','location_city.id','=','location_district.city_id')
                ->join('location_province','location_province.id','=','location_city.province_id')
                ->where('location_district.name','LIKE',$requestData['search']['value'].'%')
                ->orWhere('location_district.status','LIKE',$requestData['search']['value'].'%')
                ->orWhere('location_city.name','LIKE',$requestData['search']['value'].'%')
                ->orWhere('location_province.name','LIKE',$requestData['search']['value'].'%');
      $totalFiltered = $model->count();
      $query = $model
                ->orderBy($columns[$requestData['order'][0]['column']],$requestData['order'][0]['dir'])
                ->skip($requestData['start'])
                ->take($requestData['length'])
                ->get();
    } else {
      $query = DB::table('location_district')
                ->select('location_district.id', 'location_district.name', 'location_province.name AS province', 'location_province.id AS province_id', 'location_city.name AS city', 'location_city.id AS city_id', 'location_district.status')
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

        $nestedData[$columns[0]] = $row->name;
        $nestedData[$columns[1]] = $row->province;
        $nestedData[$columns[2]] = $row->city;
        $nestedData[$columns[3]] = $row->status;
        $nestedData['action'] = '<td><center>
                           <a data-id="'.$row->id.'" data-name="'.$row->name.'" data-province_id="'.$row->province_id.'" data-city_id="'.$row->city_id.'" data-status="'.$row->status.'" data-toggle="tooltip" title="Edit" class="btn btn-sm btn-warning edit" onClick="edit(this)"> <i class="fa fa-pencil"></i> </a>
                           <a data-id="'.$row->id.'" data-name="'.$row->name.'" data-toggle="tooltip" title="Hapus" class="btn btn-sm btn-danger destroy" onClick="destroy(this)"> <i class="fa fa-trash"></i> </a>
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

  //Create New District
  public function create(){
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

  //Update Existing District
  public function update(){
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

  //Destroy Existing District
  public function destroy(){
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

    return response()->json(['success'=>$success,'message'=>$message]);
  }

  //Get All Related Province
  public function get_province(){
    $models = ProvinceModel::join('location_city','location_city.province_id','=','location_province.id')
                            ->lists('location_province.name','location_province.id');

    return $models;
  }

  //Get All Related City
  public function get_city(){;
    $models = CityModel::where(['province_id'=>$_POST['province_id']])->lists('name','id');

    return $models;
  }
}
