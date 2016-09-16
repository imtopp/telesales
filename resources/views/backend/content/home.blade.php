@extends('backend\layout\template')

@section('title', 'Home')

@section('content')
<div class="row">
  <div class="col-md-12 col-sm-12 col-xs-12">
    <div class="x_panel">
      <div class="x_title">
        <h2>Home Page</h2>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <h4>Welcome to Telesales Administrator Home Page</h4>
        <span>Selamat datang di halaman administrator aplikasi Telesales</span>
      </div>
    </div>
  </div>
</div>
@endsection

@section('page-js-script')
  <script>
    $(document).ready(function() {
      $("#home").addClass("current-page");
    });
  </script>
@stop
