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
  <script src="{{ URL::asset('assets/js/jquery-1.11.0.min.js') }}"></script>
  <script src="{{ URL::asset('assets/js/bootstrap.min.js') }}"></script>

  <style>
  @font-face {
    font-family: 'Roboto-light';
    src: url('{{ URL::asset('assets/font/roboto/Roboto-light.ttf') }}');
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
    font-family: 'Roboto-light', sans-serif;
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
    font-family: 'roboto-regular',sans-serif;
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
          <h4>{{ $product['qty'] }} x Rp {{ number_format($product['price'],0,",",".") }}</h4>
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
        <!--<div class='form-group'>
        {!! Form::label('Metode Pembayaran') !!}
        {!! Form::select('payment_type',$payment_type,null,array('required','class'=>'form-control','placeholder'=>'Metode Pembayaran Anda')) !!}
      </div>-->
      <div class='form-group' style="text-align:left">
        <h4>Harga Barang : Rp {{ number_format($product['total_price'],0,",",".") }}</h4>
      </div>
      <div class='form-group' style='text-align:left'>
        {!! Form::checkbox('agree','yes',null,array('required')) !!}
        <span>Saya setuju dengan <a href="#" id="disclaimer">Syarat & Ketentuan</a> yang berlaku</span>
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
          <h4 class="modal-title">Message</h4>
        </div>
        <div class="modal-body">
          <p id="message">{{ $message }}</p>
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

  $("#user_form").submit(function(e) {
    e.preventDefault();
    var user_form = {};
    $("form#user_form :input").each(function(){
      user_form[$(this).attr('name')]=$(this).val();
    });
    user_form = {"_token":"{{ csrf_token() }}","user_form":user_form};
    $.ajax({
      url : '{{URL::route('checkout')}}',
      type: 'POST',
      dataType: 'JSON',
      data: user_form,
      success : function(data){
        if(data.success){
          $("#message").html(data.message);
          $('#modal_view').modal('show');
          $('#modal_view').on('hidden.bs.modal',function(){
            window.location.href = "{{ URL::to('/') }}";
          });
        }
      }
    })
  });
});

$("#disclaimer").click(function(e){
  e.preventDefault();

  $('#modal_view').modal('show');
});
</script>
</body>
</html>
