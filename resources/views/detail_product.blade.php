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
        </style>
    </head>
    <body>
        <div class="container">
            <div class="content">
                <div class="collapse navbar-collapse">
                    <div class="product">
                        <h1>{{ $product['name'] }}</h1>
                        <img src="{{$product['image_url']}}" width="320" height="480" style="margin-bottom: 10px; margin-top: 10px;"/>
                        <br/>
                        <h2>Rp {{ number_format($product['price'],2,",",".") }}</h2>
                        <br/>
                        <button type="button" id="buy" name="buy" class="btn btn-danger" style="width: 320px; margin-top: 20px">Beli</button>
                        <h4 style="margin-top: 50px">Detail Product</h4>
                        <hr/>
                        <p>{!!$product['description']!!}</p>
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
            });

            $("#buy").click(function(e){
                var fg_code = $(this).data("fgcode");

                window.location.href = "{{ URL::to('/buy_product') }}"+"?id="+{{ $product['fg_code'] }};
            });
        </script>

        <div class="modal fade" id="modal_view" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Modal Header</h4>
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
