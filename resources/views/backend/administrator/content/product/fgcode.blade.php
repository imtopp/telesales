@extends('backend\layout\template')

@section('title', 'Manage Product FG CODE')

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
        <h2>Manage Product FG CODE Page</h2>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <h4>Welcome to Manage Product FG CODE Page</h4>
        <span>
          Selamat datang di halaman manage product FG CODE. halaman ini digunakan untuk mengatur product FG CODE pada aplikasi {{ config('settings.app_name') }}
          anda dapat mengatur produk FG CODE pada tabel dibawah.
        </span>
        <table id="datatable" class="table table-striped table-bordered">
          <thead>
            <tr>
              <th>Category</th>
              <th>Product</th>
              <th>Colour</th>
              <th>FG CODE</th>
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
        <div class="form-group">
          <div class="controls">
            {!! Form::label("category_id", "Category") !!}
            {!! Form::select('category_id', [''=>'Harap Pilih Category'], null, ["class"=>"form-control","required","id"=>"category_id"]); !!}
          </div>
        </div>
        <div class="form-group product" style="display:none">
          <div class="controls">
            {!! Form::label("product_id", "Produk") !!}
            {!! Form::select('product_id', [''=>'Harap Pilih Produk'], null, ["class"=>"form-control","required","id"=>"product_id"]); !!}
          </div>
        </div>
        <div class="form-group colour" style="display:none">
          <div class="controls">
            {!! Form::label("colour_id", "Colour") !!}
            {!! Form::select('colour_id', [''=>'Harap Pilih Colour'], null, ["class"=>"form-control","required","id"=>"colour_id"]); !!}
          </div>
        </div>
        <div class="form-group">
          <div class="controls">
            {!! Form::label("fg_code", "FG CODE") !!}
            {!! Form::text("fg_code",null,["class"=>"form-control","required","id"=>"fg_code"]) !!}
          </div>
        </div>
        <div class="form-group">
          <div class="controls">
            {!! Form::label("price", "Price") !!}
            {!! Form::input('number',"price",0,["class"=>"form-control","required","id"=>"price"]) !!}
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
        <h4>Apakah anda yakin menghapus <span id="fg_code"></span> ?</h4>
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
          url: "{{URL::route('administrator_manage_product_fg_code_read')}}",
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
          "data": "category",
          "title": "Category"
        },{
          "data": "product",
          "title": "Product"
        },{
          "data": "colour",
          "title": "Colour"
        },{
          "data": "fg_code",
          "title": "FG CODE"
        },{
          "data": "price",
          "title": "Price"
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

    function initializeModal(element,title,id,fg_code,category_id,product_id,colour_id,price,status){
      $("#modal-content").html(element);
      $("#title").html(title);
      if(typeof id != 'undefined'){
        $("#id").val(id);
      }
      if(typeof fg_code != 'undefined'){
        if($("#fg_code").is("input")){
          $("#fg_code").val(fg_code);
        }else{
          $("#fg_code").html(fg_code);
        }
      }
      if($("#category_id").is("select")){
        $(function(){
          $.ajax({
            url : '{{URL::route('administrator_manage_product_fg_code_get_category')}}',
            type: 'POST',
            dataType: 'JSON',
            data: {"_token":"{{ csrf_token() }}"},
            success : function(data){
              $("#category_id").empty();
              $("#category_id").append('<option value="">Harap Pilih Category</option>')

              $.each(data,function(key,value){
                $("#category_id").append('<option value="'+key+'">'+value+'</option>');
              });

              if(typeof category_id != 'undefined'){
                $("#category_id").val(category_id);
                $("#category_id").change();
              }
            }
          });
        });

        $("#category_id").change(function(){
          if($("#category_id").val()!=""){
            $.ajax({
              url : '{{URL::route('administrator_manage_product_fg_code_get_product')}}',
              type: 'POST',
              dataType: 'JSON',
              data: {"_token":"{{ csrf_token() }}","category_id":$("#category_id").val()},
              success : function(data){
                $("#product_id").empty();
                $("#product_id").append('<option value="">Harap Pilih Produk</option>');

                $.each(data,function(key,value){
                  $("#product_id").append('<option value="'+key+'">'+value+'</option>');
                });

                if(typeof product_id != 'undefined'){
                  $("#product_id").val(product_id);
                  $("#product_id").change();
                }

                $(".product").show();
                $(".colour").hide();
              }
            });
          }else{
            $(".product").hide();
            $(".colour").hide();
            $("#product_id").empty();
            $("#product_id").append('<option value="">Harap Pilih Produk</option>');
            $("#colour_id").empty();
            $("#colour_id").append('<option value="">Harap Pilih Colour</option>');
          }
        });

        $("#product_id").change(function(){
          if($("#product_id").val()!=""){
            $.ajax({
              url : '{{URL::route('administrator_manage_product_fg_code_get_colour')}}',
              type: 'POST',
              dataType: 'JSON',
              data: {"_token":"{{ csrf_token() }}","product_id":$("#product_id").val()},
              success : function(data){
                $("#colour_id").empty();
                $("#colour_id").append('<option value="">Harap Pilih Colour</option>');

                $.each(data,function(key,value){
                  $("#colour_id").append('<option value="'+key+'">'+value+'</option>');
                });

                if(typeof colour_id != 'undefined'){
                  $("#colour_id").val(colour_id);
                }

                $(".colour").show();
              }
            });
          }else{
            $(".colour").hide();
            $("#colour_id").empty();
            $("#colour_id").append('<option value="">Harap Pilih Colour</option>');
          }
        });
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

      setSubmitModalEvent('{{URL::route('administrator_manage_product_fg_code_create')}}');
    }

    function edit(e) {
      initializeModal($("#modal-template").html(),"Edit Data",$(e).data('id'),$(e).data('fg_code'),$(e).data('category_id'),$(e).data('product_id'),$(e).data('colour_id'),$(e).data('price'),$(e).data('status'));
      showModal();
      $('#modal_view').on('hidden.bs.modal',function(){
        resetModal();
      });

      setSubmitModalEvent('{{URL::route('administrator_manage_product_fg_code_update')}}');
    }

    function destroy(e) {
      initializeModal($("#modal-template-delete").html(),"Delete Data",$(e).data('id'),$(e).data('fg_code'));
      showModal();
      $('#modal_view').on('hidden.bs.modal',function(){
        resetModal();
      });

      setSubmitModalEvent('{{URL::route('administrator_manage_product_fg_code_destroy')}}');
    }
  </script>
@stop
