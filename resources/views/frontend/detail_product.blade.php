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
            }

            .img-nav li a {
              display: block;
            }

            .img-nav li a img{
              max-height:48px;
              max-width:48px;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="content">
                <div class="collapse navbar-collapse">
                    <div class="product">
                        <div id="product_name">
                          <h1>{{ $product['name'] }}</h1>
                        </div>
                        <div class="img_preview" style="margin-bottom: 10px; margin-top: 10px; min-height: 480px;">
                          <img id="img_preview" src="{{$product['image_url']}}" style="max-width:320px; max-height:480px"/>
                        </div>
                        <div class="img_thumb">
                          <ul class="img-nav">
                            <li>
                              <a class="img-nav-icon" href="{{$product['image_url']}}">
                                <img src="{{$product['image_url']}}" onerror="this.onerror=null;this.src='assets/img/img_not_found.jpg';"/>
                              </a>
                            </li>
                            @foreach($colours as $colour)
                            <li>
                              <a class="img-nav-icon" href="{{$colour['image_url']}}">
                                <img src="{{$colour['image_url']}}" onerror="this.onerror=null;this.src='assets/img/img_not_found.jpg';"/>
                              </a>
                            </li>
                            @endforeach
                          </ul>
                        </div>
                        <br/>
                        <h2>Rp {{ number_format(0,2,",",".") }}</h2>
                        <br/>
                        <button type="button" id="buy" name="buy" class="btn btn-danger" style="width: 320px; margin-top: 20px">Beli</button>
                        <h4 style="margin-top: 50px">Detail Product</h4>
                        <hr/>
                        <div class="detail-product">
                          <p>{!!$product['description']!!}</p>
                        </div>
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

            $(".img-nav-icon").click(function(e){
              e.preventDefault();
              $("#img_preview").attr("src",$(this).find("img").attr("src"));
            });

            $("#buy").click(function(e){
              window.location.href = "{{ URL::to('/buy_product') }}"+"?id=";
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
