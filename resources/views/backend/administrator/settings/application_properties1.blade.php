@extends('backend\layout\template')

@section('title', 'Application Properties')

@section('sidebar-menu')
  @include('backend\administrator\layout\sidebar_menu_settings')
@endsection

@section('page-css-file')
  <link href="{{ URL::asset('assets/vendors/bootstrap-iconpicker/bootstrap-iconpicker/css/bootstrap-iconpicker.min.css') }}" rel="stylesheet">
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
        {!! Form::open(array(null,'class'=>'form','id'=>'user_form')) !!}
        <div class="col-xs-12 col-md-6">
          <div class="form-group">
            {!! Form::label('Application Name') !!}
            {!! Form::text('app_name',config('settings.app_name'),array('required','class'=>'form-control','placeholder'=>'Nama Aplikasi')) !!}
          </div>
          <div class="form-group">
            {!! Form::label('Application Icon') !!}<br/>
            {!! Form::button(null,array('name'=>'fa_icon','class'=>'btn btn-default','data-iconset'=>'fontawesome','data-icon'=>config('settings.fa_icon'),'role'=>'iconpicker')) !!}
          </div>
        </div>
        {!! Form::close() !!}
      </div>
    </div>
  </div>
</div>
@endsection

@section('page-js-file')
<script src="{{ URL::asset('assets/vendors/bootstrap-iconpicker/bootstrap-iconpicker/js/iconset/iconset-fontawesome-4.2.0.min.js') }}"></script>
<script src="{{ URL::asset('assets/vendors/bootstrap-iconpicker/bootstrap-iconpicker/js/bootstrap-iconpicker.min.js') }}"></script>
@endsection

@section('page-js-script')
  <script>
    $(document).ready(function() {
      $("#application_properties").addClass("current-page");
    });
  </script>
@stop
