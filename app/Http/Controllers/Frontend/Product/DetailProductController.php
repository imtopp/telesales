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

      //increase product hit count
      $product_data = ProductModel::where(['status'=>'active','id'=>$product_id])->first();
      if(isset($product_data)){
        $product_data->hit_count++;
        try{
          $product_data->save();
        }catch (Exception $ex){
          $message = $ex->getMessage();
        }

        $active_products = ViewActiveProductModel::where(['product_id'=>$product_id])->groupBy('fg_code')->get();

        $product_detail['id'] = $active_products->first()->product_id;
        $product_detail['name'] = $active_products->first()->product;
        $product_detail['description'] = $active_products->first()->product_description;

        foreach($active_products as $product){
          $product_detail['colours'][] = array('id'=>$product->colour_id,'name'=>$product->colour,'fg_code'=>$product->fg_code,'image_url'=>$product->colour_image_url,'price'=>$product->price);
        }
      }else{
        $message = "Maaf produk yang anda cari tidak dapat ditemukan.";
      }
    }else{ //the fg_code is not found so it cant find the product or product not found
      $message = "Maaf produk yang anda cari tidak dapat ditemukan.";
    }

    return view('frontend/product/detail_product',['product'=>isset($product_detail)?$product_detail:null,'message'=>isset($message)?$message:null]);
  }

}
