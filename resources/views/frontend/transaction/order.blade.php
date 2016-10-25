<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link rel="shortcut icon" href="{{ URL::asset('favicon.ico') }}" type="image/x-icon" />

  <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <title>{{ config('settings.app_name') }} | Order Page</title>

  <link href="{{ URL::asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css">
  <link href="{{ URL::asset('assets/css/font.css') }}" rel="stylesheet" type="text/css">
  <script src="{{ URL::asset('assets/js/jquery-1.11.0.min.js') }}"></script>
  <script src="{{ URL::asset('assets/js/bootstrap.min.js') }}"></script>
  <script src="{{ URL::asset('assets/js/spin.min.js') }}"></script>

  <style>
  html, body {
    height: 100%;
  }

  body {
    margin: 0;
    padding: 0;
    width: 100%;
    display: table;
    font-weight: 100;
    font-family: 'din_regular', sans-serif;
  }

  .container {
    text-align: center;
    display: table-cell;
    vertical-align: top;
  }

  .content {
    text-align: center;
  }
  .product {
    margin-bottom: 20px;
    cursor: pointer;
  }

  .form-group{
    font-family: 'din_bold',sans-serif;
  }

  .modal .modal-body{
    max-height: 420px;
    overflow-y: auto;
  }

  .modal-title{
    font-family: 'din_bold',sans-serif;
  }
  </style>
