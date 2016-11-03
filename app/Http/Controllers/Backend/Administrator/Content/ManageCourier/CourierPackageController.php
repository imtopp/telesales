<?php

namespace App\Http\Controllers\Backend\Administrator\Content\ManageCourier;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesResources;
use Illuminate\Support\Facades\Auth;
use App\Models\Courier as CourierModel;
use App\Models\CourierPackage as CourierPackageModel;
use DateTime;
use DB;
use URL;

class CourierPackageController extends BaseController
{
  use AuthorizesRequests, AuthorizesResources, DispatchesJobs, ValidatesRequests;

  //Render Page
  public function index(){
    return view('backend/administrator/content/courier/courier_package');
  }

  //Read All Courier Package
  public function read(){
    // storing  request (ie, get/post) global array to a variable
    $requestData= $_REQUEST;

    $columns = array(
    // datatable column index  => database column name
        0 => 'name',
        1 => 'courier',
        2 => 'status'
    );

    $totalData = CourierPackageModel::count();
    $totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

    if( !empty($requestData['search']['value']) ) {
      // if there is a search parameter
      $model = DB::table('courier_package')
                ->select('courier_package.id', 'courier_package.name', 'courier.name AS province', 'courier.id AS courier_id', 'courier_package.status')
                ->join('courier','courier.id','=','courier_package.courier_id')
                ->where('courier_package.name','LIKE',$requestData['search']['value'].'%')
                ->orWhere('courier_package.status','LIKE',$requestData['search']['value'].'%')
                ->orWhere('courier.name','LIKE',$requestData['search']['value'].'%');
      $totalFiltered = $model->count();
      $query = $model
                ->orderBy($columns[$requestData['order'][0]['column']],$requestData['order'][0]['dir'])
                ->skip($requestData['start'])
                ->take($requestData['length'])
                ->get();
    } else {
      $query = DB::table('courier_package')
                ->select('courier_package.id', 'courier_package.name', 'courier.name AS province', 'courier.id AS courier_id', 'courier_package.status')
                ->join('courier','courier.id','=','courier_package.courier_id')
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
                           <a data-id="'.$row->id.'" data-name="'.$row->name.'" data-courier_id="'.$row->courier_id.'" data-status="'.$row->status.'" data-toggle="tooltip" title="Edit" class="btn btn-sm btn-warning edit" onClick="edit(this)"> <i class="fa fa-pencil"></i> </a>
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
    DB::beginTransaction();
    $model = new CourierPackageModel;

    $model->name = $_POST['name'];
    $model->courier_id = $_POST['courier_id'];
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

  //Update Existing Courier Package
  public function update(){
    $date = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s')); //initialize date parameter
    DB::beginTransaction();
    $model = CourierPackageModel::where(['id'=>$_POST['id']])->first();

    $model->name = $_POST['name'];
    $model->courier_id = $_POST['courier_id'];
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

  //Destroy Existing Courier
  public function destroy(){
    DB::beginTransaction();

    try {
      $success = CourierPackageModel::destroy($_POST['id']);
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

  //Get All Product Category
  public function getCourier(){
    $models = CourierModel::lists('name','id');

    return $models;
  }
}
