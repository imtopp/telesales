@extends('backend\layout\template')

@section('title', 'Application Properties')

@section('sidebar-menu')
  @include('backend\administrator\layout\sidebar_menu_settings')
@endsection

@section('content')
<div class="row">
  <div class="col-md-12 col-sm-12 col-xs-12">
    <div class="x_panel">
      <div class="x_title">
        <h2>Application Properties</h2>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <h4>This is configuration of {{ config('settings.app_name') }} Application Properties Page</h4>
        {!! Form::open(array(null,'class'=>'form','id'=>'properties_form')) !!}
        <div class="col-xs-12 col-md-12">
          <div class="row">
            <div class="col-xs-12 col-md-6">
              <div class="form-group">
                {!! Form::label('Application Name') !!}
                {!! Form::text('app_name',config('settings.app_name'),array('required','class'=>'form-control','placeholder'=>'Nama Aplikasi')) !!}
              </div>
              <div class="form-group">
                {!! Form::label('Digital & IOT Team Email Address') !!}
                {!! Form::text('digital_iot_email',config('settings.digital_iot_email'),array('required','class'=>'form-control','placeholder'=>'Digital & IOT Team Email Address')) !!}
              </div>
            </div>
            <div class="col-xs-12 col-md-6">
            </div>
          </div>
          <div class="row">
            <div class="col-xs-1" style="float:right;">
              <div class="form-group">
                {!! Form::submit('Save',array('class'=>'btn btn-danger','style'=>'width: 100%;')) !!}
              </div>
            </div>
          </div>
        </div>
        {!! Form::close() !!}
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="modal_view" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 id="title" class="modal-title"></h4>
      </div>
      <div class="modal-body">
        <span id="message"></span>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
@endsection

@section('page-js-file')
<!-- spinner -->
<script src="{{ URL::asset('assets/js/spin.min.js') }}"></script>
@endsection

@section('page-js-script')
  <script>
    $(document).ready(function() {
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

      $("#properties_form").submit(function(e) {
        e.preventDefault();
        $.spin("show");
        var properties_form = {};

        $("form#properties_form :input").each(function(){
          properties_form[$(this).attr('name')]=$(this).val();
        });

        $.ajax({
          url : '{{URL::route('administrator_settings_application_properties_update')}}',
          type: 'POST',
          dataType: 'JSON',
          data: properties_form,
          success : function(data){
            if(data.success){
              $("#title").html("Pesan");
              $("#message").html(data.message);

              $.spin("hide");
              $('#modal_view').modal('show');
              $('#modal_view').on('hidden.bs.modal',function(){
                window.location.href = "{{ URL::route('administrator_settings_application_properties') }}";
              });
            }
          }
        });
      });
    });
  </script>
@stop
