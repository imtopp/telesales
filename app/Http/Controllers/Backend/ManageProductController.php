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
use DateTime;
use DB;
use File;
use Input;
use URL;

class ManageProductController extends BaseController
{
  use AuthorizesRequests, AuthorizesResources, DispatchesJobs, ValidatesRequests;

  //Category
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

    $model = new ProductCategoryModel;
    $model->name = $_POST['name'];
    $model->status = $_POST['status'];
    $model->input_date = $date->format('Y-m-d H:i:s');
    $model->input_by = Auth::User()->email;
    $model->update_date = $date->format('Y-m-d H:i:s');
    $model->update_by = Auth::User()->email;

    try {
      $success = $category->save();
      $message = 'Create new data is success!';
    } catch (\Exception $ex) {
      $success = false;
      $message = $ex->getMessage();
    }

    return response()->json(['success'=>$success,'message'=>$message]);
  }

  public function categoryUpdate(){
    $date = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s')); //initialize date parameter
    $model = ProductCategoryModel::where(['id'=>$_POST['id']])->first();

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

  public function categoryDestroy(){
    try {
      $success = ProductCategoryModel::destroy($_POST['id']);
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

  //product
  public function product(){
    $category = ProductCategoryModel::lists('name','id');
    return view('backend/content/product/product',['category'=>$category]); //display list_product view with all_category and all_product
  }

  public function productRead(){
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
                           <a href="#" data-id="'.$row->id.'" data-name="'.$row->name.'" data-category_id="'.$row->category_id.'" data-hit_count="'.$row->hit_count.'" data-status="'.$row->status.'" data-toggle="tooltip" title="Edit" class="btn btn-sm btn-warning edit" onClick="edit(this)"> <i class="fa fa-pencil"></i> </a>
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

  public function productCreate(){
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

  public function productUpdate(){
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

  public function productDestroy(){
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

    return response()->json(['success'=>$success,'message'=>$message,'error_message'=>$error_message]);
  }

  public function productUpload(){
    $product = ProductModel::where(['id'=>$_POST['id']])->first();

    // getting all of the post data
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

  public function productDescription(){
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

  //colour
  public function colour(){
    $product = ProductModel::lists('name','id');
    return view('backend/content/product/colour',['product'=>$product]); //display list_product view with all_category and all_product
  }

  public function colourRead(){
    // storing  request (ie, get/post) global array to a variable
    $requestData= $_REQUEST;

    $columns = array(
    // datatable column index  => database column name
        0 => 'name',
        1 => 'product',
        2 => 'image_url',
        3 => 'status'
    );

    $totalData = ProductColourModel::count();
    $totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

    if( !empty($requestData['search']['value']) ) {
      // if there is a search parameter
      $model = DB::table('product_colour')
                ->select('product_colour.id', 'product_colour.name', 'product.name AS product', 'product.id AS product_id', 'product_colour.image_url', 'product_colour.status')
                ->join('product','product.id','=','product_colour.product_id')
                ->where('product_colour.name','LIKE',$requestData['search']['value'].'%')
                ->orWhere('product.name','LIKE',$requestData['search']['value'].'%')
                ->orWhere('product_colour.status','LIKE',$requestData['search']['value'].'%');
      $totalFiltered = $model->count();
      $query = $model
                ->orderBy($columns[$requestData['order'][0]['column']],$requestData['order'][0]['dir'])
                ->skip($requestData['start'])
                ->take($requestData['length'])
                ->get();
    } else {
      $query = DB::table('product_colour')
                ->select('product_colour.id', 'product_colour.name', 'product.name AS product', 'product.id AS product_id', 'product_colour.image_url', 'product_colour.status')
                ->join('product','product.id','=','product_colour.product_id')
                ->orderBy($columns[$requestData['order'][0]['column']],$requestData['order'][0]['dir'])
                ->skip($requestData['start'])
                ->take($requestData['length'])
                ->get();
    }

    $data = array();
    foreach($query as $row) {  // preparing an array
        $nestedData=array();

        $nestedData[$columns[0]] = $row->name;
        $nestedData[$columns[1]] = $row->product;
        $nestedData[$columns[2]] = URL::asset($row->image_url);
        $nestedData[$columns[3]] = $row->status;
        $nestedData['action'] = '<td><center>
                           <a href="#" data-id="'.$row->id.'" data-name="'.$row->name.'" data-product_id="'.$row->product_id.'" data-image_url="'.$row->image_url.'" data-status="'.$row->status.'" data-toggle="tooltip" title="Edit" class="btn btn-sm btn-warning edit" onClick="edit(this)"> <i class="fa fa-pencil"></i> </a>
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

  public function colourCreate(){
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

  public function colourUpdate(){
    $date = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s')); //initialize date parameter
    $model = ProductColourModel::where(['id'=>$_POST['id']])->first();

    if($model->product_id!=$_POST['product_id']){
      $product = ProductModel::where(['id'=>$_POST['product_id']])->first();
      if(File::exists($model->image_url)){
        $fileName = end(explode("/", $model->image_url));
        $destinationPath = 'assets/img/product/'.strtolower(str_replace(' ','_',$product->name)).'/variant';

        rename($model->image_url,$destinationPath.'/'.$fileName);
      }
      $model->image_url = $destinationPath.'/'.$fileName;
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

  public function colourDestroy(){
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

    return response()->json(['success'=>$success,'message'=>$message,'error_message'=>$error_message]);
  }

  public function colourUpload(){
    $colour = ProductColourModel::where(['id'=>$_POST['id']])->first();
    $product = ProductModel::where(['id'=>$colour->product_id])->first();

    // getting all of the post data
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

  //fg_code
  public function fg_code(){
    $product_colour_list = array();
    $product_colours = ProductColourModel::get();
    foreach($product_colours as $product_colour){
      $product = ProductModel::where(['id'=>$product_colour->product_id])->first();
      $product_colour_list[$product_colour->id] = $product->name." - ".$product_colour->name;
    }
    return view('backend/content/product/fg_code',['product_colour'=>$product_colour_list]); //display list_product view with all_category and all_product
  }

  public function fg_codeRead(){
    // storing  request (ie, get/post) global array to a variable
    $requestData= $_REQUEST;

    $columns = array(
    // datatable column index  => database column name
        0 => 'fg_code',
        1 => 'product_colour',
        2 => 'price',
        3 => 'status'
    );

    $totalData = ProductFgCodeModel::count();
    $totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

    if( !empty($requestData['search']['value']) ) {
      // if there is a search parameter
      $model = DB::table('product_fg_code')
                ->select('product_fg_code.id', 'product_fg_code.fg_code', 'product.name AS product_name', 'product_colour.name AS product_colour', 'product_colour.id AS product_colour_id', 'product_fg_code.price', 'product_fg_code.status')
                ->join('product_colour','product_colour.id','=','product_fg_code.product_colour_id')
                ->join('product','product.id','=','product_colour.product_id')
                ->where('product_fg_code.fg_code','LIKE',$requestData['search']['value'].'%')
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
                ->select('product_fg_code.id', 'product_fg_code.fg_code', 'product.name AS product_name', 'product_colour.name AS product_colour', 'product_colour.id AS product_colour_id', 'product_fg_code.price', 'product_fg_code.status')
                ->join('product_colour','product_colour.id','=','product_fg_code.product_colour_id')
                ->join('product','product.id','=','product_colour.product_id')
                ->orderBy($columns[$requestData['order'][0]['column']],$requestData['order'][0]['dir'])
                ->skip($requestData['start'])
                ->take($requestData['length'])
                ->get();
    }

    $data = array();
    foreach($query as $row) {  // preparing an array
        $nestedData=array();

        $nestedData[$columns[0]] = $row->fg_code;
        $nestedData[$columns[1]] = $row->product_name." - ".$row->product_colour;
        $nestedData[$columns[2]] = $row->price;
        $nestedData[$columns[3]] = $row->status;
        $nestedData['action'] = '<td><center>
                           <a href="#" data-id="'.$row->id.'" data-fg_code="'.$row->fg_code.'" data-product_colour_id="'.$row->product_colour_id.'" data-price="'.$row->price.'" data-status="'.$row->status.'" data-toggle="tooltip" title="Edit" class="btn btn-sm btn-warning edit" onClick="edit(this)"> <i class="fa fa-pencil"></i> </a>
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

  public function fg_codeCreate(){
    $date = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s')); //initialize date parameter

    $model = new ProductFgCodeModel;
    $model->fg_code = $_POST['fg_code'];
    $model->product_colour_id = $_POST['product_colour_id'];
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

  public function fg_codeUpdate(){
    $date = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s')); //initialize date parameter
    $model = ProductFgCodeModel::where(['id'=>$_POST['id']])->first();

    $model->fg_code = $_POST['fg_code'];
    $model->product_colour_id = $_POST['product_colour_id'];
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

  public function fg_codeDestroy(){
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

    return response()->json(['success'=>$success,'message'=>$message,'error_message'=>$error_message]);
  }
}
