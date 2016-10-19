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

@section('page-js-script')
  <script>
    $(document).ready(function() {
      $("#application_properties").addClass("current-page");
      $("#properties_form").submit(function(e) {
        e.preventDefault();
        var properties_form = {};
        $("form#properties_form :input").each(function(){
          properties_form[$(this).attr('name')]=$(this).val();
        });
        //properties_form = {"_token":"{{ csrf_token() }}","properties_form":properties_form};
        $.ajax({
          url : '{{URL::route('administrator_settings_application_properties_update')}}',
          type: 'POST',
          dataType: 'JSON',
          data: properties_form,
          success : function(data){
            if(data.success){
              $("#title").html("Pesan");
              $("#message").html(data.message);
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
