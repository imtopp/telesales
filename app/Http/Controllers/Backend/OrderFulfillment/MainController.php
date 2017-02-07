<?php

namespace App\Http\Controllers\Backend\DigitalIOT;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesResources;
use File;
use DateTime;

class MainController extends BaseController
{
  use AuthorizesRequests, AuthorizesResources, DispatchesJobs, ValidatesRequests;

  //public function for showing all product at frontpage
  public function home(){
    return view('backend/digitaliot/content/home'); //display list_product view with all_category and all_product
  }

  public function digitalIOT(){
    redirect(URL::route('digitaliot_home'));
  }

}