@extends('backend\layout\template')

@section('title', 'Manage Order')

@section('sidebar-menu')
  @include('backend\order_fulfillment\layout\sidebar_menu_content')
@endsection

@section('sidebar-footer')
  @include('backend\order_fulfillment\layout\sidebar_footer')
@endsection

@section('page-css-file')
<!-- datatables bootstrap -->
<link href="{{ URL::asset('assets/vendors/datatables.net-bs/css/dataTables.bootstrap.min.css') }}" rel="stylesheet">
<!-- datatables bootstrap buttons -->
<link href="{{ URL::asset('assets/vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="row">
  <div class="col-md-12 col-sm-12 col-xs-12">
    <div class="x_panel">
      <div class="x_title">
        <h2>Manage Order Page</h2>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <h4>Welcome to Manage Order Page</h4>
        <span>
          Selamat datang di halaman manage order. halaman ini digunakan untuk memasukan pesanan customer pada aplikasi {{ config('settings.app_name') }}
          anda dapat mengatur pesanan pada tabel dibawah.
        </span>
        <table id="datatable" class="table table-striped table-bordered">
          <thead>
            <tr>
              <th>Refference Number</th>
              <th>Channel</th>
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
              <th>Status</th>
              <th>Payment Number</th>
              <th>airwaybill</th>
              <th class="text-center"> Action </th>
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
<!-- bootsrap-select -->
<script src="{{ URL::asset('assets/vendors/bootstrapselect/js/bootstrap-select.js') }}"></script>
<!-- spinner -->
<script src="{{ URL::asset('assets/js/spin.min.js') }}"></script>
@endsection

