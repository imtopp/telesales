<?php

namespace App\Http\Controllers\Backend\Content\ManageCourier;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesResources;
use Illuminate\Support\Facades\Auth;
use App\Models\Courier as CourierModel;
use App\Models\CourierPriceCategory as PriceCategoryModel;
use DateTime;
use DB;
use URL;

class CourierController extends BaseController
{
  use AuthorizesRequests, AuthorizesResources, DispatchesJobs, ValidatesRequests;

  //Render Page
  public function index(){
    return view('backend/content/courier/courier');
  }

  //Read All Courier
  public function read(){
    // storing  request (ie, get/post) global array to a variable
    $requestData= $_REQUEST;

    $columns = array(
    // datatable column index  => database column name
        0 => 'name',
        1 => 'status'
    );

    $totalData = CourierModel::count();
    $totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

    if( !empty($requestData['search']['value']) ) {
      // if there is a search parameter
      $model = DB::table('courier')
      ->select('courier.id', 'courier.name', 'courier.status')
                ->where('courier.name','LIKE',$requestData['search']['value'].'%')
                ->orWhere('courier.status','LIKE',$requestData['search']['value'].'%');
      $totalFiltered = $model->count();
      $query = $model
                ->orderBy($columns[$requestData['order'][0]['column']],$requestData['order'][0]['dir'])
                ->skip($requestData['start'])
                ->take($requestData['length'])
                ->get();
    } else {
      $query = DB::table('courier')
                ->select('courier.id', 'courier.name', 'courier.status')
                ->orderBy($columns[$requestData['order'][0]['column']],$requestData['order'][0]['dir'])
                ->skip($requestData['start'])
                ->take($requestData['length'])
                ->get();
    }

    $data = array();
    foreach($query as $row) {  // preparing an array
        $price_category_exists = PriceCategoryModel::where(['courier_id'=>$row->id])->count();
        $nestedData=array();

        $nestedData[$columns[0]] = $row->name;
        $nestedData[$columns[1]] = $row->status;
        $nestedData['price_category'] = '<td><center>
                           <a data-id="'.$row->id.'" data-reset="'.($price_category_exists?'true':'false').'" data-toggle="tooltip" title="'.($price_category_exists?'Reset Price Category':'Create Price Category').'" class="btn btn-sm btn-'.($price_category_exists?'danger':'primary').' edit" onClick="price_category(this)">'.($price_category_exists?' Reset Price Category':' Create Price Category').'</a>
                           </center></td>';
        $nestedData['action'] = '<td><center>
                           <a data-id="'.$row->id.'" data-name="'.$row->name.'" data-status="'.$row->status.'" data-toggle="tooltip" title="Edit" class="btn btn-sm btn-warning edit" onClick="edit(this)"> <i class="fa fa-pencil"></i> </a>
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

  //Create New Courier
  public function create(){
    $date = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s')); //initialize date parameter
    $model = new CourierModel;

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

  //Update Existing Courier
  public function update(){
    $date = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s')); //initialize date parameter
    $model = CourierModel::where(['id'=>$_POST['id']])->first();

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

  //Destroy Existing Courier
  public function destroy(){
    try {
      $success = CourierModel::destroy($_POST['id']);
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

  public function priceCategory(){
    $date = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s')); //initialize date parameter
    $success = true;

    $exsiting = PriceCategoryModel::where(['courier_id'=>$_POST['id'],'status'=>'active'])->get();

    foreach($exsiting as $model){
      if($success){
        $model->status = 'inactive';

        try {
          $success = $model->save();
        } catch (\Exception $ex) {
          $success = false;
          $message = $ex->getMessage();
        }
      }
    }

    foreach($_POST as $key=>$value){
      if(substr($key,0,8)=="category" && $success){
        $model = new PriceCategoryModel;

        $model->courier_id = $_POST['id'];
        $model->name = $key;
        $model->min_price = $value['min_price'];
        $model->max_price = $value['max_price']=="~"?0:$value['max_price'];
        $model->input_date = $date->format('Y-m-d H:i:s');
        $model->input_by = Auth::User()->email;
        $model->update_date = $date->format('Y-m-d H:i:s');
        $model->update_by = Auth::User()->email;

        try {
          $success = $model->save();
          $message = 'Input data is success!';
        } catch (\Exception $ex) {
          $success = false;
          $message = $ex->getMessage();
        }
      }
    }

    return response()->json(['success'=>$success,'message'=>$message]);
  }
}
