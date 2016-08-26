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
            <div class="left-side" style="display: inline-block; margin-right:20px; margin-top:20px">
            	<div class="product" data-fgcode="{{$product['fg_code']}}">
                <img src="{{$product['image_url']}}" width="160" height="240" style="margin-bottom: 10px;" onerror="this.onerror=null;this.src='assets/img/img_not_found.jpg';"/>
                <br/>
                <b style="font-size: large;">{{$product['name']}}</b>
                <h4>{{ $product['qty'] }} x Rp {{ number_format($product['price'],0,",",".") }}</h4>
  		        </div>
            </div>
            <div class="right-side" style="display: inline-block; vertical-align:top">
      				<ul>
      					@foreach ($errors->all() as $error)
      					<li>{{ $error }}</li>
      					@endforeach
      				</ul>

  	          {!! Form::open(array('route'=>'checkout','class'=>'form')) !!}
    					<div class="form-group">
    						{!! Form::hidden('fg_code', $product['fg_code']) !!}
    					</div>
              <div class="form-group">
    						{!! Form::hidden('qty', $product['qty']) !!}
    					</div>
    					<div class="form-group">
    						{!! Form::label('Nama') !!}
    						{!! Form::text('name',null,array('required','class'=>'form-control','placeholder'=>'Nama Anda')) !!}
    					</div>
    					<div class='form-group'>
    						{!! Form::label('Alamat') !!}
    						{!! Form::text('address',null,array('required','class'=>'form-control','placeholder'=>'Alamat Anda')) !!}
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
    						{!! Form::text('email',null,array('required','class'=>'form-control','placeholder'=>'Email Anda')) !!}
    					</div>
    					<div class='form-group'>
    						{!! Form::label('Nomor Handphone') !!}
    						{!! Form::text('mdn',null,array('required','class'=>'form-control','placeholder'=>'Nomor Handphone Anda')) !!}
    					</div>
    					<div class='form-group'>
    						{!! Form::label('Alamat Pengiriman') !!}
    						{!! Form::textarea('delivery_address',null,array('required','class'=>'form-control','placeholder'=>'Alamat Pengiriman Anda')) !!}
    					</div>
              <div class='form-group'>
                {!! Form::label('Metode Pembayaran') !!}
                {!! Form::select('payment_type',$payment_type,null,array('required','class'=>'form-control','placeholder'=>'Metode Pembayaran Anda')) !!}
              </div>
              <div class='form-group' style="text-align:left">
                <p>Harga Barang : Rp {{ number_format($product['total_price'],0,",",".") }}</p>
                <p>Ongkos Kirim : Rp 0</p>
                <p>Subtotal : Rp {{ number_format($product['total_price'],0,",",".") }}</p>
              </div>
    					<div class="form-group" style="margin-top:20px">
    						{!! Form::submit('Beli',array('class'=>'btn btn-danger','style'=>'width: 100%;')) !!}
    					</div>
  				    {!! Form::close() !!}
            </div>
			    </div>

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
        </script>


    </body>
</html>
