@extends('backend\layout\template')

@section('title', 'Manage Order')

@section('sidebar-menu')
  @include('backend\telesales\layout\sidebar_menu_content')
@endsection

@section('sidebar-footer')
  @include('backend\telesales\layout\sidebar_footer')
@endsection

@section('page-css-file')
<!-- datatables bootstrap -->
<link href="{{ URL::asset('assets/vendors/datatables.net-bs/css/dataTables.bootstrap.min.css') }}" rel="stylesheet">
<!-- datatables bootstrap buttons -->
<link href="{{ URL::asset('assets/vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="row">
  <div class="col-md-12 col-sm-12 col-xs-12">
    <div class="x_panel">
      <div class="x_title">
        <h2>Manage Order Page</h2>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <h4>Welcome to Manage Order Page</h4>
        <span>
          Selamat datang di halaman manage order. halaman ini digunakan untuk memasukan pesanan customer pada aplikasi {{ config('settings.app_name') }}
          anda dapat mengatur pesanan pada tabel dibawah.
        </span>
        <table id="datatable" class="table table-striped table-bordered">
          <thead>
            <tr>
              <th>Refference Number</th>
              <th>Customer Name</th>
              <th>Customer Address</th>
              <th>Customer Identity Type</th>
              <th>Customer Identity Number</th>
              <th>Customer Email</th>
              <th>Customer MDN</th>
              <th>Customer Location Province</th>
              <th>Customer Location City</th>
              <th>Customer Location Distict</th>
              <th>Customer Delivery Address</th>
              <th>Product Category</th>
              <th>Product Name</th>
              <th>Product Colour</th>
              <th>Product FG Code</th>
              <th>Product Price</th>
              <th>Payment Method</th>
              <th>Courier</th>
              <th>Courier Package</th>
              <th>Delivery Price</th>
              <th>Total Price</th>
              <th>Status</th>
              <th class="text-center"> Action </th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="modal_view" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content" id="modal-content">
    </div>
  </div>
</div>
@endsection

@section('page-js-file')
<!-- datatables jquery -->
<script src="{{ URL::asset('assets/vendors/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<!-- datatables plugin fnReloadAjax -->
<script src="{{ URL::asset('assets/vendors/datatables.net/plugin/fnReloadAjax.js') }}"></script>
<!-- datatables boostrap -->
<script src="{{ URL::asset('assets/vendors/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
<!-- datatables buttons -->
<script src="{{ URL::asset('assets/vendors/datatables.net-buttons/js/dataTables.buttons.min.js') }}"></script>
<!-- datatables boostrap buttons -->
<script src="{{ URL::asset('assets/vendors/datatables.net-buttons-bs/js/buttons.bootstrap.min.js') }}"></script>
<!-- bootsrap-select -->
<script src="{{ URL::asset('assets/vendors/bootstrapselect/js/bootstrap-select.js') }}"></script>
<!-- spinner -->
<script src="{{ URL::asset('assets/js/spin.min.js') }}"></script>
@endsection

