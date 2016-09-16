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
  </style>
</head>
<body>
  <div class="container">
    <div class="content">
      @foreach($all_category as $category)
      <div class="row" data-toggle="collapse" data-target="#{{str_replace(' ','_',$category['name'])}}" style="cursor: pointer;">
        <h1><b>{{$category['name']}}</b></h1><hr/></a>
      </div>
      <div id="{{str_replace(' ','_',$category['name'])}}" class="collapse">
        <?php $count=array();$iteration=0; ?>
        @foreach($all_product as $product)
        <?php $iteration++ ?>
        @if($product['category']==$category['name'])
        @if(!isset($count[$category['name']]))
        <?php $count[$category['name']]=0; ?>
        @endif
        @if($count[$category['name']]==0)
        <div class=row>
          @endif
          <div class="product col-xs-12 col-md-2" data-id="{{$product['id']}}">
            <div class="img_thumbnail row" style="height:148pt;">
              <img src="{{$product['image_url']}}" style="max-width: 100%; max-height:148pt;"/>
            </div>
            <b style="font-size: large;">{{$product['name']}}</b><br>
            Rp {{ number_format($product['price'],0,",",".") }}<br>
            <div class="col-xs-6 col-md-12" style="display: -webkit-inline-box; float: initial;">
              {!! Form::button('Beli',array('class'=>'btn btn-danger btn-block')) !!}
            </div>
          </div>
          <?php $count[$category['name']]++;?>
          @if($count[$category['name']]==6)
        </div>
        <?php $count[$category['name']]=0 ?>
        @endif
        @endif
        @if($iteration==count($all_product))
      </div>
      @endif
      @endforeach
    </div>
    @endforeach
  </div>
</div>

<script>
$(function(){
  $("body").hide().show();
});

$(".product").click(function(e){
  var id = $(this).data("id");

  window.location.href = "{{ URL::to('/product_detail') }}"+"?id="+id;
});
</script>
</body>
</html>