</head>
<body>
  <div class="container">
    <div class="content row">
      <div class="col-md-1">
        &nbsp;
      </div>
      <div class="left-side col-xs-12 col-md-5" style="margin-top:20px">
        <div class="col-md-1">
          &nbsp;
        </div>
        <div class="product col-xs-12 col-md-10" data-fgcode="{{$product['fg_code']}}">
          <img src="{{$product['image_url']}}" style="max-width:100%; max-height:360pt; margin-bottom: 10px;" onerror="this.onerror=null;this.src='assets/img/img_not_found.jpg';"/>
          <br/>
          <b style="font-size: large;">{{$product['name']}}</b>
          <h4>Rp {{ number_format($product['price'],0,",",".") }}</h4>
        </div>
      </div>
      <div class="right-side col-xs-12 col-md-5" style="display: inline-block; vertical-align:top">
        <ul>
          @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
          @endforeach
        </ul>

        {!! Form::open(array(null,'class'=>'form','id'=>'user_form')) !!}
        <div class="form-group">
          {!! Form::hidden('fg_code', $product['fg_code'],array('id'=>'fg_code')) !!}
        </div>
        <div class="form-group">
          {!! Form::label('Nama Sesuai Identitas') !!}
          {!! Form::text('name',null,array('required','class'=>'form-control','placeholder'=>'Nama Anda')) !!}
        </div>
        <div class='form-group'>
          {!! Form::label('Alamat Sesuai Identitas') !!}
          {!! Form::text('address',null,array('required','class'=>'form-control','placeholder'=>'Alamat anda sesuai dengan identitas')) !!}
        </div>
        <div class='form-group'>
          {!! Form::label('Jenis Identitas') !!}
          {!! Form::select('identity_type',array('KTP'=>'KTP','SIM'=>'SIM','KITAS'=>'KITAS','PASPOR'=>'PASPOR'),null,array('required','class'=>'form-control','placeholder'=>'Jenis Identitas Anda')) !!}
        </div>
        <div class='form-group'>
          {!! Form::label('Nomor Identitas') !!}
          {!! Form::text('identity_number',null,array('required','class'=>'form-control','placeholder'=>'Nomor Identitas Anda')) !!}
        </div>
        <div class='form-group'>
          {!! Form::label('Email') !!}
          {!! Form::input('email','email',null,array('required','class'=>'form-control','placeholder'=>'Email Anda')) !!}
        </div>
        <div class='form-group'>
          {!! Form::label('Nomor Kontak') !!}
          {!! Form::text('mdn',null,array('required','class'=>'form-control','placeholder'=>'Nomor Kontak Anda dalam format 088xxxxxxxxx','onkeypress'=>'return isNumberKey(event);')) !!}
        </div>
        <div class='form-group'>
          {!! Form::label('Provinsi Pengiriman') !!}
          {!! Form::select('province',[''=>'Silahkan Pilih Provinsi'],null,array('required','id'=>'province','class'=>'form-control')) !!}
        </div>
        <div class='form-group city' style="display:none">
          {!! Form::label('Kota Pengiriman') !!}
          {!! Form::select('city',[''=>'Silahkan Pilih Kota'],null,array('required','id'=>'city','class'=>'form-control')) !!}
        </div>
        <div class='form-group district' style="display:none">
          {!! Form::label('Kecamatan Pengiriman') !!}
          {!! Form::select('district_id',[''=>'Silahkan Pilih Kecamatan'],null,array('required','id'=>'district_id','class'=>'form-control')) !!}
        </div>
        <div class='form-group address' style="display:none">
          {!! Form::label('Alamat Pengiriman') !!}
          {!! Form::textarea('delivery_address',null,array('required','class'=>'form-control','placeholder'=>'Alamat Pengiriman Anda')) !!}
        </div>
        <div class='form-group payment_method' style="display:none">
          {!! Form::label('Metode Pembayaran') !!}
          {!! Form::select('payment_method_id',[''=>'Silahkan Pilih Metode Pembayaran'],null,array('required','id'=>'payment_method_id','class'=>'form-control')) !!}
        </div>
        <div class='form-group courier' style="display:none">
          {!! Form::label('Kurir') !!}
          {!! Form::select('courier_id',[''=>'Silahkan Pilih Kurir'],null,array('required','id'=>'courier_id','class'=>'form-control')) !!}
        </div>
        <div class='form-group courier_package' style="display:none">
          {!! Form::label('Paket Pengiriman Kurir') !!}
          {!! Form::select('courier_package_id',[''=>'Silahkan Pilih Paket Pengiriman'],null,array('required','id'=>'courier_package_id','class'=>'form-control')) !!}
        </div>
        <div class='form-group' style="text-align:left">
          <h4>Harga Barang : Rp {{ number_format($product['price'],0,",",".") }}</h4>
          <h4 class="delivery_price" style="display:none;">Ongkos Kirim : <span id="delivery_price">Rp 0</span></h4>
          <h4>Subtotal : <span id="subtotal">Rp {{ number_format($product['price'],0,",",".") }}</span></h4>
        </div>
        <div class='form-group' style='text-align:left'>
          {!! Form::checkbox('agree','yes',null,array('required')) !!}
          <span>Saya setuju dengan <a href="#" id="disclaimer"><b>Syarat & Ketentuan</b></a> yang berlaku</span>
        </div>
        <div class="form-group" style="margin-top:20px">
          {!! Form::submit('Beli',array('id'=>'buy','class'=>'btn btn-danger','style'=>'width: 100%;')) !!}
        </div>
        {!! Form::close() !!}
      </div>
    </div>

    <div class="modal fade" id="modal_view" role="dialog">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 id="title" class="modal-title"></h4>
          </div>
          <div class="modal-body">
            <span id="message"></span>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" id="close_dialog" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
  var price = {{$product['price']}};
  $(document).ready(function(){
    @if(!isset($product))
    $('.content').remove();
    $('#modal_view').modal('show');
    $('#modal_view').on('hidden.bs.modal',function(){
      window.location.href = "{{ URL::route('show_all_product') }}";
    });
    @endif

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

    $(function(){
      $.ajax({
        url : '{{URL::route('order_get_province_dropdown')}}',
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

    $("#province").change(function(){
      if($("#province").val()!=""){
        $.ajax({
          url : '{{URL::route('order_get_city_dropdown')}}',
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
            $("#delivery_price").html("Rp 0");
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
        $("#delivery_price").html("Rp 0");
        $("#subtotal").html("Rp "+price.formatMoney(0, ',', '.'));
      }
    });

    $("#city").change(function(){
      if($("#city").val()!=""){
        $.ajax({
          url : '{{URL::route('order_get_district_dropdown')}}',
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
            $("#delivery_price").html("Rp 0");
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
        $("#delivery_price").html("Rp 0");
        $("#subtotal").html("Rp "+price.formatMoney(0, ',', '.'));
      }
    });

    $("#district_id").change(function(){
      if($("#district_id").val()!=""){
        $.ajax({
          url : '{{URL::route('order_get_payment_method_dropdown')}}',
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
            $("#delivery_price").html("Rp 0");
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
        $("#delivery_price").html("Rp 0");
        $("#subtotal").html("Rp "+price.formatMoney(0, ',', '.'));
      }
    });

    $("#payment_method_id").change(function(){
      if($("#payment_method_id").val()!=""){
        $.ajax({
          url : '{{URL::route('order_get_courier_dropdown')}}',
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
            $("#delivery_price").html("Rp 0");
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
        $("#delivery_price").html("Rp 0");
        $("#subtotal").html("Rp "+price.formatMoney(0, ',', '.'));
      }
    });

    $("#courier_id").change(function(){
      if($("#courier_id").val()!=""){
        $.ajax({
          url : '{{URL::route('order_get_courier_package_dropdown')}}',
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
            $("#delivery_price").html("Rp 0");
            $("#subtotal").html("Rp "+price.formatMoney(0, ',', '.'));
          }
        });
      }else{
        $(".courier_package").hide();
        $(".delivery_price").hide();
        $("#courier_package_id").empty();
        $("#courier_package_id").append('<option value="">Silahkan Pilih Paket Pengiriman</option>');
        $("#delivery_price").html("Rp 0");
        $("#subtotal").html("Rp "+price.formatMoney(0, ',', '.'));
      }
    });

    $("#courier_package_id").change(function(){
      if($("#courier_package_id").val()!=""){
        $.ajax({
          url : '{{URL::route('order_get_delivery_price')}}',
          type: 'POST',
          dataType: 'JSON',
          data: {"_token":"{{ csrf_token() }}","courier_package_id":$("#courier_package_id").val(),"district_id":$("#district_id").val(),"fg_code":$("#fg_code").val()},
          success : function(data){
            if(typeof data.delivery_price != 'undefined'){
              $("#delivery_price").html("Rp "+data.delivery_price.formatMoney(0, ',', '.'));
              $("#subtotal").html("Rp "+(data.delivery_price+price).formatMoney(0, ',', '.'));
            }else {
              $("#delivery_price").html("Rp 0");
              $("#subtotal").html("Rp "+price.formatMoney(0, ',', '.'));
            }
            $(".delivery_price").show();
          }
        });
      }else{
        $(".delivery_price").hide();
        $("#delivery_price").html("Rp 0");
        $("#subtotal").html("Rp "+price.formatMoney(0, ',', '.'));
      }
    });

    $("#user_form").submit(function(e) {
      e.preventDefault();
      $.spin('show');
      var data = {};

      $("form#user_form :input").each(function(){
        data[$(this).attr('name')]=$(this).val();
      });
      data["_token"]="{{ csrf_token() }}";
/*
      $.ajax({
        url : '{{URL::route('checkout')}}',
        type: 'POST',
        dataType: 'JSON',
        data: data,
        success : function(data){
          $.spin('hide');
          if(data.success){
            $("#title").html("Pesan");
            $("#message").html(data.message);
            $('#modal_view').modal('show');
            $('#modal_view').on('hidden.bs.modal',function(){
              window.location.href = "{{ URL::route('show_all_product') }}";
            });
          }
        }
      });*/
      $.spin('hide');
      $("#title").html("Pesan");
      $("#message").html("testing");
      $('#modal_view').modal('show');
      $('#modal_view').on('hidden.bs.modal',function(){
        window.location.href = "{{ URL::route('show_all_product') }}";
      });
    });
  });

  function isNumberKey(event){
    var charCode = (event.which) ? event.which : event.keyCode;
    if (charCode != 43 && charCode > 31 && (charCode < 48 || charCode > 57))
      return false;
    return true;
  }

  $("#disclaimer").click(function(e){
    e.preventDefault();

    $("#title").html('{{$message_title}}');
    $("#message").html($("#disclaimer_template").html());
    $('#modal_view').modal('show');
  });
  </script>

  <script type = "text/template" id="disclaimer_template">
    {!! $message !!}
  </script>

</body>
</html>
