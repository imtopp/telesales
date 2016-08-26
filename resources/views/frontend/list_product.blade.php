<!DOCTYPE html>
<html>
    <head>
        <title>List Product</title>

        <link href="{{ URL::asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css">
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
                font-family: 'Lato', sans-serif;
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
                <div class="collapse navbar-collapse">
                        @foreach($all_category as $category)
                        <div data-toggle="collapse" data-target="#{{str_replace(' ','_',$category['name'])}}" style="cursor: pointer;">
                            <h1><b>{{$category['name']}}</b></h1><hr/></a>
                        </div>
                        <div id="{{str_replace(' ','_',$category['name'])}}" class="collapse">
                            @foreach($all_product as $product)
                                @if($product['category']==$category['name'])
                                <div class="product" data-id="{{$product['id']}}">
                                    <img src="{{$product['image_url']}}" width="145" height="198" style="margin-bottom: 10px;"/>
                                    <br/>
                                    <b style="font-size: large;">{{$product['name']}}</b>
                                </div>
                                @endif
                            @endforeach
                        </div>
                        @endforeach
                </div>
            </div>
        </div>

        <script>
            $(".product").click(function(e){
                var id = $(this).data("id");

                window.location.href = "{{ URL::to('/product_detail') }}"+"?id="+id;
            });
        </script>
    </body>
</html>
