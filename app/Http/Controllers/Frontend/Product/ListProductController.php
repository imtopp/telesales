<?php

namespace App\Http\Controllers\Frontend\Product;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesResources;
use App\Models\ViewActiveProduct as ViewActiveProductModel;
use File;
use DateTime;
use Mail;

class ListProductController extends BaseController
{
  use AuthorizesRequests, AuthorizesResources, DispatchesJobs, ValidatesRequests;

  //public function for showing all product at frontpage
  public function index(){
    $data = array();
    $active_category = ViewActiveProductModel::pluck('category');
    foreach ($active_category as $category){
      $data[$category] = array();
    }
    $active_product = ViewActiveProductModel::select('category','product_id','product','product_image_url','price')->groupBy('category','product_id','product','product_image_url','price')->get();
    foreach ($active_product as $product){
      $data[$product->category][] = array('id'=>$product->product_id,"name"=>$product->product,"image_url"=>$product->product_image_url,'price'=>$product->price);
    }

    return view('frontend/product/list_product',['data'=>$data]); //display list_product view with all_category and all_product
  }
}
