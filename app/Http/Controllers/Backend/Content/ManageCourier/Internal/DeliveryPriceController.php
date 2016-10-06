<?php

namespace App\Http\Controllers\Backend\Content\ManageCourier\Internal;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesResources;
use Illuminate\Support\Facades\Auth;
use App\Models\CourierInternalDeliveryPrice as CourierInternalDeliveryPriceModel;
use App\Models\CourierLocationMapping as LocationMappingModel;
use App\Models\CourierPackage as CourierPackageModel;
use App\Models\Courier as CourierModel;
use App\Models\ViewLocation as ViewLocationModel;
use DateTime;
use DB;
use URL;

class DeliveryPriceController extends BaseController
{
  use AuthorizesRequests, AuthorizesResources, DispatchesJobs, ValidatesRequests;

  //Render Page
  public function index(){
    return view('backend/content/courier/internal/delivery_price');
  }

  public function read(){
    // storing  request (ie, get/post) global array to a variable
    $requestData= $_REQUEST;

    $columns = array(
    // datatable column index  => database column name
        0 => 'province',
        1 => 'city',
        2 => 'district',
        3 => 'courier_package',
        4 => 'price',
        5 => 'status'
    );

    $totalData = CourierInternalDeliveryPriceModel::count();
    $totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

    if( !empty($requestData['search']['value']) ) {
      // if there is a search parameter
      $model = DB::table('courier_internal_delivery_price')
                ->select('courier_internal_delivery_price.id', 'location_province.name AS province', 'location_province.id AS province_id', 'location_city.name AS city', 'location_city.id AS city_id', 'location_district.name AS district', 'location_district.id AS district_id', 'courier_package.name AS courier_package', 'courier_package.id AS courier_package_id', 'courier_internal_delivery_price.price', 'courier_location_mapping.status')
                ->join('courier_location_mapping','courier_location_mapping.id','=','courier_internal_delivery_price.courier_location_mapping_id')
                ->join('location_district','location_district.id','=','courier_location_mapping.location_district_id')
                ->join('location_city','location_city.id','=','location_district.city_id')
                ->join('location_province','location_province.id','=','location_city.province_id')
                ->join('courier_package','courier_package.id','=','courier_location_mapping.courier_package_id')
                ->where('location_province.name','LIKE',$requestData['search']['value'].'%')
                ->orWhere('location_city.name','LIKE',$requestData['search']['value'].'%')
                ->orWhere('location_district.name','LIKE',$requestData['search']['value'].'%')
                ->orWhere('courier_internal_delivery_price.price','LIKE',$requestData['search']['value'].'%');
      $totalFiltered = $model->count();
      $query = $model
                ->orderBy($columns[$requestData['order'][0]['column']],$requestData['order'][0]['dir'])
                ->skip($requestData['start'])
                ->take($requestData['length'])
                ->get();
    } else {
      $query = DB::table('courier_internal_delivery_price')
                ->select('courier_internal_delivery_price.id', 'location_province.name AS province', 'location_province.id AS province_id', 'location_city.name AS city', 'location_city.id AS city_id', 'location_district.name AS district', 'location_district.id AS district_id', 'courier_package.name AS courier_package', 'courier_package.id AS courier_package_id', 'courier_internal_delivery_price.price', 'courier_location_mapping.status')
                ->join('courier_location_mapping','courier_location_mapping.id','=','courier_internal_delivery_price.courier_location_mapping_id')
                ->join('location_district','location_district.id','=','courier_location_mapping.location_district_id')
                ->join('location_city','location_city.id','=','location_district.city_id')
                ->join('location_province','location_province.id','=','location_city.province_id')
                ->join('courier_package','courier_package.id','=','courier_location_mapping.courier_package_id')
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
        $nestedData[$columns[3]] = $row->courier_package;
        $nestedData[$columns[4]] = $row->price;
        $nestedData[$columns[5]] = $row->status;
        $nestedData['action'] = '<td><center>
                           <a href="#" data-id="'.$row->id.'" data-province_id="'.$row->province_id.'" data-city_id="'.$row->city_id.'" data-district_id="'.$row->district_id.'" data-courier_package_id="'.$row->courier_package_id.'" data-price="'.$row->price.'" data-status="'.$row->status.'" data-toggle="tooltip" title="Edit" class="btn btn-sm btn-warning edit" onClick="edit(this)"> <i class="fa fa-pencil"></i> </a>
                           <a href="#" data-id="'.$row->id.'" data-name="<span style='."'font-size: small';>".'<br/>Provinsi : '.$row->province.'<br/>Kota : '.$row->city.'<br/>Kecamatan : '.$row->district.'<br/>Paket Pengiriman : '.$row->courier_package.'<br/>Biaya Kirim : '.$row->price.'</span>" data-toggle="tooltip" title="Hapus" class="btn btn-sm btn-danger destroy" onClick="destroy(this)"> <i class="fa fa-trash"></i> </a>
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

  public function create(){
    $date = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s')); //initialize date parameter

    $modelExists = false;
    $location_mapping = LocationMappingModel::where(['location_district_id'=>$_POST['district_id'],'courier_package_id'=>$_POST['courier_package_id']])->first();
    if(isset($location_mapping)){
      $modelExists = CourierInternalDeliveryPriceModel::where(['courier_location_mapping_id'=>$location_mapping->id])->count();
    }
    if(!$modelExists){
      $success = true;

      if(!isset($location_mapping)){
        $location_mapping = new LocationMappingModel;
        $location_mapping->courier_package_id = $_POST['courier_package_id'];
        $location_mapping->location_district_id = $_POST['district_id'];
        $location_mapping->status = $_POST['status'];
        $location_mapping->input_date = $date->format('Y-m-d H:i:s');
        $location_mapping->input_by = Auth::User()->email;
        $location_mapping->update_date = $date->format('Y-m-d H:i:s');
        $location_mapping->update_by = Auth::User()->email;
      }else{
        $location_mapping->status = $_POST['status'];
      }
      try {
        $success = $location_mapping->save();
      } catch (\Exception $ex) {
        $success = false;
        $message = $ex->getMessage();
      }

      if($success){
        $model = new CourierInternalDeliveryPriceModel;
        $model->courier_location_mapping_id = $location_mapping->id;
        $model->price = $_POST['price'];
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
      }
    }else{
      $success = false;
      $message = 'Maaf data tersebut sudah ada dalam database.';
    }

    return response()->json(['success'=>$success,'message'=>$message]);
  }

  public function update(){
    $date = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s')); //initialize date parameter
    $model = CourierInternalDeliveryPriceModel::where(['id'=>$_POST['id']])->first();

    $modelExists = false;
    $location_mapping = LocationMappingModel::where(['location_district_id'=>$_POST['district_id'],'courier_package_id'=>$_POST['courier_package_id']])->first();
    if(isset($location_mapping)){
      $modelExists = CourierInternalDeliveryPriceModel::where(['courier_location_mapping_id'=>$location_mapping->id])->count();
    }

    if(!$modelExists || (isset($location_mapping)?$model->courier_location_mapping_id==$location_mapping->id:false)){
      $success = true;

      if(!isset($location_mapping)){
        $location_mapping = new LocationMappingModel;
        $location_mapping->courier_package_id = $_POST['courier_package_id'];
        $location_mapping->location_district_id = $_POST['district_id'];
        $location_mapping->status = $_POST['status'];
        $location_mapping->input_date = $date->format('Y-m-d H:i:s');
        $location_mapping->input_by = Auth::User()->email;
        $location_mapping->update_date = $date->format('Y-m-d H:i:s');
        $location_mapping->update_by = Auth::User()->email;
      }else{
        $location_mapping->status = $_POST['status'];
      }
      try {
        $success = $location_mapping->save();
      } catch (\Exception $ex) {
        $success = false;
        $message = $ex->getMessage();
      }

      if($success){
        $old_location_mapping_id = $model->courier_location_mapping_id;

        $model->courier_location_mapping_id = $location_mapping->id;
        $model->price = $_POST['price'];
        $model->update_date = $date->format('Y-m-d H:i:s');
        $model->update_by = Auth::User()->email;

        try {
          $success = $model->save();
          $message = 'Edit data is success!';
        } catch (\Exception $ex) {
          $success = false;
          $message = $ex->getMessage();
        }

        if($success && $old_location_mapping_id != $model->courier_location_mapping_id){
          try {
            $success = LocationMappingModel::destroy($old_location_mapping_id);
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
        }
      }
    }else{
      $success = false;
      $message = "Maaf data tersebut sudah ada dalam database, perubahan dibatalkan.";
    }

    return response()->json(['success'=>$success,'message'=>$message]);
  }

  public function destroy(){
    $model = CourierInternalDeliveryPriceModel::where(['id'=>$_POST['id']])->first();
    $old_location_mapping_id = $model->courier_location_mapping_id;

    try {
      $success = CourierInternalDeliveryPriceModel::destroy($_POST['id']);
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

    if($success){
      try {
        $success = LocationMappingModel::destroy($old_location_mapping_id);
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
    }

    return response()->json(['success'=>$success,'message'=>$message]);
  }

  public function getProvince(){
    $isset_province = "";
    if(isset($_POST['province_id'])){
      $isset_province = ' OR location_courier.province_id='.$_POST['province_id'];
    }

    return DB::table(DB::raw('(SELECT	view_location.*, courier_package.id AS courier_package_id, courier_package.`name` AS courier_package FROM `view_location`, courier LEFT JOIN courier_package ON courier_package.courier_id = courier.id	WHERE courier.`name` = "Internal") location_courier'))
              ->leftJoin('courier_location_mapping',function($join){
                                                   $join->on('courier_location_mapping.location_district_id','=','location_courier.district_id');
                                                   $join->on('courier_location_mapping.courier_package_id','=','location_courier.courier_package_id');
                                                 })
              ->leftJoin('courier_internal_delivery_price','courier_internal_delivery_price.courier_location_mapping_id','=','courier_location_mapping.id')
              ->whereRaw('(courier_internal_delivery_price.price IS NULL'.$isset_province.')')
              ->lists('location_courier.province','location_courier.province_id');
  }

  public function getCity(){
    $isset_city = "";
    if(isset($_POST['city_id'])){
      $isset_city = ' OR location_courier.city_id='.$_POST['city_id'];
    }

    if(isset($_POST['province_id']))
      $city = DB::table(DB::raw('(SELECT	view_location.*, courier_package.id AS courier_package_id, courier_package.`name` AS courier_package FROM `view_location`, courier LEFT JOIN courier_package ON courier_package.courier_id = courier.id	WHERE courier.`name` = "Internal" AND view_location.province_id = "'.$_POST['province_id'].'") location_courier'))
                ->leftJoin('courier_location_mapping',function($join){
                                                     $join->on('courier_location_mapping.location_district_id','=','location_courier.district_id');
                                                     $join->on('courier_location_mapping.courier_package_id','=','location_courier.courier_package_id');
                                                   })
                ->leftJoin('courier_internal_delivery_price','courier_internal_delivery_price.courier_location_mapping_id','=','courier_location_mapping.id')
                ->whereRaw('(courier_internal_delivery_price.price IS NULL'.$isset_city.')')
                ->lists('location_courier.city','location_courier.city_id');
    else
      $city = null;
    return $city;
  }

  public function getDistrict(){
    $isset_district = "";
    if(isset($_POST['district_id'])){
      $isset_district = ' OR location_courier.district_id='.$_POST['district_id'];
    }

    if(isset($_POST['city_id']))
      $district = DB::table(DB::raw('(SELECT	view_location.*, courier_package.id AS courier_package_id, courier_package.`name` AS courier_package FROM `view_location`, courier LEFT JOIN courier_package ON courier_package.courier_id = courier.id	WHERE courier.`name` = "Internal" AND view_location.city_id = "'.$_POST['city_id'].'") location_courier'))
                ->leftJoin('courier_location_mapping',function($join){
                                                     $join->on('courier_location_mapping.location_district_id','=','location_courier.district_id');
                                                     $join->on('courier_location_mapping.courier_package_id','=','location_courier.courier_package_id');
                                                   })
                ->leftJoin('courier_internal_delivery_price','courier_internal_delivery_price.courier_location_mapping_id','=','courier_location_mapping.id')
                ->whereRaw('(courier_internal_delivery_price.price IS NULL'.$isset_district.')')
                ->lists('location_courier.district','location_courier.district_id');
    else
      $district = null;
    return $district;
  }

  public function getCourierPackage(){
    $isset_courier_package = "";
    if(isset($_POST['courier_package_id'])){
      $isset_courier_package = ' OR location_courier.courier_package_id='.$_POST['courier_package_id'];
    }

    if(isset($_POST['district_id']))
      $courier_package = DB::table(DB::raw('(SELECT	view_location.*, courier_package.id AS courier_package_id, courier_package.`name` AS courier_package FROM `view_location`, courier LEFT JOIN courier_package ON courier_package.courier_id = courier.id	WHERE courier.`name` = "Internal" AND view_location.district_id = "'.$_POST['district_id'].'") location_courier'))
                ->leftJoin('courier_location_mapping',function($join){
                                                     $join->on('courier_location_mapping.location_district_id','=','location_courier.district_id');
                                                     $join->on('courier_location_mapping.courier_package_id','=','location_courier.courier_package_id');
                                                   })
                ->leftJoin('courier_internal_delivery_price','courier_internal_delivery_price.courier_location_mapping_id','=','courier_location_mapping.id')
                ->whereRaw('(courier_internal_delivery_price.price IS NULL'.$isset_courier_package.')')
                ->lists('location_courier.courier_package','location_courier.courier_package_id');
    else
      $courier_package = null;
    return $courier_package;
  }
}
