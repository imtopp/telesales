@extends('backend\layout\template')

@section('title', 'Courier Location Mapping')

@section('sidebar-menu')
  @include('backend\layout\sidebar_menu_content')
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
        <h2>Courier Location Mapping Page</h2>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <h4>Welcome to Courier Location Mapping Page</h4>
        <span>
          Selamat datang di halaman Courier Location Mapping. halaman ini digunakan untuk mengatur lokasi kota pada aplikasi {{ config('settings.app_name') }}
          anda dapat mengatur mapping pada tabel dibawah.
        </span>
        <table id="datatable" class="table table-striped table-bordered">
          <thead>
            <tr>
              <th>Province</th>
              <th>City</th>
              <th>District</th>
              <th>Kurir</th>
              <th>Paket Pengiriman</th>
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
        <div class="form-group">
          <div class="controls">
            {!! Form::label("courier_id", "Kurir") !!}
            {!! Form::select('courier_id', [''=>'Harap Pilih Kurir'], null, ["class"=>"form-control","required","id"=>"courier_id"]); !!}
          </div>
        </div>
        <div class="form-group courier_package" style="display:none">
          <div class="controls">
            {!! Form::label("courier_package_id", "Paket Pengiriman") !!}
            {!! Form::select('courier_package_id', [''=>'Harap Pilih Paket Pengiriman'], null, ["class"=>"form-control","required","id"=>"courier_package_id"]); !!}
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
        <h4>Apakah anda yakin menghapus? <span id="name"></span></h4>
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
          url: "{{URL::route('backend_manage_courier_location_mapping_read')}}",
          type: "POST",
          error: function(){  // error handling
            $(".lookup-error").html("");
            $("#lookup").append('<tbody class="employee-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
            $("#lookup_processing").css("display","none");
          }
        },
        pageLength : 10,
        "columns": [{
          "data": "province",
          "title": "Provinsi"
        },{
          "data": "city",
          "title": "Kota"
        },{
          "data": "district",
          "title": "Kecamatan"
        },{
          "data": "courier",
          "title": "Kurir"
        },{
          "data": "courier_package",
          "title": "Paket Pengiriman"
        },{
          "data": "status",
          "title": "Status"
        },{
          "data": "action",
          "title": "Action"
        }],
        deferRender: true,
     });
    });

    function initializeModal(element,title,id,province_id,city_id,district_id,courier_id,courier_package_id,status){
      $("#modal-content").html(element);
      $("#title").html(title);

      if(typeof id != 'undefined'){
        $("#id").val(id);
      }

      if($("#province_id").is("select")){
        $(function(){
          $.ajax({
            url : '{{URL::route('backend_manage_courier_location_mapping_get_province')}}',
            type: 'POST',
            dataType: 'JSON',
            data: {"_token":"{{ csrf_token() }}"},
            success : function(data){
              $.each(data,function(key,value){
                $("#province_id").append('<option value="'+key+'">'+value+'</option>');
              });
              if(typeof province_id != 'undefined'){
                $("#province_id").val(province_id);
                $("#province_id").change();
              }
            }
          });
        });

        $("#province_id").change(function(){
          if($("#province_id").val()!=""){
            $.ajax({
              url : '{{URL::route('backend_manage_courier_location_mapping_get_city')}}',
              type: 'POST',
              dataType: 'JSON',
              data: {"_token":"{{ csrf_token() }}","province_id":$("#province_id").val()},
              success : function(data){
                $("#city_id").empty();
                $("#city_id").append('<option value="">Harap Pilih Kota</option>');

                $.each(data,function(key,value){
                  $("#city_id").append('<option value="'+key+'">'+value+'</option>');
                });

                if(typeof city_id != 'undefined'){
                  $("#city_id").val(city_id);
                  $("#city_id").change();
                }

                $(".city").show();
                $(".district").hide();
                $("#district_id").empty();
                $("#district_id").append('<option value="">Harap Pilih Kecamatan</option>');
              }
            });
          }else{
            $(".city").hide();
            $(".district").hide();
            $("#city_id").empty();
            $("#city_id").append('<option value="">Harap Pilih Kota</option>');
            $("#district_id").empty();
            $("#district_id").append('<option value="">Harap Pilih Kecamatan</option>');
          }
        });

        $("#city_id").change(function(){
          if($("#city_id").val()!=""){
            $.ajax({
              url : '{{URL::route('backend_manage_courier_location_mapping_get_district')}}',
              type: 'POST',
              dataType: 'JSON',
              data: {"_token":"{{ csrf_token() }}","city_id":$("#city_id").val()},
              success : function(data){
                $("#district_id").empty();
                $("#district_id").append('<option value="">Silahkan Pilih Kecamatan</option>');

                $.each(data,function(key,value){
                  $("#district_id").append('<option value="'+key+'">'+value+'</option>');
                });

                if(typeof district_id != 'undefined'){
                  $("#district_id").val(district_id);
                }

                $(".district").show();
              }
            });
          }else{
            $(".district").hide();
            $("#district").empty();
            $("#district").append('<option value="">Silahkan Pilih Kecamatan</option>');
          }
        });
      }else{
        if(typeof province_id != 'undefined'){
          $("#name").html(province_id);
        }
      }

      if($("#courier_id").is("select")){
        $(function(){
          $.ajax({
            url : '{{URL::route('backend_manage_courier_location_mapping_get_courier')}}',
            type: 'POST',
            dataType: 'JSON',
            data: {"_token":"{{ csrf_token() }}"},
            success : function(data){
              $("#courier_id").empty();
              $("#courier_id").append('<option value="">Harap Pilih Kurir</option>');

              $.each(data,function(key,value){
                $("#courier_id").append('<option value="'+key+'">'+value+'</option>');
              });
              if(typeof courier_id != 'undefined'){
                $("#courier_id").val(courier_id);
                $("#courier_id").change();
              }
            }
          });
        });

        $("#courier_id").change(function(){
          if($("#courier_id").val()!=""){
            $.ajax({
              url : '{{URL::route('backend_manage_courier_location_mapping_get_courier_package')}}',
              type: 'POST',
              dataType: 'JSON',
              data: {"_token":"{{ csrf_token() }}","courier_id":$("#courier_id").val()},
              success : function(data){
                $("#courier_package_id").empty();
                $("#courier_package_id").append('<option value="">Harap Pilih Paket Pengiriman</option>');

                $.each(data,function(key,value){
                  $("#courier_package_id").append('<option value="'+key+'">'+value+'</option>');
                });

                if(typeof courier_package_id != 'undefined'){
                  $("#courier_package_id").val(courier_package_id);
                  $("#courier_package_id").change();
                }

                $(".courier_package").show();
              }
            });
          }else{
            $(".courier_package").hide();
            $("#courier_package_id").empty();
            $("#courier_package_id").append('<option value="">Harap Pilih Paket Pengiriman</option>');
          }
        });
      }

      if(typeof courier_id != 'undefined'){
        $("#courier_id").val(courier_id);
      }

      if(typeof status != 'undefined'){
        $("#status").val(status);
      }
    }

    function showModal(){
      $('#modal_view').modal('show');
    }

    function resetModal(){
      $("#modal-content").html("");
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

        $.ajax({
          url : url,
          type: 'POST',
          dataType: 'JSON',
          data: getModalFormData(),
          success : function(data){
            $("#title").html("Pesan");
            $("#message").html(data.message);
            $("#footer").html('<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>');
            showModal();
            $('#modal_view').on('hidden.bs.modal',function(){
              resetModal();
            });
            table.fnReloadAjax();
          }
        });
      });
    }

    function create(){
      initializeModal($("#modal-template").html(),"Tambah Data Baru");
      showModal();
      $('#modal_view').on('hidden.bs.modal',function(){
        resetModal();
      });

      setSubmitModalEvent('{{URL::route('backend_manage_courier_location_mapping_create')}}');
    }

    function edit(e) {
      initializeModal($("#modal-template").html(),"Edit Data",$(e).data('id'),$(e).data('province_id'),$(e).data('city_id'),$(e).data('district_id'),$(e).data('courier_id'),$(e).data('courier_package_id'),$(e).data('status'));
      showModal();
      $('#modal_view').on('hidden.bs.modal',function(){
        resetModal();
      });

      setSubmitModalEvent('{{URL::route('backend_manage_courier_location_mapping_update')}}');
    }

    function destroy(e) {
      initializeModal($("#modal-template-delete").html(),"Delete Data",$(e).data('id'),$(e).data('name'));
      showModal();
      $('#modal_view').on('hidden.bs.modal',function(){
        resetModal();
      });

      setSubmitModalEvent('{{URL::route('backend_manage_courier_location_mapping_destroy')}}');
    }
  </script>
@stop