@section('page-js-script')
  <script type = "text/template" id="modal-template">
    {!!Form::open(["id"=>"popup_form","class"=>"form form-horizontal"])!!}
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal">&times;</button>
      <h4 id="title" class="modal-title"></h4>
    </div>
    <div class="modal-body">
      <div id="message">
        <div class="form-group">
          <div class="controls">
            {!! Form::label("category_id", "Category") !!}
            {!! Form::select('category_id', [''=>'Please Choose Category'], '', ["class"=>"form-control","required","id"=>"category_id"]) !!}
          </div>
        </div>
        <div class="form-group product" style="display:none">
          <div class="controls">
            {!! Form::label("product_id", "Product") !!}
            {!! Form::select('product_id', [''=>'Please Choose Product'], '', ["class"=>"form-control","required","id"=>"product_id"]) !!}
            <!--{!! Form::select('product_id', [''=>'Please Choose Product'], '', ["data-size"=>"5","data-width"=>"100%","class"=>"selectpicker","required","id"=>"product_id"]) !!}-->
          </div>
        </div>
        <div id="product_preview" class="row" style="display:none">
          <div class="col-md-6">
            <div style="margin-bottom: 10px; margin-top: 10px;">
              <div class="col-md-1">
                &nbsp;
              </div>
              <div class="col-md-10" style="text-align: center;">
                <img id="product_img_preview" style="max-width:100%; max-height:360pt" onerror="this.onerror=null;this.src='{{URL::asset('assets/img/img_not_found.jpg')}}';"/>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <h4>Detail Product</h4>
            <hr/>
            <div class="detail-product" style="text-align:left;">
              <section id="product_description"></section>
            </div>
          </div>
        </div>
        <div class="form-group colour" style="display:none">
          <div class="controls">
            {!! Form::label("colour_id", "Colour") !!}
            {!! Form::select('colour_id', [''=>'Please Choose Colour'], '', ["class"=>"form-control","required","id"=>"colour_id"]) !!}
          </div>
        </div>
        <div id="colour_preview" class="row" style="display:none">
          <div class="col-md-1">
            &nbsp;
          </div>
          <div class="col-md-10" style="text-align: center;">
            <img id="colour_img_preview" style="max-width:100%; max-height:360pt" onerror="this.onerror=null;this.src='{{URL::asset('assets/img/img_not_found.jpg')}}';"/>
          </div>
        </div>
        <div class="form-group">
          {!! Form::hidden('fg_code', null,array('id'=>'fg_code')) !!}
        </div>
        <div id="customer_form" style="display: none;">
          <div class="form-group">
            {!! Form::label('Identity Name') !!}
            {!! Form::text('name',null,array('required','class'=>'form-control','placeholder'=>'Customer identity Name')) !!}
          </div>
          <div class='form-group'>
            {!! Form::label('Identity Address') !!}
            {!! Form::text('address',null,array('required','class'=>'form-control','placeholder'=>'Customer identity address')) !!}
          </div>
          <div class='form-group'>
            {!! Form::label('Identity Type') !!}
            {!! Form::select('identity_type',array('KTP'=>'KTP','SIM'=>'SIM','KITAS'=>'KITAS','PASPOR'=>'PASPOR'),null,array('required','class'=>'form-control','placeholder'=>'Customer identity type')) !!}
          </div>
          <div class='form-group'>
            {!! Form::label('Identity Number') !!}
            {!! Form::text('identity_number',null,array('required','class'=>'form-control','placeholder'=>'Customer identity number')) !!}
          </div>
          <div class='form-group'>
            {!! Form::label('Email Address') !!}
            {!! Form::input('email','email',null,array('required','class'=>'form-control','placeholder'=>'Customer Email')) !!}
          </div>
          <div class='form-group'>
            {!! Form::label('Contact Number') !!}
            {!! Form::text('mdn',null,array('required','class'=>'form-control','placeholder'=>'Customer contact number in 088xxxxxxxxx format','onkeypress'=>'return isNumberKey(event);')) !!}
          </div>
          <div class='form-group'>
            {!! Form::label('Delivery Province') !!}
            {!! Form::select('province',[''=>'Please Choose Customer Delivery Province'],null,array('required','id'=>'province','class'=>'form-control')) !!}
          </div>
          <div class='form-group city' style="display:none">
            {!! Form::label('Delivery City') !!}
            {!! Form::select('city',[''=>'Please Choose Customer Delivery City'],null,array('required','id'=>'city','class'=>'form-control','placeholder'=>'Customer delivery city')) !!}
          </div>
          <div class='form-group district' style="display:none">
            {!! Form::label('Delivery District') !!}
            {!! Form::select('district_id',[''=>'Please Choose Customer Delivery District'],null,array('required','id'=>'district_id','class'=>'form-control')) !!}
          </div>
          <div class='form-group address' style="display:none">
            {!! Form::label('Delivery Address') !!}
            {!! Form::textarea('delivery_address',null,array('required','class'=>'form-control','placeholder'=>'Customer delivery address')) !!}
          </div>
          <div class='form-group payment_method' style="display:none">
            {!! Form::label('Payment Method') !!}
            {!! Form::select('payment_method_id',[''=>'Please Choose Payment Method'],null,array('required','id'=>'payment_method_id','class'=>'form-control')) !!}
          </div>
          <div class='form-group courier' style="display:none">
            {!! Form::label('Kurir') !!}
            {!! Form::select('courier_id',[''=>'Please Choose Courier'],null,array('required','id'=>'courier_id','class'=>'form-control')) !!}
          </div>
          <div class='form-group courier_package' style="display:none">
            {!! Form::label('Paket Pengiriman') !!}
            {!! Form::select('courier_package_id',[''=>'Please Choose Delivery Package'],null,array('required','id'=>'courier_package_id','class'=>'form-control')) !!}
          </div>
          <div class='form-group' style="text-align:left">
            <h4>Harga Barang : <span id="price"></span></h4>
            <h4 class="delivery_price" style="display:none;">Ongkos Kirim : <span id="delivery_price">Rp 0</span></h4>
            <h4>Subtotal : <span id="subtotal"></span></h4>
          </div>
        </div>
      </div>
    </div>
    <div id="footer" class="modal-footer">
      <div class="form-group">
        <div class="controls">
          {!!Form::submit("Save",["class"=>"btn btn-primary submit"])!!}
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
    {!!Form::close()!!}
  </script>

  <script type = "text/template" id="modal-template-cancel-order">
    {!!Form::open(["id"=>"popup_form","class"=>"form form-horizontal"])!!}
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal">&times;</button>
      <h4 id="title" class="modal-title"></h4>
    </div>
    <div class="modal-body">
      <div id="message">
        <div class="form-group">
          {!! Form::hidden('id',null,['id'=>'id']) !!}
        </div>
        <h5>Apakah anda yakin membatalkan pesanan <span id="refference_number"></span> ?</h5>
      </div>
    </div>
    <div id="footer" class="modal-footer">
      <div class="form-group">
        <div class="controls">
          {!!Form::submit("Ya",["class"=>"btn btn-primary submit"])!!}
          <button type="button" class="btn btn-default" data-dismiss="modal">Tidak</button>
        </div>
      </div>
    </div>
    {!!Form::close()!!}
  </script>

  <script>
  var table;
    $(document).ready(function() {
      (function($) {
        $.extend({
          spin: function(spin, opts) {
            if (opts === undefined) {
              opts = {
                lines: 13, // The number of lines to draw
                length: 20, // The length of each line
                width: 10, // The line thickness
                radius: 30, // The radius of the inner circle
                corners: 1, // Corner roundness (0..1)
                rotate: 0, // The rotation offset
                direction: 1, // 1: clockwise, -1: counterclockwise
                color: '#000', // #rgb or #rrggbb or array of colors
                speed: 1, // Rounds per second
                trail: 56, // Afterglow percentage
                shadow: false, // Whether to render a shadow
                hwaccel: false, // Whether to use hardware acceleration
                className: 'spinner', // The CSS class to assign to the spinner
                zIndex: 2e9, // The z-index (defaults to 2000000000)
                top: '50%', // Top position relative to parent
                left: '50%' // Left position relative to parent
              };
            }

            var data = $('body').data();

            if (data.spinner) {
              data.spinner.stop();
              delete data.spinner;
              $("#spinner_modal").remove();
              return this;
            }

            if (spin=="show") {
              var spinElem = this;

              $('body').append('<div id="spinner_modal" style="background-color: rgba(0, 0, 0, 0.3); width:100%; height:100%; position:fixed; top:0px; left:0px; z-index:' + (opts.zIndex - 1) + '"/>');
              spinElem = $("#spinner_modal")[0];

              data.spinner = new Spinner($.extend({
                color: $('body').css('color')
              }, opts)).spin(spinElem);
            }
          }
        });
      })(jQuery);

      table = $('#datatable').dataTable({
        dom: 'Bfrtipl',
        "processing": true,
        "serverSide": true,
        buttons: [{
          text: 'Tambah Baru',
          action: create,
        }],
        ajax: {
          data: {"_token":"{{ csrf_token() }}"},
          url: "{{URL::route('telesales_manage_order_read')}}",
          type: "POST",
          error: function(){  // error handling
            $(".lookup-error").html("");
            $("#lookup").append('<tbody class="employee-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
            $("#lookup_processing").css("display","none");
          }
        },
        pageLength : 10,
        "scrollX": true,
        "columns": [{
          "data": "refference_number",
          "title": "Refference Number"
        },{
          "data": "customer_name",
          "title": "Customer Name",
          "width": "160px"
        },{
          "data": "customer_address",
          "title": "Customer Address",
          "width": "160px"
        },{
          "data": "customer_identity_type",
          "title": "Customer Identity Type"
        },{
          "data": "customer_identity_number",
          "title": "Customer Identity Number",
          "width": "100px"
        },{
          "data": "customer_email",
          "title": "Customer Email"
        },{
          "data": "customer_mdn",
          "title": "Customer MDN"
        },{
          "data": "customer_location_province",
          "title": "Customer Location Province"
        },{
          "data": "customer_location_city",
          "title": "Customer Location City"
        },{
          "data": "customer_location_district",
          "title": "Customer Location District"
        },{
          "data": "customer_delivery_address",
          "title": "Customer Delivery Address",
          "width": "160px"
        },{
          "data": "product_category",
          "title": "Product Category"
        },{
          "data": "product_name",
          "title": "Product Name",
          "width": "100px"
        },{
          "data": "product_colour",
          "title": "Product Colour"
        },{
          "data": "product_fg_code",
          "title": "Product FG Code"
        },{
          "data": "product_price",
          "title": "Product Price",
          "width": "100px"
        },{
          "data": "payment_method",
          "title": "Payment Method"
        },{
          "data": "courier",
          "title": "Courier"
        },{
          "data": "courier_package",
          "title": "Courier Package"
        },{
          "data": "delivery_price",
          "title": "Delivery Price",
          "width": "100px"
        },{
          "data": "total_price",
          "title": "Total Price",
          "width": "100px"
        },{
          "data": "status",
          "title": "Status",
          "width": "100px"
        },{
          "data": "action",
          "title": "Action",
          "width": "100px"
        }],
        deferRender: true,
     });
    });

    function isNumberKey(event){
      var charCode = (event.which) ? event.which : event.keyCode;
      if (charCode != 43 && charCode > 31 && (charCode < 48 || charCode > 57))
        return false;
      return true;
    }

    function initializeModal(element,title,id,refference_number){
      $("#modal-content").html(element);
      $("#title").html(title);
      if(typeof id != 'undefined'){
        $("#id").val(id);
      }
      if(typeof refference_number != 'undefined'){
        $("#refference_number").html(refference_number);
      }else{
        var data = {"_token":"{{ csrf_token() }}"},price=0,delivery_price=0,subtotal=0;

        Number.prototype.formatMoney = function(c, d, t){
          var n = this,
          c = isNaN(c = Math.abs(c)) ? 2 : c,
          d = d == undefined ? "." : d,
          t = t == undefined ? "," : t,
          s = n < 0 ? "-" : "",
          i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "",
          j = (j = i.length) > 3 ? j % 3 : 0;
          return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
        };

        if($("#category_id").is("select")){
          $.ajax({
            url : '{{URL::route('telesales_manage_order_get_category')}}',
            type: 'POST',
            dataType: 'JSON',
            data: data,
            success : function(data){
              $.each(data,function(key,value){
                $("#category_id").append('<option value="'+key+'">'+value+'</option>');
              });
            }
          });
        }

        $("#category_id").change(function(){
          if($("#category_id").val()!=""){
            data.category_id = $("#category_id").val();

            $.ajax({
              url : '{{URL::route('telesales_manage_order_get_product')}}',
              type: 'POST',
              dataType: 'JSON',
              data: data,
              success : function(data){
                $("#product_id").empty();
                $("#product_id").append('<option value="">Please Choose Product</option>');

                $.each(data,function(){
                  $("#product_id").append('<option value="'+this.product_id+'" data-content="'+"<img src='"+this.product_image_url+"' style='max-width:80px' > "+this.product+'">'+this.product+'</option>');
                });

                //$("#product_id").selectpicker("refresh");

                $(".product").show();
                $("#product_preview").hide();
                $(".colour").hide();
                $("#colour_preview").hide();
                $("#customer_form").hide();
              }
            });
          }else{
            $(".product").hide();
            $("#product_preview").hide();
            $(".colour").hide();
            $("#colour_preview").hide();
            $("#customer_form").hide();
          }
        });

        $("#product_id").change(function(){
          if($("#product_id").val()!=""){
            data.product_id = $("#product_id").val();

            $.ajax({
              url : '{{URL::route('telesales_manage_order_get_product_detail')}}',
              type: 'POST',
              dataType: 'JSON',
              data: data,
              success : function(data){
                $("#product_img_preview").attr('src',data.image_url);
                $("#product_description").html(data.description);

                $("#product_preview").show();
                $("#colour_preview").hide();
                $("#customer_form").hide();
              }
            });

            $.ajax({
              url : '{{URL::route('telesales_manage_order_get_colour')}}',
              type: 'POST',
              dataType: 'JSON',
              data: data,
              success : function(data){
                $("#colour_id").empty();
                $("#colour_id").append('<option value="">Please Choose Colour</option>');

                $.each(data,function(key,value){
                  $("#colour_id").append('<option value="'+key+'">'+value+'</option>');
                });

                $(".colour").show();
              }
            });
          }else{
            $("#product_preview").hide();
            $(".colour").hide();
            $("#colour_preview").hide();
            $("#customer_form").hide();
          }
        });

        $("#colour_id").change(function(){
          if($("#colour_id").val()!=""){
            data.colour_id = $("#colour_id").val();

            $.ajax({
              url : '{{URL::route('telesales_manage_order_get_colour_image')}}',
              type: 'POST',
              dataType: 'JSON',
              data: data,
              success : function(data){
                $("#colour_img_preview").attr('src',data.image_url);

                $("#colour_preview").show();
              }
            });

            $.ajax({
              url : '{{URL::route('telesales_manage_order_get_fg_code')}}',
              type: 'POST',
              dataType: 'JSON',
              data: data,
              success : function(data){
                $("#fg_code").val("");
                $("#fg_code").val(data.fg_code);

                price = data.price;
                subtotal = price;
                $("#price").html("Rp "+price.formatMoney(0, ',', '.'));
                $("#subtotal").html("Rp "+subtotal.formatMoney(0, ',', '.'));

                $(function(){
                  $.ajax({
                    url : '{{URL::route('telesales_manage_order_get_province')}}',
                    type: 'POST',
                    dataType: 'JSON',
                    data: {"_token":"{{ csrf_token() }}"},
                    success : function(data){
                      $.each(data,function(key,value){
                        $("#province").append('<option value="'+key+'">'+value+'</option>');
                      });
                    }
                  });
                });

                $("#customer_form").show();
              }
            });
          }else{
            $("#colour_preview").hide();
            $("#customer_form").hide();
          }
        });

        $("#province").change(function(){
          if($("#province").val()!=""){
            $.ajax({
              url : '{{URL::route('telesales_manage_order_get_city')}}',
              type: 'POST',
              dataType: 'JSON',
              data: {"_token":"{{ csrf_token() }}","province_id":$("#province").val()},
              success : function(data){
                $("#city").empty();
                $("#city").append('<option value="">Silahkan Pilih Kota</option>');
                $.each(data,function(key,value){
                  $("#city").append('<option value="'+key+'">'+value+'</option>');
                });
                $(".city").show();
                $(".district").hide();
                $(".address").hide();
                $(".payment_method").hide();
                $(".courier").hide();
                $(".courier_package").hide();
                $(".delivery_price").hide();
                $("#district_id").empty();
                $("#district_id").append('<option value="">Silahkan Pilih Kecamatan</option>');
                $("#payment_method_id").empty();
                $("#payment_method_id").append('<option value="">Silahkan Pilih Metode Pembayaran</option>');
                $("#courier_id").empty();
                $("#courier_id").append('<option value="">Silahkan Pilih Kurir</option>');
                $("#courier_package_id").empty();
                $("#courier_package_id").append('<option value="">Silahkan Pilih Paket Pengiriman</option>');
                $("#delivery_price").html("Rp "+delivery_price.formatMoney(0, ',', '.'));
                $("#subtotal").html("Rp "+price.formatMoney(0, ',', '.'));
              }
            });
          }else{
            $(".city").hide();
            $(".district").hide();
            $(".address").hide();
            $(".payment_method").hide();
            $(".courier").hide();
            $(".courier_package").hide();
            $(".delivery_price").hide();
            $("#city").empty();
            $("#city").append('<option value="">Silahkan Pilih Kota</option>');
            $("#district_id").empty();
            $("#district_id").append('<option value="">Silahkan Pilih Kecamatan</option>');
            $("#payment_method_id").empty();
            $("#payment_method_id").append('<option value="">Silahkan Pilih Metode Pembayaran</option>');
            $("#courier_id").empty();
            $("#courier_id").append('<option value="">Silahkan Pilih Kurir</option>');
            $("#courier_package_id").empty();
            $("#courier_package_id").append('<option value="">Silahkan Pilih Paket Pengiriman</option>');
            $("#delivery_price").html("Rp "+delivery_price.formatMoney(0, ',', '.'));
            $("#subtotal").html("Rp "+price.formatMoney(0, ',', '.'));
          }
        });

        $("#city").change(function(){
          if($("#city").val()!=""){
            $.ajax({
              url : '{{URL::route('telesales_manage_order_get_district')}}',
              type: 'POST',
              dataType: 'JSON',
              data: {"_token":"{{ csrf_token() }}","city_id":$("#city").val()},
              success : function(data){
                $("#district_id").empty();
                $("#district_id").append('<option value="">Silahkan Pilih Kecamatan</option>');
                $.each(data,function(key,value){
                  $("#district_id").append('<option value="'+key+'">'+value+'</option>');
                });
                $(".district").show();
                $(".address").hide();
                $(".payment_method").hide();
                $(".courier").hide();
                $(".courier_package").hide();
                $(".delivery_price").hide();
                $("#payment_method_id").empty();
                $("#payment_method_id").append('<option value="">Silahkan Pilih Metode Pembayaran</option>');
                $("#courier_id").empty();
                $("#courier_id").append('<option value="">Silahkan Pilih Kurir</option>');
                $("#courier_package_id").empty();
                $("#courier_package_id").append('<option value="">Silahkan Pilih Paket Pengiriman</option>');
                $("#delivery_price").html("Rp "+delivery_price.formatMoney(0, ',', '.'));
                $("#subtotal").html("Rp "+price.formatMoney(0, ',', '.'));
              }
            });
          }else{
            $(".district").hide();
            $(".address").hide();
            $(".payment_method").hide();
            $(".courier").hide();
            $(".courier_package").hide();
            $(".delivery_price").hide();
            $("#district_id").empty();
            $("#district_id").append('<option value="">Silahkan Pilih Kecamatan</option>');
            $("#payment_method_id").empty();
            $("#payment_method_id").append('<option value="">Silahkan Pilih Metode Pembayaran</option>');
            $("#courier_id").empty();
            $("#courier_id").append('<option value="">Silahkan Pilih Kurir</option>');
            $("#courier_package_id").empty();
            $("#courier_package_id").append('<option value="">Silahkan Pilih Paket Pengiriman</option>');
            $("#delivery_price").html("Rp "+delivery_price.formatMoney(0, ',', '.'));
            $("#subtotal").html("Rp "+price.formatMoney(0, ',', '.'));
          }
        });

        $("#district_id").change(function(){
          if($("#district_id").val()!=""){
            $.ajax({
              url : '{{URL::route('telesales_manage_order_get_payment_method')}}',
              type: 'POST',
              dataType: 'JSON',
              data: {"_token":"{{ csrf_token() }}","district_id":$("#district_id").val()},
              success : function(data){
                $(".address").show();
                $("#payment_method_id").empty();
                $("#payment_method_id").append('<option value="">Silahkan Pilih Metode Pembayaran</option>');
                $.each(data,function(key,value){
                  $("#payment_method_id").append('<option value="'+key+'">'+value+'</option>');
                });
                $(".payment_method").show();
                $(".courier").hide();
                $(".courier_package").hide();
                $(".delivery_price").hide();
                $("#courier_id").empty();
                $("#courier_id").append('<option value="">Silahkan Pilih Kurir</option>');
                $("#courier_package_id").empty();
                $("#courier_package_id").append('<option value="">Silahkan Pilih Paket Pengiriman</option>');
                $("#delivery_price").html("Rp "+delivery_price.formatMoney(0, ',', '.'));
                $("#subtotal").html("Rp "+price.formatMoney(0, ',', '.'));
              }
            });
          }else{
            $(".address").hide();
            $(".payment_method").hide();
            $(".courier").hide();
            $(".courier_package").hide();
            $(".delivery_price").hide();
            $("#payment_method_id").empty();
            $("#payment_method_id").append('<option value="">Silahkan Pilih Metode Pembayaran</option>');
            $("#courier_id").empty();
            $("#courier_id").append('<option value="">Silahkan Pilih Kurir</option>');
            $("#courier_package_id").empty();
            $("#courier_package_id").append('<option value="">Silahkan Pilih Paket Pengiriman</option>');
            $("#delivery_price").html("Rp "+delivery_price.formatMoney(0, ',', '.'));
            $("#subtotal").html("Rp "+price.formatMoney(0, ',', '.'));
          }
        });

        $("#payment_method_id").change(function(){
          if($("#payment_method_id").val()!=""){
            $.ajax({
              url : '{{URL::route('telesales_manage_order_get_courier')}}',
              type: 'POST',
              dataType: 'JSON',
              data: {"_token":"{{ csrf_token() }}","district_id":$("#district_id").val(),"payment_method_id":$("#payment_method_id").val()},
              success : function(data){
                $("#courier_id").empty();
                $("#courier_id").append('<option value="">Silahkan Pilih Kurir</option>');
                $.each(data,function(key,value){
                  $("#courier_id").append('<option value="'+key+'">'+value+'</option>');
                });
                $(".courier").show();
                $(".courier_package").hide();
                $(".delivery_price").hide();
                $("#courier_package_id").empty();
                $("#courier_package_id").append('<option value="">Silahkan Pilih Paket Pengiriman</option>');
                $("#delivery_price").html("Rp "+delivery_price.formatMoney(0, ',', '.'));
                $("#subtotal").html("Rp "+price.formatMoney(0, ',', '.'));
              }
            });
          }else{
            $(".courier").hide();
            $(".courier_package").hide();
            $(".delivery_price").hide();
            $("#courier_id").empty();
            $("#courier_id").append('<option value="">Silahkan Pilih Kurir</option>');
            $("#courier_package_id").empty();
            $("#courier_package_id").append('<option value="">Silahkan Pilih Paket Pengiriman</option>');
            $("#delivery_price").html("Rp "+delivery_price.formatMoney(0, ',', '.'));
            $("#subtotal").html("Rp "+price.formatMoney(0, ',', '.'));
          }
        });

        $("#courier_id").change(function(){
          if($("#courier_id").val()!=""){
            $.ajax({
              url : '{{URL::route('telesales_manage_order_get_courier_package')}}',
              type: 'POST',
              dataType: 'JSON',
              data: {"_token":"{{ csrf_token() }}","district_id":$("#district_id").val(),"courier_id":$("#courier_id").val()},
              success : function(data){
                $("#courier_package_id").empty();
                $("#courier_package_id").append('<option value="">Silahkan Pilih Paket Pengiriman</option>');
                $.each(data,function(key,value){
                  $("#courier_package_id").append('<option value="'+key+'">'+value+'</option>');
                });
                $(".courier_package").show();
                $(".delivery_price").hide();
                $("#delivery_price").html("Rp "+delivery_price.formatMoney(0, ',', '.'));
                $("#subtotal").html("Rp "+price.formatMoney(0, ',', '.'));
              }
            });
          }else{
            $(".courier_package").hide();
            $(".delivery_price").hide();
            $("#courier_package_id").empty();
            $("#courier_package_id").append('<option value="">Silahkan Pilih Paket Pengiriman</option>');
            $("#delivery_price").html("Rp "+delivery_price.formatMoney(0, ',', '.'));
            $("#subtotal").html("Rp "+price.formatMoney(0, ',', '.'));
          }
        });

        $("#courier_package_id").change(function(){
          if($("#courier_package_id").val()!=""){
            $.ajax({
              url : '{{URL::route('telesales_manage_order_get_delivery_price')}}',
              type: 'POST',
              dataType: 'JSON',
              data: {"_token":"{{ csrf_token() }}","courier_package_id":$("#courier_package_id").val(),"district_id":$("#district_id").val(),"fg_code":$("#fg_code").val()},
              success : function(data){
                if(typeof data.delivery_price != 'undefined'){
                  delivery_price = data.delivery_price;
                }else {
                  delivery_price = 0;
                }

                subtotal = price+delivery_price;

                $("#delivery_price").html("Rp "+delivery_price.formatMoney(0, ',', '.'));
                $("#subtotal").html("Rp "+subtotal.formatMoney(0, ',', '.'));

                $(".delivery_price").show();
              }
            });
          }else{
            delivery_price = 0;
            $(".delivery_price").hide();
            $("#delivery_price").html("Rp "+delivery_price.formatMoney(0, ',', '.'));
            $("#subtotal").html("Rp "+price.formatMoney(0, ',', '.'));
          }
        });
      }
    }

    function showModal(){
      $('#modal_view').modal('show');
    }

    function resetModal(){
      $("#modal-content").html("");
    }

    function getModalFormData(){
      var popup_form = {};
      $("form#popup_form :input").each(function(){
        popup_form[$(this).attr('name')]=$(this).val();
      });
      return popup_form;
    }

    function setSubmitModalEvent(url){
      $("#popup_form").submit(function(e) {
        e.preventDefault();

        $.spin("show");
        $.ajax({
          url : url,
          type: 'POST',
          dataType: 'JSON',
          data: getModalFormData(),
          success : function(data){
            $("#title").html("Pesan");
            $("#message").html(data.message);
            $("#footer").html('<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>');
            showModal();
            $('#modal_view').on('hidden.bs.modal',function(){
              resetModal();
            });
            table.fnReloadAjax();
            $.spin("hide");
          }
        });
      });
    }

    function create(){
      initializeModal($("#modal-template").html(),"Tambah Data Baru");
      showModal();
      $('#modal_view').on('hidden.bs.modal',function(){
        resetModal();
      });

      setSubmitModalEvent('{{URL::route('telesales_manage_order_create')}}');
    }

    function cancel(e) {
      initializeModal($("#modal-template-cancel-order").html(),"Cancel Order",$(e).data('id'),$(e).data('refference_number'));
      showModal();
      $('#modal_view').on('hidden.bs.modal',function(){
        resetModal();
      });

      setSubmitModalEvent('{{URL::route('telesales_manage_order_cancel_order')}}');
    }
  </script>
@stop
