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
              <th>Refference Number</th>
              <th>Customer Name</th>
              <th>Customer Address</th>
              <th>Customer Identity Type</th>
              <th>Customer Identity Number</th>
              <th>Customer Email</th>
              <th>Customer MDN</th>
              <th>Customer Location Province</th>
              <th>Customer Location City</th>
              <th>Customer Location Distict</th>
              <th>Customer Delivery Address</th>
              <th>Product Category</th>
              <th>Product Name</th>
              <th>Product Colour</th>
              <th>Product FG Code</th>
              <th>Product Price</th>
              <th>Payment Method</th>
              <th>Courier</th>
              <th>Courier Package</th>
              <th>Delivery Price</th>
              <th>Total Price</th>
              <th>Recent Status</th>
              <th>Created Date</th>
              <th>Created By</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="modal_view" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content" id="modal-content">
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
<!-- spinner -->
<script src="{{ URL::asset('assets/js/spin.min.js') }}"></script>
@endsection

@section('page-js-script')
  <script type = "text/template" id="modal-template">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal">&times;</button>
      <h4 id="title" class="modal-title"></h4>
    </div>
    <div class="modal-body">
      <div id="message">
        <table id="statustable" class="table table-striped table-bordered">
          <thead>
            <tr>
              <th>Status</th>
              <th>Date</th>
              <th>Updated By</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
    <div id="footer" class="modal-footer">
      <div class="form-group">
        <div class="controls">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </script>

  <script>
    var table;
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

      table = $('#datatable').dataTable({
        dom: 'frtipl',
        "processing": true,
        "serverSide": true,
        ajax: {
          data: {"_token":"{{ csrf_token() }}"},
          url: "{{URL::route('administrator_transaction_list_read')}}",
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
          "data": "refference_number",
          "title": "Refference Number"
        },{
          "data": "customer_name",
          "title": "Customer Name",
          "width": "160px"
        },{
          "data": "customer_address",
          "title": "Customer Address",
          "width": "160px"
        },{
          "data": "customer_identity_type",
          "title": "Customer Identity Type"
        },{
          "data": "customer_identity_number",
          "title": "Customer Identity Number",
          "width": "100px"
        },{
          "data": "customer_email",
          "title": "Customer Email"
        },{
          "data": "customer_mdn",
          "title": "Customer MDN"
        },{
          "data": "customer_location_province",
          "title": "Customer Location Province"
        },{
          "data": "customer_location_city",
          "title": "Customer Location City"
        },{
          "data": "customer_location_district",
          "title": "Customer Location District"
        },{
          "data": "customer_delivery_address",
          "title": "Customer Delivery Address",
          "width": "160px"
        },{
          "data": "product_category",
          "title": "Product Category"
        },{
          "data": "product_name",
          "title": "Product Name",
          "width": "100px"
        },{
          "data": "product_colour",
          "title": "Product Colour"
        },{
          "data": "product_fg_code",
          "title": "Product FG Code"
        },{
          "data": "product_price",
          "title": "Product Price",
          "width": "100px"
        },{
          "data": "payment_method",
          "title": "Payment Method"
        },{
          "data": "courier",
          "title": "Courier"
        },{
          "data": "courier_package",
          "title": "Courier Package"
        },{
          "data": "delivery_price",
          "title": "Delivery Price",
          "width": "100px"
        },{
          "data": "total_price",
          "title": "Total Price",
          "width": "100px"
        },{
          "data": "recent_status",
          "title": "Recent Status",
          "width": "100px"
        },{
          "data": "created_date",
          "title": "Created Date",
          "width": "100px"
        },{
          "data": "created_by",
          "title": "Created By",
          "width": "100px"
        }],
        deferRender: true,
     });
    });

    function initializeModal(mode,element,title,id){
      $.spin("show");
      $("#modal-content").html(element);
      $("#title").html(title);

      if(mode == "read"){
        $('#statustable').dataTable({
          dom: 'rt',
          "processing": true,
          "serverSide": true,
          ajax: {
            data: {"_token":"{{ csrf_token() }}","id":id},
            url: "{{URL::route('administrator_transaction_list_read_status')}}",
            type: "POST",
            error: function(){  // error handling
              $(".lookup-error").html("");
              $("#lookup").append('<tbody class="employee-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
              $("#lookup_processing").css("display","none");
            }
          },
          "columns": [{
            "data": "status",
            "title": "Status",
            "width": "200px"
          },{
            "data": "date",
            "title": "Date",
            "width": "100px"
          },{
            "data": "updated_by",
            "title": "Updated By",
            "width": "200px"
          }],
          deferRender: true,
       });
      }

      $.spin("hide");
      $('#modal_view').modal('show');
      $('#modal_view').on('hidden.bs.modal',function(){
        $("#modal-content").html("");
      });
    }

    function detailStatus(e){
      initializeModal("read",$("#modal-template").html(),"Detail Status",$(e).data('id'));
    }
  </script>
@stop
