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
use DateTime;
use DB;
use File;
use Input;
use URL;

class ProductController extends BaseController
{
  use AuthorizesRequests, AuthorizesResources, DispatchesJobs, ValidatesRequests;

  //Render Page
  public function index(){
    return view('backend/content/product/product');
  }

  //Read All Product
  public function read(){
    // storing  request (ie, get/post) global array to a variable
    $requestData= $_REQUEST;

    $columns = array(
    // datatable column index  => database column name
        0 => 'name',
        1 => 'category',
        2 => 'description',
        3 => 'image_url',
        4 => 'hit_count',
        5 => 'status'
    );

    $totalData = ProductModel::count();
    $totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

    if( !empty($requestData['search']['value']) ) {
      // if there is a search parameter
      $model = DB::table('product')
                ->select('product.id', 'product.name', 'product_category.name AS category', 'product_category.id AS category_id', 'product.description', 'product.image_url', 'product.hit_count', 'product.status')
                ->join('product_category','product_category.id','=','product.category_id')
                ->where('product.name','LIKE',$requestData['search']['value'].'%')
                ->orWhere('product_category.name','LIKE',$requestData['search']['value'].'%')
                ->orWhere('product.status','LIKE',$requestData['search']['value'].'%');
      $totalFiltered = $model->count();
      $query = $model
                ->orderBy($columns[$requestData['order'][0]['column']],$requestData['order'][0]['dir'])
                ->skip($requestData['start'])
                ->take($requestData['length'])
                ->get();
    } else {
      $query = DB::table('product')
                ->select('product.id', 'product.name', 'product_category.name AS category', 'product_category.id AS category_id', 'product.description', 'product.image_url', 'product.hit_count', 'product.status')
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
        $nestedData[$columns[2]] = $row->description;
        $nestedData[$columns[3]] = URL::asset($row->image_url);
        $nestedData[$columns[4]] = $row->hit_count;
        $nestedData[$columns[5]] = $row->status;
        $nestedData['action'] = '<td><center>
                           <a data-id="'.$row->id.'" data-name="'.$row->name.'" data-category_id="'.$row->category_id.'" data-hit_count="'.$row->hit_count.'" data-status="'.$row->status.'" data-toggle="tooltip" title="Edit" class="btn btn-sm btn-warning edit" onClick="edit(this)"> <i class="fa fa-pencil"></i> </a>
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

  //Create New Product
  public function create(){
    $date = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s')); //initialize date parameter

    $model = new ProductModel;
    $model->name = $_POST['name'];
    $model->category_id = $_POST['category_id'];
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

  //Update Existing Product
  public function update(){
    $date = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s')); //initialize date parameter
    $model = ProductModel::where(['id'=>$_POST['id']])->first();

    if($model->name!=$_POST['name']){
      if(File::exists($model->image_url)){
        $fileName = end(explode("/", $model->image_url));
        $extension = end(explode(".", $fileName));
        $destinationPath = 'assets/img/product/'.strtolower(str_replace(' ','_',$_POST['name']));

        rename($model->image_url,$destinationPath.'/'.$_POST['id'].'.'.$extension);
      }
      $model->image_url = $destinationPath.'/'.$_POST['id'].'.'.$extension;
    }

    $model->name = $_POST['name'];
    $model->category_id = $_POST['category_id'];
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

  //Destroy Existing Product
  public function destroy(){
    try {
      $success = ProductModel::destroy($_POST['id']);
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

  //Get All Product Category
  public function getCategory(){
    $models = ProductCategoryModel::lists('name','id');

    return $models;
  }

  //Upload Product Image
  public function upload(){
    $product = ProductModel::where(['id'=>$_POST['id']])->first();

    // check image is valid and store it
    if (Input::file('upload_image')->isValid()) {
      $destinationPath = 'assets/img/product/'.strtolower(str_replace(' ','_',$product->name)); // upload path base_path() . '/public/assets/'
      $destinationAvail = File::exists($destinationPath);
      $success=$destinationAvail;
      if(!$destinationAvail)
        $success = File::makeDirectory($destinationPath);

      $extension = Input::file('upload_image')->getClientOriginalExtension(); // getting image extension
      $fileName = $product->id.'_temp.'.$extension; // renameing image
      if($success)
        $success=Input::file('upload_image')->move($destinationPath, $fileName); // uploading file to given path
      if($success && File::exists($product->image_url)){
        $success=File::delete($product->image_url);
      }
      if($success)
        rename($destinationPath.'/'.$fileName,$destinationPath.'/'.$product->id.'.'.$extension);
      if($success){
        $product->image_url=$destinationPath.'/'.$product->id.'.'.$extension;
        try {
          $success=$product->save();
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

  //Update Product Description
  public function description(){
    $product = ProductModel::where(['id'=>$_POST['id']])->first();

    $product->description = $_POST['description'];

    try {
      $success=$product->save();
      $message="Update description sukses!";
    } catch (\Exception $ex) {
      $success = false;
      $message = $ex->getMessage();
    }

    return response()->json(['success'=>$success,'message'=>$message]);
  }
}
