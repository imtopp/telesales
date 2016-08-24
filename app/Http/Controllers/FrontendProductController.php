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
use App\Models\CustomerInfo as CustomerInfoModel;
use App\Models\Transaction as TransactionModel;
use App\Models\PaymentType as PaymentTypeModel;
use File;
use DateTime;

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

    	if(isset($all_data) && count($all_data)!=0){
    		//product list
	    	foreach($all_data as $data){
	    		$product = array('name'=>$data['product']->name." - ".$data['colour']->name,'image_url'=>$data['fg_code']->image_url,'fg_code'=>$data['fg_code']->fg_code,'price'=>$data['fg_code']->price);
	    	}
	    	$payment_type = array();
	    	$payment_types = PaymentTypeModel::get();
	    	foreach($payment_types as $type){
	    		$payment_type[$type->id] = $type->name;
	    	}
	    	$message = File::get('assets/disclaimer/disclaimer.txt');
	    }else{
	    	$product = null;
	    	$message = "Maaf produk yang anda cari tidak dapat ditemukan.";
	    }

    	return view('customer_form',['product'=>$product,'message'=>$message,'payment_type'=>$payment_type]);
	}

	public function storeCustomerForm(){
		$customer_info = new CustomerInfoModel;
		$transaction = new TransactionModel;
		$date = DateTime::createFromFormat("Y-m-d H:i:s", date("Y-m-d H:i:s"));
		$product = $fg_codes = ProductFgCodeModel::where(['status'=>'1','fg_code'=>$_POST['fg_code']])->first();
		$key = "t3rs3r@h";

		$customer_info->name = $this->encrypt($key,$_POST['name']);
		$customer_info->address = $this->encrypt($key,$_POST['address']);
		$customer_info->identity_type = $this->encrypt($key,$_POST['identity_type']);
		$customer_info->identity_number = $this->encrypt($key,$_POST['identity_number']);
		$customer_info->email = $this->encrypt($key,$_POST['email']);
		$customer_info->mdn = $this->encrypt($key,$_POST['mdn']);
		$customer_info->delivery_address = $this->encrypt($key,$_POST['delivery_address']);
		$customer_info->input_date = $date->format("Y-m-d H:i:s");
		$customer_info->input_by = "System";
		$customer_info->input_date = $date->format("Y-m-d H:i:s");
		$customer_info->update_by = "System";

		$customer_info->save();

		$payment_type = PaymentTypeModel::where(['id'=>$_POST['payment_type']])->first();
		$transaction->customer_info_id = $customer_info->id;
		$transaction->product_fg_code_id = $product->id;
		$transaction->qty = $_POST['qty'];
		$transaction->payment_type_id = $payment_type->id;
		$transaction->input_date = $date->format("Y-m-d H:i:s");
		$transaction->input_by = "System";
		$transaction->input_date = $date->format("Y-m-d H:i:s");
		$transaction->update_by = "System";

		$transaction->save();

		return redirect($payment_type->redirect_url);
	}

	public function checkData(){
		$customer_info = CustomerInfoModel::get();
		$key = "t3rs3r@h";

		foreach($customer_info as $info){
			echo $this->decrypt($key,$info->name)." ";
			echo $this->decrypt($key,$info->address)." ";
			echo $this->decrypt($key,$info->identity_type)." ";
			echo $this->decrypt($key,$info->identity_number)." ";
			echo $this->decrypt($key,$info->email)." ";
			echo $this->decrypt($key,$info->mdn)." ";
			echo $this->decrypt($key,$info->delivery_address)." ";
			echo "<br/>";
		}
	}

	private function encrypt($key,$string){
		$iv = mcrypt_create_iv(
			mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC),
			MCRYPT_DEV_URANDOM
		);
		
		$encrypted = base64_encode(
			$iv .
			mcrypt_encrypt(
				MCRYPT_RIJNDAEL_128,
				hash('sha256',$key,true),
				$string,
				MCRYPT_MODE_CBC,
				$iv
			)
		);
		
		return $encrypted;
	}

	private function decrypt($key,$encrypted){
		$data = base64_decode($encrypted);
		$iv = substr($data, 0, mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128,MCRYPT_MODE_CBC));
		
		$decrypted = rtrim(
			mcrypt_decrypt(
				MCRYPT_RIJNDAEL_128,
				hash('sha256',$key,true),
				substr($data, mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC)),
				MCRYPT_MODE_CBC,
				$iv
			),
			"\0"
		);
		
		return $decrypted;
	}
}
