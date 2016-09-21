<?php

namespace App\Http\Controllers\Backend;

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
use DB;
use DateTime;

class ManageProductController extends BaseController
{
  use AuthorizesRequests, AuthorizesResources, DispatchesJobs, ValidatesRequests;

  //public function for showing all product at frontpage
  public function category(){
    return view('backend/content/product/category'); //display list_product view with all_category and all_product
  }

  public function categoryRead(){
    // storing  request (ie, get/post) global array to a variable
    $requestData= $_REQUEST;

    $columns = array(
    // datatable column index  => database column name
        0 => 'name',
        1 => 'status'
    );

    $totalData = ProductCategoryModel::count();
    $totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

    if( !empty($requestData['search']['value']) ) {
      // if there is a search parameter
      $model = DB::table('product_category')
                ->select('product_category.id', 'product_category.name', 'product_category.status')
                ->where('product_category.name','LIKE',$requestData['search']['value'].'%')
                ->orWhere('product_category.status','LIKE',$requestData['search']['value'].'%');
      $totalFiltered = $model->count();
      $query = $model
                ->orderBy($columns[$requestData['order'][0]['column']],$requestData['order'][0]['dir'])
                ->skip($requestData['start'])
                ->take($requestData['length'])
                ->get();
    } else {
      $query = DB::table('product_category')
                ->select('product_category.id', 'product_category.name', 'product_category.status')
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

  public function categoryCreate(){
    $date = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s')); //initialize date parameter

    $category = new ProductCategoryModel;
    $category->name = $_POST['name'];
    $category->status = $_POST['status'];
    $category->input_date = $date->format('Y-m-d H:i:s');
    $category->input_by = Auth::User()->email;
    $category->update_date = $date->format('Y-m-d H:i:s');
    $category->update_by = Auth::User()->email;

    try {
      $success = $category->save();
      $message = 'Create new category is success!';
    } catch (Exception $ex) {
      $success = false;
      $message = $ex->getMessage();
    }

    return response()->json(['success'=>$success,'message'=>$message]);
  }

  public function categoryUpdate(){
    $date = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s')); //initialize date parameter
    $category = ProductCategoryModel::where(['id'=>$_POST['id']])->first();

    $category->name = $_POST['name'];
    $category->status = $_POST['status'];
    $category->update_date = $date->format('Y-m-d H:i:s');
    $category->update_by = Auth::User()->email;

    try {
      $success = $category->save();
      $message = 'Edit category is success!';
    } catch (Exception $ex) {
      $success = false;
      $message = $ex->getMessage();
    }

    return response()->json(['success'=>$success,'message'=>$message]);
  }

  public function categoryDestroy(){
    try {
      $success = ProductCategoryModel::destroy($_POST['id']);
      $message = 'Delete category is success!';
    } catch (Exception $ex) {
      $success = false;
      $message = $ex->getMessage();
    }

    return response()->json(['success'=>$success,'message'=>$message]);
  }
}
