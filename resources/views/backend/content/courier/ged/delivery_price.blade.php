@extends('backend\layout\template')

@section('title', 'GED Courier Delivery Price')

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
        <h2>GED Courier Delivery Price</h2>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <h4>Welcome to GED Courier Delivery Price Page</h4>
        <span>
          Selamat datang di halaman GED Courier Delivery Price. halaman ini digunakan untuk mengatur delivery price untuk kurir GED pada aplikasi {{ config('settings.app_name') }}
          anda dapat mengatur delivery price pada tabel dibawah.
        </span>
        <table id="datatable" class="table table-striped table-bordered">
          <thead>
            <tr>
              <th>Province</th>
              <th>City</th>
              <th>District</th>
              <th>Paket Pengiriman</th>
              <th>Kategori Harga</th>
              <th>Price</th>
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
        <div class="form-group courier_package" style="display:none">
          <div class="controls">
            {!! Form::label("courier_package_id", "Paket Pengiriman") !!}
            {!! Form::select('courier_package_id', [''=>'Harap pilih Paket Pengiriman'], null, ["class"=>"form-control","required","id"=>"courier_package_id"]); !!}
          </div>
        </div>
        <div class="form-group price_category" style="display:none">
          <div class="controls">
            {!! Form::label("price_category_id", "Kategori Harga") !!}
            {!! Form::select('price_category_id', [''=>'Harap pilih Kategori Harga'], null, ["class"=>"form-control","required","id"=>"price_category_id"]); !!}
          </div>
        </div>
        <div class="form-group">
          <div class="controls">
            {!! Form::label("price", "Price") !!}
            {!! Form::input('number',"price",1,["class"=>"form-control","required","id"=>"price",'min'=>1]) !!}
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
          url: "{{URL::route('backend_manage_courier_ged_delivery_price_read')}}",
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
          "data": "courier_package",
          "title": "Paket Pengiriman"
        },{
          "data": "price_category",
          "title": "Kategori Harga"
        },{
          "data": "price",
          "title": "Biaya Kirim"
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

    function initializeModal(element,title,id,province_id,city_id,district_id,courier_package_id,price_category_id,price,status){
      $("#modal-content").html(element);
      $("#title").html(title);

      if(typeof id != 'undefined'){
        $("#id").val(id);
      }

      if($("#province_id").is("select")){
        $(function(){
          var data = {"_token":"{{ csrf_token() }}"};
          if(typeof province_id != 'undefined'){
            data.province_id = province_id;
          }

          $.ajax({
            url : '{{URL::route('backend_manage_courier_ged_delivery_price_get_province')}}',
            type: 'POST',
            dataType: 'JSON',
            data: data,
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
            var data = {"_token":"{{ csrf_token() }}","province_id":$("#province_id").val()};
            if(typeof city_id != 'undefined'){
              data.city_id = city_id;
            }

            $.ajax({
              url : '{{URL::route('backend_manage_courier_ged_delivery_price_get_city')}}',
              type: 'POST',
              dataType: 'JSON',
              data: data,
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
                $(".courier_package").hide();
                $("#courier_package_id").empty();
                $("#courier_package_id").append('<option value="">Silahkan Pilih Paket Pengiriman</option>');
                $(".price_category").hide();
                $("#price_category_id").empty();
                $("#price_category_id").append('<option value="">Silahkan Pilih Kategori Harga</option>');
              }
            });
          }else{
            $(".city").hide();
            $("#city_id").empty();
            $("#city_id").append('<option value="">Harap Pilih Kota</option>');
            $(".district").hide();
            $("#district_id").empty();
            $("#district_id").append('<option value="">Harap Pilih Kecamatan</option>');
            $(".courier_package").hide();
            $("#courier_package_id").empty();
            $("#courier_package_id").append('<option value="">Silahkan Pilih Paket Pengiriman</option>');
            $(".price_category").hide();
            $("#price_category_id").empty();
            $("#price_category_id").append('<option value="">Silahkan Pilih Kategori Harga</option>');
          }
        });

        $("#city_id").change(function(){
          if($("#city_id").val()!=""){
            var data = {"_token":"{{ csrf_token() }}","city_id":$("#city_id").val()};
            if(typeof district_id != 'undefined'){
              data.district_id = district_id;
            }

            $.ajax({
              url : '{{URL::route('backend_manage_courier_ged_delivery_price_get_district')}}',
              type: 'POST',
              dataType: 'JSON',
              data: data,
              success : function(data){
                $("#district_id").empty();
                $("#district_id").append('<option value="">Silahkan Pilih Kecamatan</option>');

                $.each(data,function(key,value){
                  $("#district_id").append('<option value="'+key+'">'+value+'</option>');
                });

                if(typeof district_id != 'undefined'){
                  $("#district_id").val(district_id);
                  $("#district_id").change();
                }

                $(".district").show();
                $(".courier_package").hide();
                $("#courier_package_id").empty();
                $("#courier_package_id").append('<option value="">Silahkan Pilih Paket Pengiriman</option>');
                $(".price_category").hide();
                $("#price_category_id").empty();
                $("#price_category_id").append('<option value="">Silahkan Pilih Kategori Harga</option>');
              }
            });
          }else{
            $(".district").hide();
            $("#district").empty();
            $("#district").append('<option value="">Silahkan Pilih Kecamatan</option>');
            $(".courier_package").hide();
            $("#courier_package_id").empty();
            $("#courier_package_id").append('<option value="">Silahkan Pilih Paket Pengiriman</option>');
            $(".price_category").hide();
            $("#price_category_id").empty();
            $("#price_category_id").append('<option value="">Silahkan Pilih Kategori Harga</option>');
          }
        });

        $("#district_id").change(function(){
          if($("#district_id").val()!=""){
            var data = {"_token":"{{ csrf_token() }}","district_id":$("#district_id").val()};
            if(typeof courier_package_id != 'undefined'){
              data.courier_package_id = courier_package_id;
            }

            $.ajax({
              url : '{{URL::route('backend_manage_courier_ged_delivery_price_get_courier_package')}}',
              type: 'POST',
              dataType: 'JSON',
              data: data,
              success : function(data){
                $("#courier_package_id").empty();
                $("#courier_package_id").append('<option value="">Silahkan Pilih Paket Pengiriman</option>');

                $.each(data,function(key,value){
                  $("#courier_package_id").append('<option value="'+key+'">'+value+'</option>');
                });

                if(typeof courier_package_id != 'undefined'){
                  $("#courier_package_id").val(courier_package_id);
                  $("#courier_package_id").change();
                }

                $(".courier_package").show();
                $(".price_category").hide();
                $("#price_category_id").empty();
                $("#price_category_id").append('<option value="">Silahkan Pilih Kategori Harga</option>');
              }
            });
          }else{
            $(".courier_package").hide();
            $("#courier_package_id").empty();
            $("#courier_package_id").append('<option value="">Silahkan Pilih Paket Pengiriman</option>');
            $(".price_category").hide();
            $("#price_category_id").empty();
            $("#price_category_id").append('<option value="">Silahkan Pilih Kategori Harga</option>');
          }
        });

        $("#courier_package_id").change(function(){
          if($("#courier_package_id").val()!=""){
            var data = {"_token":"{{ csrf_token() }}","courier_package_id":$("#courier_package_id").val(),"district_id":$("#district_id").val()};
            if(typeof price_category_id != 'undefined'){
              data.price_category_id = price_category_id;
            }

            $.ajax({
              url : '{{URL::route('backend_manage_courier_ged_delivery_price_get_price_category')}}',
              type: 'POST',
              dataType: 'JSON',
              data: data,
              success : function(data){
                $("#price_category_id").empty();
                $("#price_category_id").append('<option value="">Silahkan Pilih Kategori Harga</option>');

                $.each(data,function(key,value){
                  $("#price_category_id").append('<option value="'+key+'">'+value+'</option>');
                });

                if(typeof price_category_id != 'undefined'){
                  $("#price_category_id").val(price_category_id);
                  $("#price_category_id").change();
                }

                $(".price_category").show();
              }
            });
          }else{
            $(".price_category").hide();
            $("#price_category_id").empty();
            $("#price_category_id").append('<option value="">Silahkan Pilih Kategori Harga</option>');
          }
        });

      }else{
        if(typeof province_id != 'undefined'){
          $("#name").html(province_id);
        }
      }


      if(typeof price != 'undefined'){
        $("#price").val(price);
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

      setSubmitModalEvent('{{URL::route('backend_manage_courier_ged_delivery_price_create')}}');
    }

    function edit(e) {
      initializeModal($("#modal-template").html(),"Edit Data",$(e).data('id'),$(e).data('province_id'),$(e).data('city_id'),$(e).data('district_id'),$(e).data('courier_package_id'),$(e).data('price_category_id'),$(e).data('price'),$(e).data('status'));
      showModal();
      $('#modal_view').on('hidden.bs.modal',function(){
        resetModal();
      });

      setSubmitModalEvent('{{URL::route('backend_manage_courier_ged_delivery_price_update')}}');
    }

    function destroy(e) {
      initializeModal($("#modal-template-delete").html(),"Delete Data",$(e).data('id'),$(e).data('name'));
      showModal();
      $('#modal_view').on('hidden.bs.modal',function(){
        resetModal();
      });

      setSubmitModalEvent('{{URL::route('backend_manage_courier_ged_delivery_price_destroy')}}');
    }
  </script>
@stop
