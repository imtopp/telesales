<?php

namespace App\Http\Controllers\Backend\Telesales;

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
    return view('backend/telesales/content/home'); //display list_product view with all_category and all_product
  }

  public function telesales(){
    redirect(URL::route('telesales_home'));
  }

}
