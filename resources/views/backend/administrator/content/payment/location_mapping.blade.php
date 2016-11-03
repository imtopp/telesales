@extends('backend\layout\template')

@section('title', 'Payment Method Location Mapping')

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
        <h2>Payment Method Location Mapping Page</h2>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <h4>Welcome to Payment Method Location Mapping Page</h4>
        <span>
          Selamat datang di halaman Payment Method Location Mapping. halaman ini digunakan untuk mengatur lokasi kota pada aplikasi {{ config('settings.app_name') }}
          anda dapat mengatur mapping pada tabel dibawah.
        </span>
        <table id="datatable" class="table table-striped table-bordered">
          <thead>
            <tr>
              <th>Province</th>
              <th>City</th>
              <th>District</th>
              <th>Payment Method</th>
              <th>Status</th>
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
<!-- spinner -->
<script src="{{ URL::asset('assets/js/spin.min.js') }}"></script>
@endsection

@section('page-js-script')
  <script type = "text/template" id="modal-template">
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
        <div class="form-group province">
          <div class="controls">
            {!! Form::label("province_id", "Provinsi") !!}
            {!! Form::select('province_id', [''=>'Harap pilih Provinsi'], null, ["class"=>"form-control","required","id"=>"province_id"]); !!}
          </div>
        </div>
        <div class="form-group city" style="display:none">
          <div class="controls">
            {!! Form::label("city_id", "Kota") !!}
            {!! Form::select('city_id', [''=>'Harap pilih Kota'], null, ["class"=>"form-control","required","id"=>"city_id"]); !!}
          </div>
        </div>
        <div class="form-group district" style="display:none">
          <div class="controls">
            {!! Form::label("district_id", "Kecamatan") !!}
            {!! Form::select('district_id', [''=>'Harap pilih Kecamatan'], null, ["class"=>"form-control","required","id"=>"district_id"]); !!}
          </div>
        </div>
        <div class="form-group payment_method" style="display:none">
          <div class="controls">
            {!! Form::label("payment_method_id", "Metode Pembayaran") !!}
            {!! Form::select('payment_method_id', [''=>'Harap pilih Payment Method'], null, ["class"=>"form-control","required","id"=>"payment_method_id"]); !!}
          </div>
        </div>
        <div class="form-group">
          <div class="controls">
            {!! Form::label("status", "Status") !!}
            {!! Form::select('status', array('active' => 'Active','inactive' => 'Inactive'), 'active', ["class"=>"form-control","required","id"=>"status"]); !!}
          </div>
        </div>
      </div>
    </div>
    <div id="footer" class="modal-footer">
      <div class="form-group">
        <div class="controls">
          {!!Form::submit("Save",["class"=>"btn btn-primary submit"])!!}
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
    {!!Form::close()!!}
  </script>

  <script type = "text/template" id="modal-template-delete">
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
        <h4>Apakah anda yakin menghapus <span id="name"></span> ?</h4>
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
        dom: 'Bfrtipl',
        "processing": true,
        "serverSide": true,
        buttons: [{
          text: 'Tambah Baru',
          action: create,
        }],
        ajax: {
          data: {"_token":"{{ csrf_token() }}"},
          url: "{{URL::route('administrator_manage_payment_method_location_mapping_read')}}",
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
          "data": "province",
          "title": "Provinsi",
          "width": "160px"
        },{
          "data": "city",
          "title": "Kota",
          "width": "160px"
        },{
          "data": "district",
          "title": "Kecamatan",
          "width": "160px"
        },{
          "data": "payment_method",
          "title": "Metode Pembayaran",
          "width": "160px"
        },{
          "data": "status",
          "title": "Status",
          "width": "100px"
        },{
          "data": "action",
          "title": "Action",
          "width": "150px"
        }],
        deferRender: true,
     });
    });

    function initializeModal(mode,element,title,id,province_id,city_id,district_id,payment_method_id,status){
      $.spin("show");
      $("#modal-content").html(element);
      $("#title").html(title);

      var data = {"_token":"{{ csrf_token() }}"};

      if(mode == "update" || mode == "delete"){
        $("#id").val(id);

        if(mode == "update"){
          $("#payment_method_id").val(payment_method_id);
          $("#status").val(status);

          data.province_id = province_id;
          data.city_id = city_id;
          data.district_id = district_id;
          data.payment_method_id = payment_method_id;
        }else if(mode == "delete"){
          $("#name").html(province_id);

          $.spin("hide");
          $('#modal_view').modal('show');
          $('#modal_view').on('hidden.bs.modal',function(){
            $("#modal-content").html("");
          });
        }
      }

      if(mode == "create" || mode == "update"){
        $(function(){
          $.ajax({
            url : '{{URL::route('administrator_manage_payment_method_location_mapping_get_province')}}',
            type: 'POST',
            dataType: 'JSON',
            data: data,
            success : function(data){
              $.each(data,function(key,value){
                $("#province_id").append('<option value="'+key+'">'+value+'</option>');
              });
              if(mode == "update"){
                $("#province_id").val(province_id);
                $("#province_id").change();
              }else if(mode == "create"){
                $.spin("hide");
                $('#modal_view').modal('show');
                $('#modal_view').on('hidden.bs.modal',function(){
                  $("#modal-content").html("");
                });
              }
            }
          });
        });

        $("#province_id").change(function(){
          if($("#province_id").val()!=""){
            data.province_id = $("#province_id").val();

            $.ajax({
              url : '{{URL::route('administrator_manage_payment_method_location_mapping_get_city')}}',
              type: 'POST',
              dataType: 'JSON',
              data: data,
              success : function(data){
                $("#city_id").empty();
                $("#city_id").append('<option value="">Harap Pilih Kota</option>');

                $.each(data,function(key,value){
                  $("#city_id").append('<option value="'+key+'">'+value+'</option>');
                });

                if(mode == "update" && $("#province_id").val() == province_id){
                  $("#city_id").val(city_id);
                  $("#city_id").change();
                }

                $(".city").show();
                $(".district").hide();
                $(".payment_method").hide();
                $("#district_id").empty();
                $("#district_id").append('<option value="">Harap Pilih Kecamatan</option>');
                $("#payment_method_id").empty();
                $("#payment_method_id").append('<option value="">Silahkan Pilih Payment Method</option>');
              }
            });
          }else{
            $(".city").hide();
            $(".district").hide();
            $(".payment_method").hide();
            $("#city_id").empty();
            $("#city_id").append('<option value="">Harap Pilih Kota</option>');
            $("#district_id").empty();
            $("#district_id").append('<option value="">Harap Pilih Kecamatan</option>');
            $("#payment_method_id").empty();
            $("#payment_method_id").append('<option value="">Silahkan Pilih Payment Method</option>');
          }
        });

        $("#city_id").change(function(){
          if($("#city_id").val()!=""){
            data.city_id = $("#city_id").val();

            $.ajax({
              url : '{{URL::route('administrator_manage_payment_method_location_mapping_get_district')}}',
              type: 'POST',
              dataType: 'JSON',
              data: data,
              success : function(data){
                $("#district_id").empty();
                $("#district_id").append('<option value="">Silahkan Pilih Kecamatan</option>');

                $.each(data,function(key,value){
                  $("#district_id").append('<option value="'+key+'">'+value+'</option>');
                });

                if(mode == "update" && $("#city_id").val() == city_id){
                  $("#district_id").val(district_id);
                  $("#district_id").change();
                }

                if(mode == "update"){
                  $.spin("hide");
                  $('#modal_view').modal('show');
                  $('#modal_view').on('hidden.bs.modal',function(){
                    $("#modal-content").html("");
                  });
                }

                $(".district").show();
                $(".payment_method").hide();
                $("#payment_method_id").empty();
                $("#payment_method_id").append('<option value="">Silahkan Pilih Payment Method</option>');
              }
            });
          }else{
            $(".district").hide();
            $(".payment_method").hide();
            $("#district").empty();
            $("#district").append('<option value="">Silahkan Pilih Kecamatan</option>');
            $("#payment_method_id").empty();
            $("#payment_method_id").append('<option value="">Silahkan Pilih Payment Method</option>');
          }
        });

        $("#district_id").change(function(){
          if($("#district_id").val()!=""){
            data.district_id = $("#district_id").val();

            $.ajax({
              url : '{{URL::route('administrator_manage_payment_method_location_mapping_get_payment_method')}}',
              type: 'POST',
              dataType: 'JSON',
              data: data,
              success : function(data){
                $("#payment_method_id").empty();
                $("#payment_method_id").append('<option value="">Silahkan Pilih Payment Method</option>');

                $.each(data,function(key,value){
                  $("#payment_method_id").append('<option value="'+key+'">'+value+'</option>');
                });

                if(mode == "update" && $("#district_id").val() == district_id){
                  $("#payment_method_id").val(district_id);
                }

                if(mode == "update"){
                  $.spin("hide");
                  $('#modal_view').modal('show');
                  $('#modal_view').on('hidden.bs.modal',function(){
                    $("#modal-content").html("");
                  });
                }

                $(".payment_method").show();
              }
            });
          }else{
            $(".payment_method").hide();
            $("#payment_method_id").empty();
            $("#payment_method_id").append('<option value="">Silahkan Pilih Payment Method</option>');
          }
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

    function create(){
      initializeModal("create",$("#modal-template").html(),"Tambah Data Baru");

      setSubmitModalEvent('{{URL::route('administrator_manage_payment_method_location_mapping_create')}}');
    }

    function edit(e) {
      initializeModal("update",$("#modal-template").html(),"Edit Data",$(e).data('id'),$(e).data('province_id'),$(e).data('city_id'),$(e).data('district_id'),$(e).data('payment_method_id'),$(e).data('status'));

      setSubmitModalEvent('{{URL::route('administrator_manage_payment_method_location_mapping_update')}}');
    }

    function destroy(e) {
      initializeModal("delete",$("#modal-template-delete").html(),"Delete Data",$(e).data('id'),$(e).data('name'));

      setSubmitModalEvent('{{URL::route('administrator_manage_payment_method_location_mapping_destroy')}}');
    }
  </script>
@stop
