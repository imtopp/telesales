<?php

namespace App\Http\Controllers\Backend\Content\ManageCourier\GED;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesResources;
use Illuminate\Support\Facades\Auth;
use App\Models\CourierGedPriceCategory as PriceCategoryModel;
use DateTime;
use DB;
use URL;

class PriceCategoryController extends BaseController
{
  use AuthorizesRequests, AuthorizesResources, DispatchesJobs, ValidatesRequests;

  //Render Page
  public function index(){
    return view('backend/content/courier/ged/price_category');
  }

  //Read Price Category
  public function read(){
    $data = array();
    $price_categories = PriceCategoryModel::get();

    foreach($price_categories as $price_category){
      $data[str_replace(" ","_",strtolower($price_category->name))."_min_price"] = $price_category->min_price;
      $data[str_replace(" ","_",strtolower($price_category->name))."_max_price"] = $price_category->max_price;
    }

    return response()->json($data);
  }

  //Update Price Category
  public function update(){
    $date = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s')); //initialize date parameter
    $price_categories = PriceCategoryModel::get();

    foreach($price_categories as $price_category){
      $price_category->min_price = $_POST[str_replace(" ","_",strtolower($price_category->name))."_min_price"];
      $price_category->max_price = ($_POST[str_replace(" ","_",strtolower($price_category->name))."_max_price"]=="~"?"0":$_POST[str_replace(" ","_",strtolower($price_category->name))."_max_price"]);
      $price_category->update_date = $date->format('Y-m-d H:i:s');
      $price_category->update_by = Auth::User()->email;

      try {
        $success = $price_category->save();
        $message = 'Update data is success!';
      } catch (\Exception $ex) {
        $success = false;
        $message = $ex->getMessage();
        return response()->json(['success'=>$success,'message'=>$message]);
      }
    }

    return response()->json(['success'=>$success,'message'=>$message]);
  }
}
