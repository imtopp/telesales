<?php

namespace App\Http\Controllers\Backend\Administrator\Content\ManageCourier;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesResources;
use Illuminate\Support\Facades\Auth;
use App\Models\CourierDeliveryPrice as DeliveryPriceModel;
use App\Models\CourierLocationMapping as LocationMappingModel;
use App\Models\CourierPackage as CourierPackageModel;
use App\Models\Courier as CourierModel;
use App\Models\CourierPriceCategory as PriceCategoryModel;
use App\Models\ViewLocation as ViewLocationModel;
use DateTime;
use DB;
use URL;

class DeliveryPriceController extends BaseController
{
  use AuthorizesRequests, AuthorizesResources, DispatchesJobs, ValidatesRequests;

  //Render Page
  public function index(){
    return view('backend/administrator/content/courier/delivery_price');
  }

  //Get All Delivery Price
  public function read(){
    // storing  request (ie, get/post) global array to a variable
    $requestData= $_REQUEST;

    $columns = array(
    // datatable column index  => database column name
        0 => 'province',
        1 => 'city',
        2 => 'district',
        3 => 'courier',
        4 => 'courier_package',
        5 => 'status'
    );
    
    $model = DB::table('courier_delivery_price')
              ->select('courier_location_mapping.id', 'location_province.name AS province', 'location_province.id AS province_id', 'location_city.name AS city', 'location_city.id AS city_id', 'location_district.name AS district', 'location_district.id AS district_id', 'courier.name AS courier', 'courier.id AS courier_id', 'courier_package.name AS courier_package', 'courier_package.id AS courier_package_id', 'courier_location_mapping.status')
              ->join('courier_price_category','courier_price_category.id','=','courier_delivery_price.courier_price_category_id')
              ->join('courier_location_mapping','courier_location_mapping.id','=','courier_delivery_price.courier_location_mapping_id')
              ->join('location_district','location_district.id','=','courier_location_mapping.location_district_id')
              ->join('location_city','location_city.id','=','location_district.city_id')
              ->join('location_province','location_province.id','=','location_city.province_id')
              ->join('courier_package','courier_package.id','=','courier_location_mapping.courier_package_id')
              ->join('courier','courier.id','=','courier_package.courier_id')
              ->where('courier_price_category.status','=','active')
              ->groupBy('location_province.name')
              ->groupBy('location_city.name')
              ->groupBy('location_district.name')
              ->groupBy('courier.name')
              ->groupBy('courier_package.name')
              ->groupBy('courier_location_mapping.status');

    $totalData = $model->count();

    if( !empty($requestData['search']['value']) ) {
      $totalFiltered = $model->count();

      // if there is a search parameter
      $query = $model
                ->where('location_province.name','LIKE',$requestData['search']['value'].'%')
                ->orWhere('location_city.name','LIKE',$requestData['search']['value'].'%')
                ->orWhere('location_district.name','LIKE',$requestData['search']['value'].'%')
                ->orWhere('courier.name','LIKE',$requestData['search']['value'].'%')
                ->orWhere('courier_package.name','LIKE',$requestData['search']['value'].'%')
                ->orWhere('courier_location_mapping.status','LIKE',$requestData['search']['value'].'%')
                ->orderBy($columns[$requestData['order'][0]['column']],$requestData['order'][0]['dir'])
                ->skip($requestData['start'])
                ->take($requestData['length'])
                ->get();
    } else {
      $totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

      $query = $model
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
        $nestedData[$columns[3]] = $row->courier;
        $nestedData[$columns[4]] = $row->courier_package;
        $nestedData[$columns[5]] = $row->status;
        $nestedData['delivery_price'] = '<td><center>
                           <a data-id="'.$row->id.'" data-courier_id="'.$row->courier_id.'" data-toggle="tooltip" title="Change Delivery Price" class="btn btn-sm btn-primary" onClick="delivery_price(this)"> <i class="fa fa-pencil"></i> Change Delivery Price</a>
                           </center></td>';
        $nestedData['action'] = '<td><center>
                           <a data-id="'.$row->id.'" data-province_id="'.$row->province_id.'" data-city_id="'.$row->city_id.'" data-district_id="'.$row->district_id.'" data-courier_id="'.$row->courier_id.'" data-courier_package_id="'.$row->courier_package_id.'" data-status="'.$row->status.'" data-toggle="tooltip" title="Edit" class="btn btn-sm btn-warning edit" onClick="edit(this)"> <i class="fa fa-pencil"></i> </a>
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

    $location_mapping = LocationMappingModel::where(['location_district_id'=>$_POST['district_id'],'courier_package_id'=>$_POST['courier_package_id']])->first();

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
      $location_mapping->update_date = $date->format('Y-m-d H:i:s');
      $location_mapping->update_by = Auth::User()->email;
    }
    try {
      $success = $location_mapping->save();
    } catch (\Exception $ex) {
      $success = false;
      $message = $ex->getMessage();
    }

    if($success){
      foreach ($_POST['delivery_price'] as $key => $value) {
        if($success){
          $model = new DeliveryPriceModel;

          $model->courier_location_mapping_id = $location_mapping->id;
          $model->courier_price_category_id = $key;
          $model->price = $value;
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
      }
    }

    return response()->json(['success'=>$success,'message'=>$message]);
  }

  public function update(){
    $date = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s')); //initialize date parameter

    if(isset($_POST['id']) && isset($_POST['courier_package_id']) && isset($_POST['district_id']) && isset($_POST['status'])){
      $location_mapping = LocationMappingModel::where(['id'=>$_POST['id']])->first();

      $location_mapping->courier_package_id = $_POST['courier_package_id'];
      $location_mapping->location_district_id = $_POST['district_id'];
      $location_mapping->status = $_POST['status'];
      $location_mapping->update_date = $date->format('Y-m-d H:i:s');
      $location_mapping->update_by = Auth::User()->email;

      try {
        $success = $location_mapping->save();
        $message = "Edit data is success";
      } catch (\Exception $ex) {
        $success = false;
        $message = $ex->getMessage();
      }
    }else if(isset($_POST['delivery_price'])){
      $success = true;

      foreach($_POST['delivery_price'] as $key=>$value){
        if($success){
          $model = DeliveryPriceModel::where(['courier_location_mapping_id'=>$_POST['id'],'courier_price_category_id'=>$key])->first();

          $model->price = $value;
          $model->update_date = $date->format('Y-m-d H:i:s');
          $model->update_by = Auth::User()->email;

          try {
            $success = $model->save();
            $message = "Change data is success";
          } catch (\Exception $ex) {
            $success = false;
            $message = $ex->getMessage();
          }
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

    return DB::table(DB::raw('(SELECT	view_location.*, courier.name AS courier, courier.id AS courier_id, courier_package.id AS courier_package_id, courier_package.`name` AS courier_package,	courier_price_category.id AS price_category_id,	CONCAT(courier_price_category.`name`," (",courier_price_category.min_price," - ",IF(courier_price_category.max_price=0,"~",courier_price_category.max_price),")") AS price_category FROM `view_location`,	courier	LEFT JOIN courier_package ON courier_package.courier_id = courier.id,	courier_price_category WHERE courier_price_category.status="active") location_courier'))
              ->leftJoin('courier_location_mapping',function($join){
                                                   $join->on('courier_location_mapping.location_district_id','=','location_courier.district_id');
                                                   $join->on('courier_location_mapping.courier_package_id','=','location_courier.courier_package_id');
                                                 })
              ->leftJoin('courier_delivery_price',function($join){
                                                   $join->on('courier_delivery_price.courier_location_mapping_id','=','courier_location_mapping.id');
                                                   $join->on('courier_delivery_price.courier_price_category_id','=','location_courier.price_category_id');
                                                 })
              ->whereRaw('(courier_delivery_price.price IS NULL'.$isset_province.')')
              ->lists('location_courier.province','location_courier.province_id');
  }

  public function getCity(){
    $isset_city = "";
    if(isset($_POST['city_id'])){
      $isset_city = ' OR location_courier.city_id='.$_POST['city_id'];
    }

    if(isset($_POST['province_id']))
      $city = DB::table(DB::raw('(SELECT	view_location.*, courier.name AS courier, courier.id AS courier_id, courier_package.id AS courier_package_id, courier_package.`name` AS courier_package,	courier_price_category.id AS price_category_id,	CONCAT(courier_price_category.`name`," (",courier_price_category.min_price," - ",IF(courier_price_category.max_price=0,"~",courier_price_category.max_price),")") AS price_category FROM `view_location`,	courier	LEFT JOIN courier_package ON courier_package.courier_id = courier.id LEFT JOIN	courier_price_category ON courier_price_category.courier_id = courier.id WHERE courier_price_category.status="active" AND view_location.province_id = "'.$_POST['province_id'].'") location_courier'))
                ->leftJoin('courier_location_mapping',function($join){
                                                     $join->on('courier_location_mapping.location_district_id','=','location_courier.district_id');
                                                     $join->on('courier_location_mapping.courier_package_id','=','location_courier.courier_package_id');
                                                   })
                ->leftJoin('courier_delivery_price',function($join){
                                                     $join->on('courier_delivery_price.courier_location_mapping_id','=','courier_location_mapping.id');
                                                     $join->on('courier_delivery_price.courier_price_category_id','=','location_courier.price_category_id');
                                                   })
                ->whereRaw('(courier_delivery_price.price IS NULL'.$isset_city.')')
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
      $district = DB::table(DB::raw('(SELECT	view_location.*, courier.name AS courier, courier.id AS courier_id, courier_package.id AS courier_package_id, courier_package.`name` AS courier_package,	courier_price_category.id AS price_category_id,	CONCAT(courier_price_category.`name`," (",courier_price_category.min_price," - ",IF(courier_price_category.max_price=0,"~",courier_price_category.max_price),")") AS price_category FROM `view_location`,	courier	LEFT JOIN courier_package ON courier_package.courier_id = courier.id LEFT JOIN	courier_price_category ON courier_price_category.courier_id = courier.id WHERE courier_price_category.status="active" AND view_location.city_id = "'.$_POST['city_id'].'") location_courier'))
                ->leftJoin('courier_location_mapping',function($join){
                                                     $join->on('courier_location_mapping.location_district_id','=','location_courier.district_id');
                                                     $join->on('courier_location_mapping.courier_package_id','=','location_courier.courier_package_id');
                                                   })
                ->leftJoin('courier_delivery_price',function($join){
                                                     $join->on('courier_delivery_price.courier_location_mapping_id','=','courier_location_mapping.id');
                                                     $join->on('courier_delivery_price.courier_price_category_id','=','location_courier.price_category_id');
                                                   })
                ->whereRaw('(courier_delivery_price.price IS NULL'.$isset_district.')')
                ->lists('location_courier.district','location_courier.district_id');
    else
      $district = null;
    return $district;
  }

  public function getCourier(){
    $isset_courier = "";
    if(isset($_POST['courier_id'])){
      $isset_courier = ' OR location_courier.courier_id='.$_POST['courier_id'];
    }

    if(isset($_POST['district_id'])){
      $courier = DB::table(DB::raw('(SELECT	view_location.*, courier.name AS courier, courier.id AS courier_id, courier_package.id AS courier_package_id, courier_package.`name` AS courier_package,	courier_price_category.id AS price_category_id,	CONCAT(courier_price_category.`name`," (",courier_price_category.min_price," - ",IF(courier_price_category.max_price=0,"~",courier_price_category.max_price),")") AS price_category FROM `view_location`,	courier	LEFT JOIN courier_package ON courier_package.courier_id = courier.id LEFT JOIN	courier_price_category ON courier_price_category.courier_id = courier.id WHERE courier_price_category.status="active" AND view_location.district_id = "'.$_POST['district_id'].'") location_courier'))
                ->leftJoin('courier_location_mapping',function($join){
                                                     $join->on('courier_location_mapping.location_district_id','=','location_courier.district_id');
                                                     $join->on('courier_location_mapping.courier_package_id','=','location_courier.courier_package_id');
                                                   })
                ->leftJoin('courier_delivery_price',function($join){
                                                     $join->on('courier_delivery_price.courier_location_mapping_id','=','courier_location_mapping.id');
                                                     $join->on('courier_delivery_price.courier_price_category_id','=','location_courier.price_category_id');
                                                   })
                ->whereRaw('(courier_delivery_price.price IS NULL'.$isset_courier.')')
                ->lists('location_courier.courier','location_courier.courier_id');
    }else{
      $courier = null;
    }

    return $courier;
  }

  public function getCourierPackage(){
    $isset_courier_package = "";
    if(isset($_POST['courier_package_id'])){
      $isset_courier_package = ' OR location_courier.courier_package_id='.$_POST['courier_package_id'];
    }

    if(isset($_POST['district_id']) && isset($_POST['courier_id']))
      $courier_package = DB::table(DB::raw('(SELECT	view_location.*, courier.name AS courier, courier.id AS courier_id, courier_package.id AS courier_package_id, courier_package.`name` AS courier_package,	courier_price_category.id AS price_category_id,	CONCAT(courier_price_category.`name`," (",courier_price_category.min_price," - ",IF(courier_price_category.max_price=0,"~",courier_price_category.max_price),")") AS price_category FROM `view_location`,	courier	LEFT JOIN courier_package ON courier_package.courier_id = courier.id LEFT JOIN	courier_price_category ON courier_price_category.courier_id = courier.id WHERE courier_price_category.status="active" AND view_location.district_id = "'.$_POST['district_id'].'" AND courier.id = "'.$_POST['courier_id'].'") location_courier'))
                ->leftJoin('courier_location_mapping',function($join){
                                                     $join->on('courier_location_mapping.location_district_id','=','location_courier.district_id');
                                                     $join->on('courier_location_mapping.courier_package_id','=','location_courier.courier_package_id');
                                                   })
                ->leftJoin('courier_delivery_price',function($join){
                                                     $join->on('courier_delivery_price.courier_location_mapping_id','=','courier_location_mapping.id');
                                                     $join->on('courier_delivery_price.courier_price_category_id','=','location_courier.price_category_id');
                                                   })
                ->whereRaw('(courier_delivery_price.price IS NULL'.$isset_courier_package.')')
                ->lists('location_courier.courier_package','location_courier.courier_package_id');
    else
      $courier_package = null;

    return $courier_package;
  }

  public function getPriceCategory(){
    if(isset($_POST['courier_id']) && isset($_POST['id'])){
      $price_category = array();

      $models = DeliveryPriceModel::select(DB::raw('CONCAT(courier_price_category.name," (",courier_price_category.min_price," - ",courier_price_category.max_price,")") AS price_category'),'courier_price_category.id AS price_category_id','courier_delivery_price.price')
                                ->join('courier_price_category',function($join){
                                           $join->on('courier_price_category.id','=','courier_delivery_price.courier_price_category_id');
                                           $join->on('courier_price_category.courier_id','=',DB::raw('"'.$_POST['courier_id'].'"'));
                                         })
                                ->where(['courier_price_category.status'=>'active','courier_delivery_price.courier_location_mapping_id'=>$_POST['id']])
                                ->get();

      foreach($models as $model){
        $price_category[] = array("price_category"=>$model->price_category,"price_category_id"=>$model->price_category_id,"price"=>$model->price);
      }
    }else if(isset($_POST['courier_id'])){
      $price_category = PriceCategoryModel::select(DB::raw('CONCAT(courier_price_category.name," (",courier_price_category.min_price," - ",courier_price_category.max_price,")") AS price_category'),'courier_price_category.id')
                                          ->where(['courier_id'=>$_POST['courier_id'],'status'=>'active'])
                                          ->lists('price_category','courier_price_category.id');
    }else{
      $price_category = null;
    }

    return response()->json($price_category);
  }
}
