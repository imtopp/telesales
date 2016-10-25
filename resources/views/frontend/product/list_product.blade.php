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

  <title>{{ config('settings.app_name') }} | List Product</title>

  <link href="{{ URL::asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css">
  <link href="{{ URL::asset('assets/css/font.css') }}" rel="stylesheet" type="text/css">
  <script src="{{ URL::asset('assets/js/jquery-1.11.0.min.js') }}"></script>
  <script src="{{ URL::asset('assets/js/bootstrap.min.js') }}"></script>

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
      display: inline-block;
    }

    .product-image-block {
      height:148pt;
    }

    .product-image {
      max-width: 100%;
      max-height:148pt;
    }

    .product-name {
      font-size: large;
      font-weight: bold;
      height: 22px;
      display: block;
      overflow: hidden;
    }

    .product-action {
      display: -webkit-inline-box;
      float: initial;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="content">
      @foreach($data as $category => $products)
      <div class="row" data-toggle="collapse" data-target="#{{str_replace(' ','_',$category)}}" style="cursor: pointer;">
        <h1><b>{{$category}}</b></h1>
        <hr/>
      </div>
      <div id="{{str_replace(' ','_',$category)}}" class="collapse row">
        @foreach($products as $product)
          <div class="product col-xs-12 col-md-2" data-id="{{$product['id']}}">
            <div class="img_thumbnail row product-image-block">
              <img src="{{$product['image_url']}}" class="product-image"/>
            </div>
            <span class="row product-name">{{$product['name']}}</span>
            <div class="row">Rp {{ number_format($product['price'],0,",",".") }}</div>
            <div class="col-xs-5 col-md-12 product-action">
              {!! Form::button('Beli',array('class'=>'btn btn-danger btn-block','style'=>'width:100%')) !!}
            </div>
          </div>
        @endforeach
      </div>
      @endforeach
    </div>
  </div>

<script>
$(".product").click(function(e){
  var id = $(this).data("id");

  window.location.href = "{{ URL::route('product_detail') }}"+"?id="+id;
});
</script>
</body>
</html>
