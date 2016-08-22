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
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="content">
                <div class="collapse navbar-collapse">
                	<div class="product" data-fgcode="{{$product['fg_code']}}">
					    <img src="{{$product['image_url']}}" width="145" height="198" style="margin-bottom: 10px;"/>
					    <br/>
					    <b style="font-size: large;">{{$product['name']}}</b>
                        <h4>Rp {{ number_format($product['price'],2,",",".") }}</h4>
					</div>
				</div>
            </div>
        </div>
    </body>
</html>
