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
  }

}
