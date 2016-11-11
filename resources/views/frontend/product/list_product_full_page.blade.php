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
  <script src="{{ URL::asset('assets/js/spin.min.js') }}"></script>

  <style>
    html, body {
      height:100vh;
    }
  </style>
</head>
<body>
  <div id="content">
    <div class="col-md-4" style="float:left; min-height: 100vh; padding: 0; border-right-style: inset;">
      <iframe id="left-panel" src="{{ URL::route('show_all_product').'?isMicrosite=true' }}" frameborder="0" style="overflow:hidden;height:100vh;width:100%" height="100vh" width="100%">Sorry your browser is not supported.</iframe>
    </div>
    <div class="col-md-8" style="float:right; min-height: 100vh; padding: 0;">
      <img id="logo-smartfren" src="{{ URL::asset('assets/img/logo_smartfren.jpg') }}" style="max-height:100vh; max-width:100%;">
      <iframe id="right-panel" src="" frameborder="0" style="overflow:hidden;height:100vh;width:100%;display:none;" height="100vh" width="100%">Sorry your browser is not supported.</iframe>
    </div>
  </div>
<script>
  $("body").css("overflow", "hidden");

  $('#left-panel').load(function() {
    document.getElementById('left-panel').contentWindow.$(".product").unbind('click').click(function(e){
      e.preventDefault();

      $("#logo-smartfren").hide();
      $("#right-panel").show();
      $("#right-panel").attr("src","{{ URL::route('product_detail') }}"+"?id="+$(this).data("id"));
    });

    (function($) {
      $.extend({
        spin_left: function(spin, opts) {
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

          var data = document.getElementById('left-panel').contentWindow.$('body').data();

          if (data.spinner) {
            data.spinner.stop();
            delete data.spinner;
            document.getElementById('left-panel').contentWindow.$("#spinner_modal").remove();
            return this;
          }

          if (spin=="show") {
            var spinElem = this;

            document.getElementById('left-panel').contentWindow.$('body').append('<div id="spinner_modal" style="background-color: rgba(0, 0, 0, 0.3); width:100%; height:100%; position:fixed; top:0px; left:0px; z-index:' + (opts.zIndex - 1) + '"/>');
            spinElem = document.getElementById('left-panel').contentWindow.$("#spinner_modal")[0];

            data.spinner = new Spinner($.extend({
              color: document.getElementById('left-panel').contentWindow.$('body').css('color')
            }, opts)).spin(spinElem);
          }
        }
      });
    })(jQuery);
  });

  $('#right-panel').load(function() {
    document.getElementById('right-panel').contentWindow.$("#user_form").submit(function(e) {
      e.preventDefault();

      $.spin_left('show');

      document.getElementById('right-panel').contentWindow.$('#modal_view').unbind('hidden.bs.modal').on('hidden.bs.modal',function(){
        $.spin_left('hide');

        $("#right-panel").attr("src","");
        $("#right-panel").hide();
        $("#logo-smartfren").show();
        $("#left-panel").attr("src","{{ URL::route('show_all_product').'?isMicrosite=true' }}");
      });
    });
  });
</script>
</body>
</html>
