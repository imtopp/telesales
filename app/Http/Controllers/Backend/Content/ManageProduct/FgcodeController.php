<?php

namespace App\Http\Controllers\Backend\Content\ManageProduct;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesResources;
use Illuminate\Support\Facades\Auth;
use App\Models\ProductCategory as ProductCategoryModel;
use App\Models\Product as ProductModel;
use App\Models\ProductColour as ProductColourModel;
use App\Models\ProductFgCode as ProductFgCodeModel;
use DateTime;
use DB;
use File;
use Input;
use URL;

class FgcodeController extends BaseController
{
  use AuthorizesRequests, AuthorizesResources, DispatchesJobs, ValidatesRequests;

  //Render Page
  public function index(){
    $product_colour_list = array();
    $product_colours = ProductColourModel::get();
    foreach($product_colours as $product_colour){
      $product = ProductModel::where(['id'=>$product_colour->product_id])->first();
      $product_colour_list[$product_colour->id] = $product->name." - ".$product_colour->name;
    }
    return view('backend/content/product/fgcode',['product_colour'=>$product_colour_list]);
  }

  //Read All FGCODE
  public function read(){
    // storing  request (ie, get/post) global array to a variable
    $requestData= $_REQUEST;

    $columns = array(
    // datatable column index  => database column name
        0 => 'fg_code',
        1 => 'category',
        2 => 'product',
        3 => 'colour',
        4 => 'price',
        5 => 'status'
    );

    $totalData = ProductFgCodeModel::count();
    $totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

    if( !empty($requestData['search']['value']) ) {
      // if there is a search parameter
      $model = DB::table('product_fg_code')
                ->select('product_fg_code.id', 'product_fg_code.fg_code', 'product_category.name AS category', 'product_category.id AS category_id', 'product.name AS product', 'product.id AS product_id', 'product_colour.name AS colour', 'product_colour.id AS colour_id', 'product_fg_code.price', 'product_fg_code.status')
                ->join('product_colour','product_colour.id','=','product_fg_code.product_colour_id')
                ->join('product','product.id','=','product_colour.product_id')
                ->join('product_category','product_category.id','=','product.category_id')
                ->where('product_fg_code.fg_code','LIKE',$requestData['search']['value'].'%')
                ->orWhere('product_category.name','LIKE',$requestData['search']['value'].'%')
                ->orWhere('product.name','LIKE',$requestData['search']['value'].'%')
                ->orWhere('product_colour.name','LIKE',$requestData['search']['value'].'%')
                ->orWhere('product_fg_code.price','LIKE',$requestData['search']['value'].'%')
                ->orWhere('product_fg_code.status','LIKE',$requestData['search']['value'].'%');
      $totalFiltered = $model->count();
      $query = $model
                ->orderBy($columns[$requestData['order'][0]['column']],$requestData['order'][0]['dir'])
                ->skip($requestData['start'])
                ->take($requestData['length'])
                ->get();
    } else {
      $query = DB::table('product_fg_code')
                ->select('product_fg_code.id', 'product_fg_code.fg_code', 'product_category.name AS category', 'product_category.id AS category_id', 'product.name AS product', 'product.id AS product_id', 'product_colour.name AS colour', 'product_colour.id AS colour_id', 'product_fg_code.price', 'product_fg_code.status')
                ->join('product_colour','product_colour.id','=','product_fg_code.product_colour_id')
                ->join('product','product.id','=','product_colour.product_id')
                ->join('product_category','product_category.id','=','product.category_id')
                ->orderBy($columns[$requestData['order'][0]['column']],$requestData['order'][0]['dir'])
                ->skip($requestData['start'])
                ->take($requestData['length'])
                ->get();
    }

    $data = array();
    foreach($query as $row) {  // preparing an array
        $nestedData=array();

        $nestedData[$columns[0]] = $row->fg_code;
        $nestedData[$columns[1]] = $row->category;
        $nestedData[$columns[2]] = $row->product;
        $nestedData[$columns[3]] = $row->colour;
        $nestedData[$columns[4]] = $row->price;
        $nestedData[$columns[5]] = $row->status;
        $nestedData['action'] = '<td><center>
                           <a href="#" data-id="'.$row->id.'" data-fg_code="'.$row->fg_code.'" data-category_id="'.$row->category_id.'" data-product_id="'.$row->product_id.'" data-colour_id="'.$row->colour_id.'" data-price="'.$row->price.'" data-status="'.$row->status.'" data-toggle="tooltip" title="Edit" class="btn btn-sm btn-warning edit" onClick="edit(this)"> <i class="fa fa-pencil"></i> </a>
                           <a href="#" data-id="'.$row->id.'" data-fg_code="'.$row->fg_code.'" data-toggle="tooltip" title="Hapus" class="btn btn-sm btn-danger destroy" onClick="destroy(this)"> <i class="fa fa-trash"></i> </a>
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

  //Create New FGCODE
  public function create(){
    $date = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s')); //initialize date parameter

    $model = new ProductFgCodeModel;
    $model->fg_code = $_POST['fg_code'];
    $model->product_colour_id = $_POST['colour_id'];
    $model->price = $_POST['price'];
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

  //Update Exsiting FGCODE
  public function update(){
    $date = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s')); //initialize date parameter
    $model = ProductFgCodeModel::where(['id'=>$_POST['id']])->first();

    $model->fg_code = $_POST['fg_code'];
    $model->product_colour_id = $_POST['colour_id'];
    $model->price = $_POST['price'];
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

  //Destroy Existing FGCODE
  public function destroy(){
    try {
      $success = ProductFgCodeModel::destroy($_POST['id']);
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

  //Get All Related Category
  public function getCategory(){
    $models = ProductCategoryModel::join('product','product.category_id','=','product_category.id')
                            ->join('product_colour','product_colour.product_id','=','product.id')
                            ->lists('product_category.name','product_category.id');

    return $models;
  }

  //Get All Related Product
  public function getProduct(){;
    $models = ProductModel::where(['category_id'=>$_POST['category_id']])->lists('name','id');

    return $models;
  }

  //Get All Related Colour
  public function getColour(){;
    $models = ProductColourModel::where(['product_id'=>$_POST['product_id']])->lists('name','id');

    return $models;
  }
}