@section('page-js-script')
  <script type = "text/template" id="modal-template-deliver-order">
    {!!Form::open(["id"=>"popup_form","class"=>"form form-horizontal"])!!}
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal">&times;</button>
      <h4 id="title" class="modal-title"></h4>
    </div>
    <div class="modal-body">
      <div id="message">
        <div class="form-group">
          {!! Form::hidden('id',null,['id'=>'id']) !!}
        </div>
        <h5>Input AirWayBill Number for order number <span id="refference_number"></span>.</h5>
        <div class="form-group">
          <div class="controls">
            {!! Form::label("airwaybill", "AirWayBill Number") !!}
            {!! Form::text("airwaybill",null,["class"=>"form-control","required","id"=>"airwaybill"]) !!}
          </div>
        </div>
      </div>
    </div>
    <div id="footer" class="modal-footer">
      <div class="form-group">
        <div class="controls">
          {!!Form::submit("Ya",["class"=>"btn btn-primary submit"])!!}
          <button type="button" class="btn btn-default" data-dismiss="modal">Tidak</button>
        </div>
      </div>
    </div>
    {!!Form::close()!!}
  </script>

  <script type = "text/template" id="modal-template-payment-received">
    {!!Form::open(["id"=>"popup_form","class"=>"form form-horizontal"])!!}
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal">&times;</button>
      <h4 id="title" class="modal-title"></h4>
    </div>
    <div class="modal-body">
      <div id="message">
        <div class="form-group">
          {!! Form::hidden('id',null,['id'=>'id']) !!}
        </div>
        <h5>Apakah anda yakin pesanan <span id="refference_number"></span> telah dibayar?</h5>
      </div>
    </div>
    <div id="footer" class="modal-footer">
      <div class="form-group">
        <div class="controls">
          {!!Form::submit("Ya",["class"=>"btn btn-primary submit"])!!}
          <button type="button" class="btn btn-default" data-dismiss="modal">Tidak</button>
        </div>
      </div>
    </div>
    {!!Form::close()!!}
  </script>

  <script type = "text/template" id="modal-template-cancel-order">
    {!!Form::open(["id"=>"popup_form","class"=>"form form-horizontal"])!!}
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal">&times;</button>
      <h4 id="title" class="modal-title"></h4>
    </div>
    <div class="modal-body">
      <div id="message">
        <div class="form-group">
          {!! Form::hidden('id',null,['id'=>'id']) !!}
        </div>
        <h5>Apakah anda yakin membatalkan pesanan <span id="refference_number"></span> ?</h5>
      </div>
    </div>
    <div id="footer" class="modal-footer">
      <div class="form-group">
        <div class="controls">
          {!!Form::submit("Ya",["class"=>"btn btn-primary submit"])!!}
          <button type="button" class="btn btn-default" data-dismiss="modal">Tidak</button>
        </div>
      </div>
    </div>
    {!!Form::close()!!}
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
          url: "{{URL::route('order_fulfillment_manage_order_read')}}",
          type: "POST",
          error: function(){  // error handling
            $(".lookup-error").html("");
            $("#lookup").append('<tbody class="employee-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
            $("#lookup_processing").css("display","none");
          }
        },
        "order": [[ 0, "desc" ]],
        pageLength : 10,
        "scrollX": true,
        "columns": [{
          "data": "refference_number",
          "title": "Refference Number"
        },{
          "data": "channel",
          "title": "Channel",
          "width": "100px"
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
          "data": "status",
          "title": "Status",
          "width": "100px"
        },{
          "data": "payment_number",
          "title": "Payment Number",
          "width": "100px"
        },{
          "data": "airwaybill",
          "title": "Airwaybill",
          "width": "100px"
        },{
          "data": "action",
          "title": "Action",
          "width": "100px"
        }],
        deferRender: true,
     });
    });

    function isNumberKey(event){
      var charCode = (event.which) ? event.which : event.keyCode;
      if (charCode != 43 && charCode > 31 && (charCode < 48 || charCode > 57))
        return false;
      return true;
    }

    function initializeModal(mode,element,title,id,refference_number){
      $.spin("show");
      $("#modal-content").html(element);
      $("#title").html(title);

      if(mode == "delete"){
        $("#id").val(id);
        $("#refference_number").html(refference_number);

        $.spin("hide");
        $('#modal_view').modal('show');
        $('#modal_view').on('hidden.bs.modal',function(){
          $("#modal-content").html("");
        });
      }

      if(mode == "update"){
        $("#id").val(id);
        $("#refference_number").html(refference_number);

        $.spin("hide");
        $('#modal_view').modal('show');
        $('#modal_view').on('hidden.bs.modal',function(){
          $("#modal-content").html("");
        });
      }
    }

    function getModalFormData(){
      var popup_form = {};
      $("form#popup_form :input").each(function(){
        popup_form[$(this).attr('name')]=$(this).val();
      });
      return popup_form;
    }

    function setSubmitModalEvent(url){
      $("#popup_form").submit(function(e) {
        e.preventDefault();

        $.spin("show");
        $.ajax({
          url : url,
          type: 'POST',
          dataType: 'JSON',
          data: getModalFormData(),
          success : function(data){
            $("#title").html("Pesan");
            $("#message").html(data.message);
            $("#footer").html('<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>');

            $.spin("hide");
            $('#modal_view').modal('show');
            $('#modal_view').on('hidden.bs.modal',function(){
              $("#modal-content").html("");
            });

            table.fnReloadAjax();
          }
        });
      });
    }

    function paymentReceived(e) {
      initializeModal("delete",$("#modal-template-payment-received").html(),"Payment Received",$(e).data('id'),$(e).data('refference_number'));

      setSubmitModalEvent('{{URL::route('order_fulfillment_manage_order_payment_received')}}');
    }

    function cancel(e) {
      initializeModal("delete",$("#modal-template-cancel-order").html(),"Cancel Order",$(e).data('id'),$(e).data('refference_number'));

      setSubmitModalEvent('{{URL::route('order_fulfillment_manage_order_cancel_order')}}');
    }

    function deliver(e) {
      initializeModal("update",$("#modal-template-deliver-order").html(),"Deliver Order",$(e).data('id'),$(e).data('refference_number'));

      setSubmitModalEvent('{{URL::route('order_fulfillment_manage_order_deliver_order')}}');
    }
  </script>
@stop
