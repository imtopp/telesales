<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
  <title>List Product</title>

  <link href="{{ URL::asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css">
  <link href="{{ URL::asset('assets/css/font.css') }}" rel="stylesheet" type="text/css">
  <script src="{{ URL::asset('assets/js/jquery-1.11.0.min.js') }}"></script>
  <script src="{{ URL::asset('assets/js/bootstrap.min.js') }}"></script>

  <style>
  @font-face {
    font-family: 'Roboto-regular';
    src: url('{{ URL::asset('assets/font/roboto/roboto-regular.ttf') }}');
    font-weight: normal;
    font-style: normal;
  }

  @font-face {
    font-family: 'Roboto-light';
    src: url('{{ URL::asset('assets/font/roboto/roboto-light.ttf') }}');
    font-weight: normal;
    font-style: normal;
  }

  @font-face {
    font-family: 'Roboto-Bold';
    src: url('{{ URL::asset('assets/font/roboto/roboto-bold.ttf') }}');
    font-weight: normal;
    font-style: normal;
  }


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

  .left-side {
    display: inline-block;
  }

  .right-side {
    display: inline-block;
  }

  .img-nav {
    padding: 0;
    margin: 0;
  }

  .img-nav li {
    list-style: none;
    width: 50px;
    height: 50px;
    display: inline-block;
    border: grey;
    border-style: solid;
    border-width: thin;
    vertical-align: top;
  }

  .img-nav li a {
    display: block;
  }

  .img-nav li a img{
    max-height:48px;
    max-width:48px;
  }

  .form-group{
    font-family: 'din_bold',sans-serif;
  }
  </style>
</head>
<body>
  <div class="container">
    <div class="content">
      <div id="product_name row">
        <h1 style="font-weight:bold">{{ $product['name'] }}</h1>
      </div>
      <div id="product_detail row">
        <div class="col-md-1">
          &nbsp;
        </div>
        <div class="left-side col-xs-12 col-md-5">
          <div class="img_preview row" style="margin-bottom: 10px; margin-top: 10px;">
            <div class="col-md-1">
              &nbsp;
            </div>
            <div class="col-md-10">
              <img id="img_preview" src="{{$colours[0]['image_url']}}" style="max-width:100%; max-height:360pt"/>
            </div>
          </div>
          <hr>
          <div class="img_thumb row">
            <ul class="img-nav">
              @if(isset($colours))
              @foreach($colours as $colour)
              <li>
                <a class="img-nav-icon" id="thumbnail_{{$colour['id']}}" data-id="{{$colour['id']}}" data-fg-code="{{$colour['fg_code']}}" data-price="{{$colour['price']}}" href="{{$colour['image_url']}}">
                  <img src="{{$colour['image_url']}}" onerror="this.onerror=null;this.src='assets/img/img_not_found.jpg';"/>
                </a>
              </li>
              @endforeach
              @endif
            </ul>
          </div>
        </div>
        <div class="right-side col-xs-12 col-md-5">
          <h4 style=" font-family:'roboto-regular', sans-serif;">Detail Product</h4>
          <hr/>
          <div class="detail-product" style="text-align:left;">
            <p>{!!$product['description']!!}</p>
          </div>

          {!! Form::open(array('route'=>'buy_product','class'=>'form')) !!}
          <div class="form-group">
            {!! Form::hidden('fg_code',$colours[0]['fg_code'],array('id'=>'fg_code')) !!}
          </div>
          <div class="form-group">
            {!! Form::hidden('price',$colours[0]['price'],array('id'=>'price')) !!}
          </div>
          <div class='form-group'>
            {!! Form::label('Variant') !!}
            {!! Form::select('product_colour_id',isset($colours_dropdown)?$colours_dropdown:array(),null,array('required','id'=>'product_colour_id','class'=>'form-control','placeholder'=>'Metode Pembayaran Anda')) !!}
          </div>
          <div class='form-group'>
            {!! Form::label('QTY') !!}
            {!! Form::input('number','qty',1,array('required','id'=>'qty','class'=>'form-control','placeholder'=>'Jumlah Barang','min'=>'1')) !!}
          </div>
          <h2 id="price_label" style="margin-top: 20px; font-family: 'Roboto-bold', sans-serif;">Rp {{ number_format($colours[0]['price'],0,",",".") }}</h2>
          <div class="form-group">
            {!! Form::submit('Beli',array('class'=>'btn btn-danger','style'=>'width: 100%;')) !!}
          </div>
          {!! Form::close() !!}

        </div>
      </div>
    </div>
  </div>

  <script>
  $(document).ready(function(){
    @if(!isset($product))
    $('.content').remove();
    $('#modal_view').modal('show');
    $('#modal_view').on('hidden.bs.modal',function(){
      window.location.href = "{{ URL::to('/') }}";
    });
    @endif

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

    var colour_id  = $("#thumbnail_"+$("#product_colour_id").val());
    var price = colour_id.data("price")*$("#qty").val();
    var image = colour_id.find("img").attr("src");

    $("#price_label").html("Rp "+price.formatMoney(0, ',', '.'));
    $("#price").val(price);

    function imageExists(image_url){
      var http = new XMLHttpRequest();
      http.open('HEAD', image_url, false);
      http.send();
      return http.status != 404;
    }

    if(!imageExists(image)){
      image = "assets/img/img_not_found.jpg";
    }
    $("#img_preview").attr("src",image);

    $("#fg_code").val(colour_id.data("fg-code"));
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

  $(".img-nav-icon").click(function(e){
    e.preventDefault();
    $("#img_preview").attr("src",$(this).find("img").attr("src"));
    var price = $(this).data("price")*$("#qty").val();
    $("#price_label").html("Rp "+price.formatMoney(0, ',', '.'));
    $("#price").val(price);
    $("#product_colour_id").val($(this).data("id"));
    $("#fg_code").val($(this).data("fg-code"));
  });

  $("#product_colour_id").change(function(){
    $("#thumbnail_"+$(this).val()).click();
  });

  $("#qty").change(function(){
    var price = $("#thumbnail_"+$("#product_colour_id").val()).data("price")*$(this).val();
    $("#price_label").html("Rp "+price.formatMoney(0, ',', '.'));
    $("#price").val(price);
  });
  </script>

  <div class="modal fade" id="modal_view" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Message</h4>
        </div>
        <div class="modal-body">
          <p>{{ $message }}</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
