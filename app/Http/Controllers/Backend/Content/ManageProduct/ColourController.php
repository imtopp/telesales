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
use DateTime;
use DB;
use File;
use Input;
use URL;

class ColourController extends BaseController
{
  use AuthorizesRequests, AuthorizesResources, DispatchesJobs, ValidatesRequests;

  //Render Page
  public function index(){
    $product = ProductModel::lists('name','id');
    return view('backend/content/product/colour',['product'=>$product]);
  }

  //Read All Colour
  public function read(){
    // storing  request (ie, get/post) global array to a variable
    $requestData= $_REQUEST;

    $columns = array(
    // datatable column index  => database column name
        0 => 'name',
        1 => 'category',
        2 => 'product',
        3 => 'image_url',
        4 => 'status'
    );

    $totalData = ProductColourModel::count();
    $totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

    if( !empty($requestData['search']['value']) ) {
      // if there is a search parameter
      $model = DB::table('product_colour')
                ->select('product_colour.id', 'product_colour.name', 'product_category.name AS category', 'product_category.id AS category_id', 'product.name AS product', 'product.id AS product_id', 'product_colour.image_url', 'product_colour.status')
                ->join('product','product.id','=','product_colour.product_id')
                ->join('product_category','product_category.id','=','product.category_id')
                ->where('product_colour.name','LIKE',$requestData['search']['value'].'%')
                ->orWhere('product.name','LIKE',$requestData['search']['value'].'%')
                ->orWhere('product_category.name','LIKE',$requestData['search']['value'].'%')
                ->orWhere('product_colour.status','LIKE',$requestData['search']['value'].'%');
      $totalFiltered = $model->count();
      $query = $model
                ->orderBy($columns[$requestData['order'][0]['column']],$requestData['order'][0]['dir'])
                ->skip($requestData['start'])
                ->take($requestData['length'])
                ->get();
    } else {
      $query = DB::table('product_colour')
                ->select('product_colour.id', 'product_colour.name', 'product_category.name AS category', 'product_category.id AS category_id', 'product.name AS product', 'product.id AS product_id', 'product_colour.image_url', 'product_colour.status')
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

        $nestedData[$columns[0]] = $row->name;
        $nestedData[$columns[1]] = $row->category;
        $nestedData[$columns[2]] = $row->product;
        $nestedData[$columns[3]] = URL::asset($row->image_url);
        $nestedData[$columns[4]] = $row->status;
        $nestedData['action'] = '<td><center>
                           <a href="#" data-id="'.$row->id.'" data-name="'.$row->name.'" data-product_id="'.$row->product_id.'" data-category_id="'.$row->category_id.'" data-image_url="'.$row->image_url.'" data-status="'.$row->status.'" data-toggle="tooltip" title="Edit" class="btn btn-sm btn-warning edit" onClick="edit(this)"> <i class="fa fa-pencil"></i> </a>
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

  //Create New Colour
  public function create(){
    $date = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s')); //initialize date parameter

    $model = new ProductColourModel;
    $model->name = $_POST['name'];
    $model->product_id = $_POST['product_id'];
    $model->image_url = "image.jpg";
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

  //Update Existing Colour
  public function update(){
    $date = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s')); //initialize date parameter
    $model = ProductColourModel::where(['id'=>$_POST['id']])->first();

    if($model->product_id!=$_POST['product_id']){
      $product = ProductModel::where(['id'=>$_POST['product_id']])->first();
      if(File::exists($model->image_url)){
        $fileName = end(explode("/", $model->image_url));
        $destinationPath = 'assets/img/product/'.strtolower(str_replace(' ','_',$product->name)).'/variant';

        rename($model->image_url,$destinationPath.'/'.$fileName);
        $model->image_url = $destinationPath.'/'.$fileName;
      }
    }

    $model->name = $_POST['name'];
    $model->product_id = $_POST['product_id'];
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

  //Destroy Existing Colour
  public function destroy(){
    try {
      $colour = ProductColourModel::where(['id'=>$_POST['id']])->first();
      $success = ProductColourModel::destroy($_POST['id']);
      if($success && File::exists($colour->image_url)){
        $success=File::delete($colour->image_url);
      }
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
                            ->lists('product_category.name','product_category.id');

    return $models;
  }

  //Get All Related Product
  public function getProduct(){;
    $models = ProductModel::where(['category_id'=>$_POST['category_id']])->lists('name','id');

    return $models;
  }

  //Upload Colour Image
  public function upload(){
    $colour = ProductColourModel::where(['id'=>$_POST['id']])->first();
    $product = ProductModel::where(['id'=>$colour->product_id])->first();

    // check image is valid and store it
    if (Input::file('upload_image')->isValid()) {
      $destinationPath = 'assets/img/product/'.strtolower(str_replace(' ','_',$product->name)).'/variant'; // upload path base_path() . '/public/assets/'
      $destinationAvail=File::exists($destinationPath);
      $success=$destinationAvail;
      if(!$destinationAvail)
        $success = File::makeDirectory($destinationPath);

      $extension = Input::file('upload_image')->getClientOriginalExtension(); // getting image extension
      $fileName = $colour->id.'_temp.'.$extension; // renameing image
      if($success)
        $success=Input::file('upload_image')->move($destinationPath, $fileName); // uploading file to given path
      if($success && File::exists($colour->image_url)){
        $success=File::delete($colour->image_url);
      }
      if($success)
        rename($destinationPath.'/'.$fileName,$destinationPath.'/'.$colour->id.'.'.$extension);
      if($success){
        $colour->image_url=$destinationPath.'/'.$colour->id.'.'.$extension;
        try {
          $success=$colour->save();
          $message="Upload file sukses!";
        } catch (\Exception $ex) {
          $success = false;
          $message = $ex->getMessage();
        }
      }
    }
    else {
      $success=false;
      $message='file yang di upload tidak valid';
    }

    return response()->json(['success'=>$success,'message'=>$message]);
  }
}
