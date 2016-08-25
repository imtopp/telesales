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

  //local function that find all active product or specific fg_code
  private function getActiveProduct($find_product_id = null,$find_fg_code = null){
  	$all_data = array(); //initialize all active product array

    //trace active product model from category to fg_code
    $categories = ProductCategoryModel::where(['status'=>'1'])->get();
    foreach($categories as $category){ //looping active category
      if(!isset($find_product_id)){ //check if the request is not find on specific product_id
        $products = ProductModel::where(['status'=>'1','category_id'=>$category->id])->get();
        foreach($products as $product){ //looping active product
          $colours = ProductColourModel::where(['status'=>'1','product_id'=>$product->id])->get();
          foreach($colours as $colour){ //looping active colour
            //assumed there is only 1 fg_code active for 1 product colour and store it to all_data array.
            if(!isset($find_fg_code)){ //check if the request is not find on specific fg_code
              $fg_code = ProductFgCodeModel::where(['status'=>'1','product_colour_id'=>$colour->id])->first();
              if(isset($fg_code) && count($fg_code)!=0) //fg_code is available
                $all_data[] = array('category'=>$category,'product'=>$product,'colour'=>$colour,'fg_code'=>$fg_code);
            }else{ //finding specific fg_code
              $fg_code = ProductFgCodeModel::where(['status'=>'1','product_colour_id'=>$colour->id,'fg_code'=>$find_fg_code])->first();
              if(isset($fg_code) && count($fg_code)!=0){ //specific fg_code is found
                $all_data = array('category'=>$category,'product'=>$product,'colour'=>$colour,'fg_code'=>$fg_code);
                return $all_data; //stop the function and return the data after found;
              }
            }
          }
        }
      }else{
        $products = ProductModel::where(['status'=>'1','category_id'=>$category->id,'id'=>$find_product_id])->get();
        foreach($products as $product){ //looping active product
          $colours = ProductColourModel::where(['status'=>'1','product_id'=>$product->id])->get();
          foreach($colours as $colour){ //looping active colour
            //assumed there is only 1 fg_code active for 1 product colour and store it to all_data array.
            if(!isset($find_fg_code)){ //check if the request is not find on specific fg_code
              $fg_code = ProductFgCodeModel::where(['status'=>'1','product_colour_id'=>$colour->id])->first();
              if(isset($fg_code) && count($fg_code)!=0) //fg_code is available
                $all_data[] = array('category'=>$category,'product'=>$product,'colour'=>$colour,'fg_code'=>$fg_code);
            }else{ //finding specific fg_code
              $fg_code = ProductFgCodeModel::where(['status'=>'1','product_colour_id'=>$colour->id,'fg_code'=>$find_fg_code])->first();
              if(isset($fg_code) && count($fg_code)!=0){ //specific fg_code is found
                $all_data = array('category'=>$category,'product'=>$product,'colour'=>$colour,'fg_code'=>$fg_code);
                return $all_data; //stop the function and return the data after found;
              }
            }
          }
        }
      }
    }

    return $all_data; //return all data whether data is not found or request is collection of data
  }

  //public function for showing all product at frontpage
  public function showAllProduct(){
  	$all_category = array(); //initialize all category array
  	$all_data = $this->getActiveProduct(); //get all data array from getActiveProduct local function

    //creating all active product & category array by looping at all_data array
  	foreach($all_data as $data){
  		$all_category[] = array('name'=>$data['category']->name);
      $all_product[] = array('name'=>$data['product']->name,'category'=>$data['category']->name,'image_url'=>$data['product']->image_url,'id'=>$data['product']->id);
  	}
  	$all_category = array_unique($all_category,SORT_REGULAR); //create unique category
    $all_product = array_unique($all_product,SORT_REGULAR); //create unique product

  	foreach($all_data as $data){
  	}

  	return view('frontend/list_product',['all_category'=>$all_category,'all_product'=>$all_product]); //display list_product view with all_category and all_product
  }

  //public funtion for showing detail of product at frontpage
  public function showProductDetail(){
  	if(isset($_GET['id']) && $_GET['id']!="") //checking if there is fg_code at $_GET['id'] from URL
  		$all_data = $this->getActiveProduct($_GET['id']); //getting active product from the fg_code
  	else //when the $_GET['id'] is not set / there is no fg_code in URL
  		$all_data = null; //set all_data null

  	if(isset($all_data) && count($all_data)!=0){ //check if the all_data is set for value and it's count of value is not 0
      $colour = array();
      foreach($all_data as $data){
        $product = array('name'=>$data['product']->name,'image_url'=>$data['product']->image_url,'description'=>$data['product']->description);
        $colours[] = array('id'=>$data['colour']->id,'name'=>$data['colour']->name,'image_url'=>$data['colour']->image_url,'fg_code'=>$data['fg_code']->fg_code,'price'=>$data['fg_code']->price);
      }
      $message = null;
    }else{ //the fg_code is not found so it cant find the product or product not found
    	$product = null;
      $colours = null;
    	$message = "Maaf produk yang anda cari tidak dapat ditemukan.";
    }

  	return view('frontend/detail_product',['product'=>$product,'colours'=>$colours,'message'=>$message]); //display detail_product view with product property and the message
  }

  //public function for showing form to customer when they want buy product
	public function showCustomerForm(){
		if(isset($_GET['id']) && $_GET['id']!="") //checking if there is fg_code at $_GET['id'] from URL
    		$all_data = $this->getActiveProduct($_GET['id']); //getting active product from the fg_code
    	else //when the $_GET['id'] is not set / there is no fg_code in URL
    		$all_data = null; //set all_data null

    	if(isset($all_data) && count($all_data)!=0){ //check if the all_data is set for value and it's count of value is not 0
	    	$product = array('name'=>$all_data['product']->name." - ".$all_data['colour']->name,'image_url'=>$all_data['fg_code']->image_url,'fg_code'=>$all_data['fg_code']->fg_code,'price'=>$all_data['fg_code']->price);
	    	$payment_type = array(); //initialize array for payment_type
	    	$payment_types = PaymentTypeModel::get(); //getting available payment type
        foreach($payment_types as $type){ //creating array of payment_type
	    		$payment_type[$type->id] = $type->name;
	    	}
	    	$message = File::get('assets/disclaimer/disclaimer.txt'); //message for showing disclaimer
	    }else{ //the fg_code is not found so it cant find the product or product not found
	    	$product = null;
	    	$message = "Maaf produk yang anda cari tidak dapat ditemukan.";
	    }

    	return view('frontend/customer_form',['product'=>$product,'message'=>$message,'payment_type'=>$payment_type]); //display customer_form for buying product with the product,payment_type and the message
	}

  //public function for storing customer form input when they want buy product
	public function storeCustomerForm(){
		$customer_info = new CustomerInfoModel; //creating model for customer_info
		$transaction = new TransactionModel; //creating model for transaction
		$date = DateTime::createFromFormat("Y-m-d H:i:s", date("Y-m-d H:i:s")); //initialize date parameter
    $fg_codes = ProductFgCodeModel::where(['status'=>'1','fg_code'=>$_POST['fg_code']])->first(); //find fg_code from the model
		$key = "t3rs3r@h"; //key for encryption
    if(isset($_POST) && count($_POST)!=0){
      $payment_type = PaymentTypeModel::where(['id'=>$_POST['payment_type']])->first(); //find payment_type model

      //fill customer_info model
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

  		$customer_info->save(); //save the customer_info model to database

      //fill transaction model
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
