@extends('layout\login_template')

@section('title','Pendaftaran')

@section('content')
@foreach($errors->all() as $error)
    <p class="alert alert-danger">{!!$error!!}</p>
@endforeach
{!!Form::open(['url'=>URL::route('register'),'id'=>"register_form",'class'=>'form form-horizontal'])!!}
<h1>Create Account</h1>
<div class="form-group">
    <div class="controls">
        {!! Form::email('email',Input::old('email'),['class'=>'form-control','placeholder'=>'Email','required']) !!}
    </div>
</div>
<div class="form-group">
    <div class="controls">
        {!! Form::password('password',['class'=>'form-control','placeholder'=>'Password','required']) !!}
    </div>
</div>
<div class="form-group">
    <div class="controls">
        {!! Form::password('password_confirmation',['class'=>'form-control','placeholder'=>'Confirm Password','required']) !!}
    </div>
</div>
<div>
    {!!Form::submit('Register',['class'=>'btn btn-default submit','style'=>'float: initial; margin-left: initial;'])!!}
</div>

<div class="clearfix"></div>

<div class="separator">
  <p class="change_link">Already a member ?
    {!!HTML::link(URL::route('login'),'Log In',['class'=>'to_register link'])!!}
  </p>

  <div class="clearfix"></div>
  <br />

  <div>
    <h1><i class="fa {{ config('settings.fa_icon') }}"></i> {{ config('settings.app_name') }}</h1>
    <p>©2016 All Rights Reserved. Gentelella Alela! is a Bootstrap 3 template. Privacy and Terms</p>
  </div>
</div>
{!!Form::close()!!}
@endsection

@section('page-js-file')
<!-- jQuery -->
<script src="{{ URL::asset('assets/js/jquery-1.11.0.min.js') }}"></script>
<!-- spinner -->
<script src="{{ URL::asset('assets/js/spin.min.js') }}"></script>
@endsection

@section('page-js-script')
<script>
  $(document).ready(function(){
    (function($) {
      $.extend({
        spin: function(spin, opts) {
          if (opts === undefined) {
            opts = {
              lines: 13, // The number of lines to draw
              length: 20, // The length of each line
              width: 10, // The line thickness
              radius: 30, // The radius of the inner circle
              corners: 1, // Corner roundness (0..1)
              rotate: 0, // The rotation offset
              direction: 1, // 1: clockwise, -1: counterclockwise
              color: '#000', // #rgb or #rrggbb or array of colors
              speed: 1, // Rounds per second
              trail: 56, // Afterglow percentage
              shadow: false, // Whether to render a shadow
              hwaccel: false, // Whether to use hardware acceleration
              className: 'spinner', // The CSS class to assign to the spinner
              zIndex: 2e9, // The z-index (defaults to 2000000000)
              top: '50%', // Top position relative to parent
              left: '50%' // Left position relative to parent
            };
          }

          var data = $('body').data();

          if (data.spinner) {
            data.spinner.stop();
            delete data.spinner;
            $("#spinner_modal").remove();
            return this;
          }

          if (spin=="show") {
            var spinElem = this;

            $('body').append('<div id="spinner_modal" style="background-color: rgba(0, 0, 0, 0.3); width:100%; height:100%; position:fixed; top:0px; left:0px; z-index:' + (opts.zIndex - 1) + '"/>');
            spinElem = $("#spinner_modal")[0];

            data.spinner = new Spinner($.extend({
              color: $('body').css('color')
            }, opts)).spin(spinElem);
          }
        }
      });
    })(jQuery);

    $("#register_form").submit(function(e) {
      $.spin("show");
    });

    $(".link").click(function(e) {
      $.spin("show");
    });
  });
</script>
@stop
