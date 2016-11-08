<div class="menu_section">
  <h3>General</h3>
  <ul class="nav side-menu">
    <li id="home"><a href="{{URL::route('administrator_home')}}"><i class="fa fa-home"></i> Home </a></li>
  </ul>
</div>
<div class="menu_section">
  <h3>Management</h3>
  <ul class="nav side-menu">
    <li><a><i class="fa fa-edit"></i> Manage Product <span class="fa fa-chevron-down"></span></a>
      <ul class="nav child_menu">
        <li id="manage-product-category"><a href="{{URL::route('administrator_manage_product_category')}}">Category</a></li>
        <li id="manage-product-product"><a href="{{URL::route('administrator_manage_product_product')}}">Product</a></li>
        <li id="manage-product-colour"><a href="{{URL::route('administrator_manage_product_colour')}}">Colour</a></li>
        <li id="manage-product-fg-code"><a href="{{URL::route('administrator_manage_product_fg_code')}}">FG_CODE</a></li>
      </ul>
    </li>
    <li><a><i class="fa fa-map"></i> Manage Location <span class="fa fa-chevron-down"></span></a>
      <ul class="nav child_menu">
        <li id="manage-location-province"><a href="{{URL::route('administrator_manage_location_province')}}">Province</a></li>
        <li id="manage-location-city"><a href="{{URL::route('administrator_manage_location_city')}}">City</a></li>
        <li id="manage-location-district"><a href="{{URL::route('administrator_manage_location_district')}}">District</a></li>
      </ul>
    </li>
    <li><a><i class="fa fa-credit-card"></i> Manage Payment <span class="fa fa-chevron-down"></span></a>
      <ul class="nav child_menu">
        <li id="manage-payment-method"><a href="{{URL::route('administrator_manage_payment_method')}}">Payment Method</a></li>
        <li id="manage-payment-method-location-mapping"><a href="{{URL::route('administrator_manage_payment_method_location_mapping')}}">Payment Method Location Mapping</a></li>
      </ul>
    </li>
    <li><a><i class="fa fa-send"></i> Manage Courier <span class="fa fa-chevron-down"></span></a>
      <ul class="nav child_menu">
        <li id="manage-courier"><a href="{{URL::route('administrator_manage_courier')}}"> Courier </a></li>
        <li id="manage-courier-package"><a href="{{URL::route('administrator_manage_courier_package')}}"> Courier Package </a></li>
        <li id="manage-courier-delivery-price"><a href="{{URL::route('administrator_manage_courier_delivery_price')}}"> Courier Delivery Price </a></li>
      </ul>
    </li>
  </ul>
</div>
<div class="menu_section">
  <h3>Reporting</h3>
  <ul class="nav side-menu">
    <li id="customer-info"><a href="{{URL::route('administrator_customer_info')}}"><i class="fa fa-users"></i> Customer Information </a></li>
    <!--<li><a><i class="fa fa-calculator"></i> Product Hit Count </a></li>-->
    <li id="transaction"><a href="{{URL::route('administrator_transaction_list')}}"><i class="fa fa-credit-card"></i> Transaction </a></li>
  </ul>
</div>
