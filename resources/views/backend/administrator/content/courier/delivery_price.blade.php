@extends('backend\layout\template')

@section('title', 'Courier Delivery Price')

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
        <h2>Courier Delivery Price</h2>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <h4>Welcome to Courier Delivery Price Page</h4>
        <span>
          Selamat datang di halaman Courier Delivery Price. halaman ini digunakan untuk mengatur delivery price untuk kurir pada aplikasi {{ config('settings.app_name') }}
          anda dapat mengatur delivery price pada tabel dibawah.
        </span>
        <table id="datatable" class="table table-striped table-bordered">
          <thead>
            <tr>
              <th>Province</th>
              <th>City</th>
              <th>District</th>
              <th>Kurir</th>
              <th>Paket Pengiriman</th>
              <th>Delivery Price</th>
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
        <div class="form-group courier" style="display:none">
          <div class="controls">
            {!! Form::label("courier_id", "Kurir") !!}
            {!! Form::select('courier_id', [''=>'Harap pilih Kurir'], null, ["class"=>"form-control","required","id"=>"courier_id"]); !!}
          </div>
        </div>
        <div class="form-group courier_package" style="display:none">
          <div class="controls">
            {!! Form::label("courier_package_id", "Paket Pengiriman") !!}
            {!! Form::select('courier_package_id', [''=>'Harap pilih Paket Pengiriman'], null, ["class"=>"form-control","required","id"=>"courier_package_id"]); !!}
          </div>
        </div>
        <div class="form-group price_category" style="display:none">
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

  <script type = "text/template" id="modal-template-delivery-price">
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
          url: "{{URL::route('administrator_manage_courier_delivery_price_read')}}",
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
          "data": "delivery_price",
          "title": "Delivery Price"
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

    function initializeModal(type,element,title,id,province_id,city_id,district_id,courier_id,courier_package_id,status){
      $("#modal-content").html(element);
      $("#title").html(title);

      var data = {"_token":"{{ csrf_token() }}"};
      if(type == "mapping"){
        if(typeof id != "undefined" && typeof province_id != "undefined" && typeof city_id != "undefined" && typeof courier_id != "undefined" && typeof courier_package_id != "undefined" && typeof status != "undefined"){
          flag = "edit";
        }else{
          flag = "new";
        }

        if(flag == "edit"){
          $("#id").val(id);
          $("#status").val(status);
          data.province_id = province_id;
          data.city_id = city_id;
          data.district_id = district_id;
          data.courier_id = courier_id;
          data.courier_package_id = courier_package_id;
        }

        if($("#province_id").is("select")){
          $.ajax({
            url : '{{URL::route('administrator_manage_courier_delivery_price_get_province')}}',
            type: 'POST',
            dataType: 'JSON',
            data: data,
            success : function(data){
              $.each(data,function(key,value){
                $("#province_id").append('<option value="'+key+'">'+value+'</option>');
              });
              if(flag == "edit"){
                $("#province_id").val(province_id);
                $("#province_id").change();
              }
            }
          });
        }

        $("#province_id").change(function(){
          if($("#province_id").val()!=""){
            data.province_id = $("#province_id").val();

            $.ajax({
              url : '{{URL::route('administrator_manage_courier_delivery_price_get_city')}}',
              type: 'POST',
              dataType: 'JSON',
              data: data,
              success : function(data){
                $("#city_id").empty();
                $("#city_id").append('<option value="">Harap Pilih Kota</option>');

                $.each(data,function(key,value){
                  $("#city_id").append('<option value="'+key+'">'+value+'</option>');
                });

                if(flag == "edit" && $("#province_id").val() == province_id){
                  $("#city_id").val(city_id);
                  $("#city_id").change();
                }

                $(".city").show();
                $(".district").hide();
                $("#district_id").empty();
                $("#district_id").append('<option value="">Harap Pilih Kecamatan</option>');
                $(".courier").hide();
                $("#courier_id").empty();
                $("#courier_id").append('<option value="">Silahkan Pilih Paket Pengiriman</option>');
                $(".courier_package").hide();
                $("#courier_package_id").empty();
                $("#courier_package_id").append('<option value="">Silahkan Pilih Paket Pengiriman</option>');
                $(".price_category").hide();
                $(".price_category").empty();
              }
            });
          }else{
            $(".city").hide();
            $("#city_id").empty();
            $("#city_id").append('<option value="">Harap Pilih Kota</option>');
            $(".district").hide();
            $("#district_id").empty();
            $("#district_id").append('<option value="">Harap Pilih Kecamatan</option>');
            $(".courier").hide();
            $("#courier_id").empty();
            $("#courier_id").append('<option value="">Silahkan Pilih Paket Pengiriman</option>');
            $(".courier_package").hide();
            $("#courier_package_id").empty();
            $(".price_category").hide();
            $(".price_category").empty();
          }
        });

        $("#city_id").change(function(){
          if($("#city_id").val()!=""){
            data.city_id = $("#city_id").val();

            $.ajax({
              url : '{{URL::route('administrator_manage_courier_delivery_price_get_district')}}',
              type: 'POST',
              dataType: 'JSON',
              data: data,
              success : function(data){
                $("#district_id").empty();
                $("#district_id").append('<option value="">Silahkan Pilih Kecamatan</option>');

                $.each(data,function(key,value){
                  $("#district_id").append('<option value="'+key+'">'+value+'</option>');
                });

                if(flag == "edit" && $("#city_id").val() == city_id){
                  $("#district_id").val(district_id);
                  $("#district_id").change();
                }

                $(".district").show();
                $(".courier").hide();
                $("#courier_id").empty();
                $("#courier_id").append('<option value="">Silahkan Pilih Paket Pengiriman</option>');
                $(".courier_package").hide();
                $("#courier_package_id").empty();
                $("#courier_package_id").append('<option value="">Silahkan Pilih Paket Pengiriman</option>');
                $(".price_category").hide();
                $(".price_category").empty();
              }
            });
          }else{
            $(".district").hide();
            $("#district").empty();
            $("#district").append('<option value="">Silahkan Pilih Kecamatan</option>');
            $(".courier").hide();
            $("#courier_id").empty();
            $("#courier_id").append('<option value="">Silahkan Pilih Paket Pengiriman</option>');
            $(".courier_package").hide();
            $("#courier_package_id").empty();
            $("#courier_package_id").append('<option value="">Silahkan Pilih Paket Pengiriman</option>');
            $(".price_category").hide();
            $(".price_category").empty();
          }
        });

        $("#district_id").change(function(){
          if($("#district_id").val()!=""){
            data.district_id = $("#district_id").val();

            $.ajax({
              url : '{{URL::route('administrator_manage_courier_delivery_price_get_courier')}}',
              type: 'POST',
              dataType: 'JSON',
              data: data,
              success : function(data){
                $("#courier_id").empty();
                $("#courier_id").append('<option value="">Silahkan Pilih Kurir</option>');

                $.each(data,function(key,value){
                  $("#courier_id").append('<option value="'+key+'">'+value+'</option>');
                });

                if(flag == "edit" && $("#district_id").val() == district_id){
                  $("#courier_id").val(courier_id);
                  $("#courier_id").change();
                }

                $(".courier").show();
                $(".courier_package").hide();
                $("#courier_package_id").empty();
                $("#courier_package_id").append('<option value="">Silahkan Pilih Paket Pengiriman</option>');
                $(".price_category").hide();
                $(".price_category").empty();
              }
            });
          }else{
            $(".courier").hide();
            $("#courier_id").empty();
            $("#courier_id").append('<option value="">Silahkan Pilih Paket Pengiriman</option>');
            $(".courier_package").hide();
            $("#courier_package_id").empty();
            $("#courier_package_id").append('<option value="">Silahkan Pilih Paket Pengiriman</option>');
            $(".price_category").hide();
            $(".price_category").empty();
          }
        });

        $("#courier_id").change(function(){
          if($("#courier_id").val()!=""){
            data.courier_id = $("#courier_id").val();
            data.district_id = $("#district_id").val();

            $.ajax({
              url : '{{URL::route('administrator_manage_courier_delivery_price_get_courier_package')}}',
              type: 'POST',
              dataType: 'JSON',
              data: data,
              success : function(data){
                $("#courier_package_id").empty();
                $("#courier_package_id").append('<option value="">Silahkan Pilih Paket Pengiriman</option>');

                $.each(data,function(key,value){
                  $("#courier_package_id").append('<option value="'+key+'">'+value+'</option>');
                });

                if(flag == "edit" && $("#courier_id").val() == courier_id && $("#district_id").val() == district_id){
                  $("#courier_package_id").val(courier_package_id);
                }

                $(".courier_package").show();
                $(".price_category").hide();
                $(".price_category").empty();
              }
            });
          }else{
            $(".courier_package").hide();
            $("#courier_package_id").empty();
            $("#courier_package_id").append('<option value="">Silahkan Pilih Paket Pengiriman</option>');
            $(".price_category").hide();
            $(".price_category").empty();
          }
        });

        $("#courier_package_id").change(function(){
          if($("#courier_package_id").val()!="" && flag == "new"){
            data.courier_id = $("#courier_id").val();

            $.ajax({
              url : '{{URL::route('administrator_manage_courier_delivery_price_get_price_category')}}',
              type: 'POST',
              dataType: 'JSON',
              data: data,
              success : function(data){
                String.prototype.capitalizeFirstLetter = function() {
                    return this.charAt(0).toUpperCase() + this.slice(1);
                }

                $.each(data,function(key,value){
                  $(".price_category").append('<div class="controls">'
                                              +'<label for="price_'+key+'">'+value.replace(/_/g," ").capitalizeFirstLetter()+'</label>'
                                              +'<input class="form-control" required="required" id="price_'+key+'" min="1" name="price_'+key+'" type="number" value="1">'
                                              +'</div>');
                });

                $(".price_category").show();
              }
            });
          }else{
            $(".price_category").hide();
            $(".price_category").empty();
          }
        });
      }else if(type == "delivery_price"){
        $("#id").val(id);
        data.id = id;
        data.courier_id = courier_id;

        $.ajax({
          url : '{{URL::route('administrator_manage_courier_delivery_price_get_price_category')}}',
          type: 'POST',
          dataType: 'JSON',
          data: data,
          success : function(data){
            String.prototype.capitalizeFirstLetter = function() {
                return this.charAt(0).toUpperCase() + this.slice(1);
            }

            $.each(data,function(){
              $("#message").append('<div class="controls">'
                                          +'<label for="price_'+this.price_category_id+'">'+this.price_category.replace("_"," ").capitalizeFirstLetter()+'</label>'
                                          +'<input class="form-control" required="required" id="price_'+this.price_category_id+'" min="1" name="price_'+this.price_category_id+'" type="number" value="'+this.price+'">'
                                          +'</div>');
            });
          }
        });
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

      if(typeof popup_form["price"] == "undefined")
        popup_form.delivery_price = {};
      $("form#popup_form :input").each(function(){
        if(typeof $(this).attr('name') != "undefined" && $(this).attr('name').substring(0,5)=="price"){
          popup_form.delivery_price[$(this).attr('name').split("price_")[1]] = $(this).val();
        }else{
          popup_form[$(this).attr('name')]=$(this).val();
        }
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
      initializeModal("mapping",$("#modal-template").html(),"Tambah Data Baru");
      showModal();
      $('#modal_view').on('hidden.bs.modal',function(){
        resetModal();
      });

      setSubmitModalEvent('{{URL::route('administrator_manage_courier_delivery_price_create')}}');
    }

    function edit(e) {
      initializeModal("mapping",$("#modal-template").html(),"Edit Data",$(e).data('id'),$(e).data('province_id'),$(e).data('city_id'),$(e).data('district_id'),$(e).data('courier_id'),$(e).data('courier_package_id'),/*$(e).data('price_category_id'),$(e).data('price'),*/$(e).data('status'));
      showModal();
      $('#modal_view').on('hidden.bs.modal',function(){
        resetModal();
      });

      setSubmitModalEvent('{{URL::route('administrator_manage_courier_delivery_price_update')}}');
    }

    function delivery_price(e){
      initializeModal("delivery_price",$("#modal-template-delivery-price").html(),"Change Delivery Price",$(e).data('id'),null,null,null,$(e).data('courier_id'));
      showModal();
      $('#modal_view').on('hidden.bs.modal',function(){
        resetModal();
      });

      setSubmitModalEvent('{{URL::route('administrator_manage_courier_delivery_price_update')}}');
    }
  </script>
@stop