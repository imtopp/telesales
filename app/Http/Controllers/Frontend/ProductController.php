





  public function checkData(){
    $transactions = TransactionModel::get();
    foreach($transactions as $transaction){
      $customer_info = CustomerInfoModel::where(["id"=>$transaction->customer_info_id])->get();
      $key = "t3rs3r@h";
      foreach($customer_info as $info){
        $item = ProductFgCodeModel::where(["id"=>$transaction->product_fg_code_id])->first();
        $colour = ProductColourModel::where(['id'=>$item->product_colour_id])->first();
        $product = ProductModel::where(['id'=>$colour->product_id])->first();
        $district = LocationDistrictModel::where(['id'=>$info->location_district_id])->first();
        $city = LocationCityModel::where(['id'=>isset($district->city_id)?$district->city_id:null])->first();
        $province = LocationProvinceModel::where(['id'=>isset($city->province_id)?$city->province_id:null])->first();
        $payment_method = PaymentMethodModel::where(['id'=>$transaction->payment_method_id])->first();
        $mapping = PaymentMethodLocationMappingModel::where(['payment_method_id'=>isset($payment_method->id)?$payment_method->id:null,'location_district_id'=>isset($district->id)?$district->id:null])->first();
        $total_price_category = TotalPriceCategoryModel::where('min_price','<=',$item->price*$transaction->qty)->where(function($query)use($item,$transaction){return $query->where('max_price','>=',$item->price*$transaction->qty)->orWhere('max_price','=','0');})->first();
        $delivery_price = DeliveryPriceModel::where(['payment_method_location_mapping_id'=>isset($mapping->id)?$mapping->id:null,'total_price_category_id'=>$total_price_category->id])->first();
        echo "Customer Name : ".$this->decrypt($key,$info->name)."<br/>";
        echo "Address : ".$this->decrypt($key,$info->address)."<br/>";
        echo "Identity Type : ".$this->decrypt($key,$info->identity_type)."<br/>";
        echo "Identity Number : ".$this->decrypt($key,$info->identity_number)."<br/>";
        echo "Email : ".$this->decrypt($key,$info->email)."<br/>";
        echo "MDN : ".$this->decrypt($key,$info->mdn)."<br/>";
        echo "Province : ".(isset($province->name)?$province->name:null)."<br/>";
        echo "City : ".(isset($city->name)?$city->name:null)."<br/>";
        echo "District : ".(isset($district->name)?$district->name:null)."<br/>";
        echo "Delivery Address : ".$this->decrypt($key,$info->delivery_address)."<br/>";
        echo "Product : ".$product->name." ".$colour->name."<br/>";
        echo "Product Price : Rp ".$item->price."<br/>";
        echo "Total QTY : ".$transaction->qty." unit<br/>";
        echo "Payment Method : ".(isset($payment_method->name)?$payment_method->name:null)."<br/>";
        echo "Delivery Price : ".(isset($delivery_price->price)?$delivery_price->price:null)."<br/>";
        echo "Total Price : Rp ".(($item->price*$transaction->qty)+(isset($delivery_price->price)?$delivery_price->price:0))."<br/>";
        echo "<br/>";
        echo "<br/>";
      }
    }
  }

  
}
