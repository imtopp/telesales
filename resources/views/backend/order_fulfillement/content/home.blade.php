@extends('backend\layout\template')

@section('title', 'Home')

@section('sidebar-menu')
  @include('backend\order_fulfillment\layout\sidebar_menu_content')
@endsection

@section('sidebar-footer')
  @include('backend\order_fulfillment\layout\sidebar_footer')
@endsection

@section('content')
<div class="row">
  <div class="col-md-12 col-sm-12 col-xs-12">
    <div class="x_panel">
      <div class="x_title">
        <h2>Home Page</h2>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <h4>Welcome to {{ config('settings.app_name') }} Order Fulfillment Home Page</h4>
        <span>
          Selamat datang di halaman order fulfillment aplikasi {{ config('settings.app_name') }}. halaman order fulfillment ini merupakan bagian dari aplikasi {{ config('settings.app_name') }}
          yang merupakan halaman untuk memproses order customer dari aplikasi {{ config('settings.app_name') }}.
        </span>
      </div>
    </div>
  </div>
</div>
@endsection