<?php

namespace App\Http\Controllers\Backend\Settings;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesResources;
use Illuminate\Contracts\Cache\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\Configuration as ConfigurationModel;
use DB;
use File;
use DateTime;

class ApplicationPropertiesController extends BaseController
{
  use AuthorizesRequests, AuthorizesResources, DispatchesJobs, ValidatesRequests;

  //Render Page
  public function index(){
    return view('backend/settings/application_properties');
  }

  //Update Application Properties
  public function update(Factory $cache){
    $date = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s')); //initialize date parameter
    $app_name = ConfigurationModel::where(["name"=>"app_name"])->first();

    $app_name->value = $_POST['app_name'];
    $app_name->update_date = $date->format('Y-m-d H:i:s');
    $app_name->update_by = Auth::User()->email;

    try {
      $success = $app_name->save();
      $message = "Setting has been saved successfully.";
    } catch (Exception $ex) {
      $success = false;
      $message = $ex->getMessage();
    }

    $cache->forget('settings');
    return response()->json(["success"=>$success,"message"=>$message]);
  }
}
