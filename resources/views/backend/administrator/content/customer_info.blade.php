@extends('backend\layout\template')

@section('title', 'Customer Info')

@section('sidebar-menu')
  @include('backend\administrator\layout\sidebar_menu_content')
@endsection

@section('sidebar-footer')
  @include('backend\administrator\layout\sidebar_footer')
@endsection

@section('page-css-file')
<!-- datatables bootstrap -->
<link href="{{ URL::asset('assets/vendors/datatables.net-bs/css/dataTables.bootstrap.min.css') }}" rel="stylesheet">
<!-- datatables bootstrap buttons-->
<link href="{{ URL::asset('assets/vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="row">
  <div class="col-md-12 col-sm-12 col-xs-12">
    <div class="x_panel">
      <div class="x_title">
        <h2>Customer Info Page</h2>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <h4>Welcome to Customer Info Page</h4>
        <span>
          Selamat datang di halaman customer info. halaman ini digunakan untuk melihat info customer pada aplikasi {{ config('settings.app_name') }}
          anda dapat melihat customer info pada tabel dibawah.
        </span>
        <table id="datatable" class="table table-striped table-bordered">
          <thead>
            <tr>
              <th>Nama</th>
              <th>Address</th>
              <th>Identity Type</th>
              <th>Identity Number</th>
              <th>Email</th>
              <th>MDN</th>
              <th>Location Province</th>
              <th>Location City</th>
              <th>Location District</th>
              <th>Delivery Address</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection

@section('page-js-file')
<!-- datatables jquery -->
<script src="{{ URL::asset('assets/vendors/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<!-- datatables plugin fnReloadAjax -->
<script src="{{ URL::asset('assets/vendors/datatables.net/plugin/fnReloadAjax.js') }}"></script>
<!-- datatables boostrap -->
<script src="{{ URL::asset('assets/vendors/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
<!-- datatables buttons -->
<script src="{{ URL::asset('assets/vendors/datatables.net-buttons/js/dataTables.buttons.min.js') }}"></script>
<!-- datatables boostrap buttons -->
<script src="{{ URL::asset('assets/vendors/datatables.net-buttons-bs/js/buttons.bootstrap.min.js') }}"></script>
@endsection

@section('page-js-script')
  <script>
    var table;
    $(document).ready(function() {
      table = $('#datatable').dataTable({
        dom: 'frtipl',
        "processing": true,
        "serverSide": true,
        ajax: {
          data: {"_token":"{{ csrf_token() }}"},
          url: "{{URL::route('administrator_customer_info_read')}}",
          type: "POST",
          error: function(){  // error handling
            $(".lookup-error").html("");
            $("#lookup").append('<tbody class="employee-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
            $("#lookup_processing").css("display","none");
          }
        },
        pageLength : 10,
        "scrollX": true,
        "columns": [{
          "data": "name",
          "title": "Identity Name",
          "width": "200px"
        },{
          "data": "address",
          "title": "Identity Address",
          "width": "300px"
        },{
          "data": "identity_type",
          "title": "Identity Type",
          "width": "80px"
        },{
          "data": "identity_number",
          "title": "Identity Number",
          "width": "150px"
        },{
          "data": "email",
          "title": "Email",
          "width": "180px"
        },{
          "data": "mdn",
          "title": "Contact Number",
          "width": "150px"
        },{
          "data": "location_province",
          "title": "Delivery Province",
          "width": "150px"
        },{
          "data": "location_city",
          "title": "Delivery City",
          "width": "150px"
        },{
          "data": "location_district",
          "title": "Delivery District",
          "width": "150px"
        },{
          "data": "delivery_address",
          "title": "Delivery Address",
          "width": "300px"
        }],
        deferRender: true,
     });
    });
  </script>
@stop
