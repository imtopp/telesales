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
                <div class="product">
                  <div id="product_name">
                    <h1 style="font-weight:bold">{{ $product['name'] }}</h1>
                  </div>
                    <div class="left-side" style="display: inline-block;">
                      <div class="img_preview" style="margin-bottom: 10px; margin-top: 10px; min-height: 480px;">
                        <img id="img_preview" src="{{$colours[0]['image_url']}}" style="max-width:320px; max-height:480px"/>
                      </div>
                      <div class="img_thumb">
                        <ul class="img-nav">
                          @if(isset($colours))
                          @foreach($colours as $colour)
                          <li>
                            <a class="img-nav-icon" data-price="{{$colour['price']}}" href="{{$colour['image_url']}}">
                              <img src="{{$colour['image_url']}}" onerror="this.onerror=null;this.src='assets/img/img_not_found.jpg';"/>
                            </a>
                          </li>
                          @endforeach
                          @endif
                        </ul>
                      </div>
                      <br/>
                    </div>
                    <div class="right-side" style="display: inline-block; vertical-align:top">
                      <h4 style="">Detail Product</h4>
                      <hr/>
                      <div class="detail-product" style="text-align:left">
                        <p>{!!$product['description']!!}</p>
                      </div>
                      <h2 id="price">Rp {{ number_format($colours[0]['price'],0,",",".") }}</h2>
                      <br/>
                      <button type="button" id="buy" name="buy" class="btn btn-danger" style="width: 320px; margin-top: 20px">Beli</button>
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
              var price = $(this).data("price");
              $("#price").html("Rp "+price.formatMoney(0, ',', '.'));
              console.log();
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
