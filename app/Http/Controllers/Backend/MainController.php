<?php

namespace App\Http\Controllers\Backend;

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
use App\Models\CustomerLocationProvince as CustomerLocationProvinceModel;
use App\Models\CustomerLocationCity as CustomerLocationCityModel;
use App\Models\CustomerLocationDistrict as CustomerLocationDistrictModel;
use App\Models\PaymentMethodLocationMapping as PaymentMethodLocationMappingModel;
use App\Models\PaymentMethod as PaymentMethodModel;
use App\Models\TotalPriceCategory as TotalPriceCategoryModel;
use App\Models\DeliveryPrice as DeliveryPriceModel;
use File;
use DateTime;

class MainController extends BaseController
{
  use AuthorizesRequests, AuthorizesResources, DispatchesJobs, ValidatesRequests;

  //public function for showing all product at frontpage
  public function home(){
    return view('backend/content/home'); //display list_product view with all_category and all_product
  }
}
