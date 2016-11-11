<?php

namespace App\Http\Controllers\Backend\Administrator\Settings;

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
    return view('backend/administrator/settings/application_properties');
  }

  //Update Application Properties
  public function update(Factory $cache){
    $date = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s')); //initialize date parameter
    $success = true;
    $message = "Setting has been saved successfully.";
    DB::beginTransaction();

    foreach($_REQUEST as $key=>$value){
      if($key != "_token" && $key != "undefined"){
        $model = ConfigurationModel::where(["name"=>$key])->first();

        if($model->value != $value){
          $model->value = $value;
          $model->update_date = $date->format('Y-m-d H:i:s');
          $model->update_by = Auth::User()->email;

          try {
            $success = $model->save();
          } catch (Exception $ex) {
            DB::rollback();
            $success = false;
            $message = $ex->getMessage();
          }
        }
      }
    }

    if($success){
      DB::commit();
    }

    $cache->forget('settings');
    return response()->json(["success"=>$success,"message"=>$message]);
  }
}
