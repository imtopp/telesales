<?php

namespace App\Http\Controllers\Frontend\Product;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesResources;
use App\Models\Product as ProductModel;
use App\Models\ViewActiveProduct as ViewActiveProductModel;
use File;
use DateTime;
use Mail;

class DetailProductController extends BaseController
{
  use AuthorizesRequests, AuthorizesResources, DispatchesJobs, ValidatesRequests;

  //public funtion for showing detail of product at frontpage
  public function index(){
    /*if(isset($_GET['id']) && $_GET['id']!=""){ //checking if there is fg_code at $_GET['id'] from URL
      $all_data = $this->getActiveProduct($_GET['id']); //getting active product from the fg_code
    }else{ //when the $_GET['id'] is not set / there is no fg_code in URL
      $all_data = null; //set all_data null
    }
    if(isset($all_data) && count($all_data)!=0){ //check if the all_data is set for value and it's count of value is not 0
      $colours = array();
      $colours_dropdown = array();
      $product_data = ProductModel::where(['status'=>'active','id'=>$_GET['id']])->first();
      $product_data->hit_count = ++$product_data->hit_count;
      $product_data->save();
      foreach($all_data as $data){
        $product = array('name'=>$data['product']->name,'image_url'=>$data['product']->image_url,'description'=>$data['product']->description);
        $colours[] = array('id'=>$data['colour']->id,'name'=>$data['colour']->name,'image_url'=>$data['colour']->image_url,'fg_code'=>$data['fg_code']->fg_code,'price'=>$data['fg_code']->price);
        $colours_dropdown[$data['colour']->id] = $data['colour']->name;
      }
      $message = null;
    }else{ //the fg_code is not found so it cant find the product or product not found
      $message = "Maaf produk yang anda cari tidak dapat ditemukan.";
    }*/

    if(isset($_GET['id']) && $_GET['id']!=""){
      $product_id = $_GET['id'];
      $product = array();

      //increase product hit count
      $product_data = ProductModel::where(['status'=>'active','id'=>$product_id])->first();
      if(isset($product_data)){
        $product_data->hit_count++;
        $product_data->save();
        $product['id'] = $product_data->id;
        $product['name'] = $product_data->name;
        $product['description'] = $product_data->description;
      }

      $product['colours'] = array();
      $product_detail = ViewActiveProductModel::select('colour_id','colour','colour_image_url','fg_code','price')->where(['product_id'=>$product_id])->groupBy('colour_id','colour','colour_image_url','fg_code','price')->get();
      foreach($product_detail as $detail){
        $product['colours'][] = array('id'=>$detail->colour_id,'name'=>$detail->colour,'fg_code'=>$detail->fg_code,'image_url'=>$detail->colour_image_url,'price'=>$detail->price);
      }
    }else{ //the fg_code is not found so it cant find the product or product not found
      $message = "Maaf produk yang anda cari tidak dapat ditemukan.";
    }

    return view('frontend/product/detail_product',['product'=>isset($product)?$product:null,'message'=>isset($message)?$message:null]);

    //return view('frontend/product/detail_product',['product'=>isset($product)?$product:null,'colours'=>isset($colours)?$colours:null,'colours_dropdown'=>isset($colours_dropdown)?$colours_dropdown:null,'message'=>$message]); //display detail_product view with product property and the message
  }

}
