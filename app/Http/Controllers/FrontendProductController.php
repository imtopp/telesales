<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesResources;
use App\Models\ProductFgCode as ProductFgCodeModel;
use App\Models\ProductColour as ProductColourModel;
use App\Models\Product as ProductModel;
use App\Models\ProductCategory as ProductCategoryModel;

class FrontendProductController extends BaseController
{
    use AuthorizesRequests, AuthorizesResources, DispatchesJobs, ValidatesRequests;

	private function getActiveProduct($fgcode = "not_set"){
		$all_data = array();
		
		if($fgcode == "not_set"){
			$fg_codes = ProductFgCodeModel::where(['status'=>'1'])->get();
		}else{
			$fg_codes = ProductFgCodeModel::where(['status'=>'1','fg_code'=>$fgcode])->get();
		}
	    	foreach($fg_codes as $fg_code){
	    		$colours = ProductColourModel::where(['status'=>'1','id'=>$fg_code->product_colour_id])->get();
		    	foreach($colours as $colour){
		    		$products = ProductModel::where(['status'=>'1','id'=>$colour->product_id])->get();
			    	foreach($products as $product){
			    		$categories = ProductCategoryModel::where(['status'=>'1','id'=>$product->category_id])->get();
				    	foreach($categories as $category){
				    		$all_data[] = array('category'=>$category,'product'=>$product,'colour'=>$colour,'fg_code'=>$fg_code);
				    	}
			    	}
		    	}
	    	}

		return $all_data;
	}
	
    public function showAllProduct(){
    	$all_category = array();
    	$all_data = $this->getActiveProduct();
		
    	//category list
    	foreach($all_data as $data){
    		$all_category[] = array('name'=>$data['category']->name);
    	}

    	$all_category = array_unique($all_category,SORT_REGULAR);

		//product list
    	foreach($all_data as $data){
    		$all_product[] = array('name'=>$data['product']->name." - ".$data['colour']->name,'category'=>$data['category']->name,'image_url'=>$data['fg_code']->image_url,'fg_code'=>$data['fg_code']->fg_code);
    	}

    	$all_product = array_unique($all_product,SORT_REGULAR);


    	return view('list_product',['all_category'=>$all_category,'all_product'=>$all_product]);
    }

    public function showProductDetail(){
    	if(isset($_GET['id']) && $_GET['id']!="")
    		$all_data = $this->getActiveProduct($_GET['id']);
    	else
    		$all_data = null;

    	//product list
    	if(isset($all_data) && count($all_data)!=0){
	    	foreach($all_data as $data){
	    		$product = array('name'=>$data['product']->name." - ".$data['colour']->name,'image_url'=>$data['fg_code']->image_url,'fg_code'=>$data['fg_code']->fg_code,'description'=>$data['product']->description,'price'=>$data['fg_code']->price);
	    	}
	    	$message = null;
	    }else{
	    	$product = null;
	    	$message = "Maaf produk yang anda cari tidak dapat ditemukan.";
	    }

    	return view('detail_product',['product'=>$product,'message'=>$message]);
    }
	
	public function showCustomerForm(){
		if(isset($_GET['id']) && $_GET['id']!="")
    		$all_data = $this->getActiveProduct($_GET['id']);
    	else
    		$all_data = null;

    	//product list
    	if(isset($all_data) && count($all_data)!=0){
	    	foreach($all_data as $data){
	    		$product = array('name'=>$data['product']->name." - ".$data['colour']->name,'image_url'=>$data['fg_code']->image_url,'fg_code'=>$data['fg_code']->fg_code,'price'=>$data['fg_code']->price);
	    	}
	    	$message = null;
	    }else{
	    	$product = null;
	    	$message = "Maaf produk yang anda cari tidak dapat ditemukan.";
	    }

    	return view('customer_form',['product'=>$product,'message'=>$message]);
	}
}
