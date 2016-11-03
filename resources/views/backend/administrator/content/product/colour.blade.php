@extends('backend\layout\template')

@section('title', 'Manage Product Colour')

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
        <h2>Manage Product Colour Page</h2>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <h4>Welcome to Manage Product Colour Page</h4>
        <span>
          Selamat datang di halaman manage product colour. halaman ini digunakan untuk mengatur product colour pada aplikasi {{ config('settings.app_name') }}
          anda dapat mengatur produk colour pada tabel dibawah.
        </span>
        <table id="datatable" class="table table-striped table-bordered">
          <thead>
            <tr>
              <th>Category</th>
              <th>Produk</th>
              <th>Nama</th>
              <th class="text-center"> Image </th>
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
        <div class="form-group">
          <div class="controls">
            {!! Form::label("name", "Nama") !!}
            {!! Form::text("name",null,["class"=>"form-control","required","id"=>"name"]) !!}
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

  <script type = "text/template" id="modal-template-image">
    {!!Form::open(["id"=>"popup_form","class"=>"form form-horizontal","files"=>true])!!}
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal">&times;</button>
      <h4 id="title" class="modal-title"></h4>
    </div>
    <div class="modal-body">
      <div id="message">
        <div style="text-align:center">
          <img id="image_url" src="#" onerror="this.onerror=null;this.src='{{URL::asset('assets/img/img_not_found.jpg')}}';" style="max-height:350px"/>
        </div>
        <div class="form-group">
          {!! Form::hidden('id',null,['id'=>'id']) !!}
        </div>
        <div class="form-group">
            {!! Form::label('Product Colour Image') !!}
            {!! Form::file('upload_image', ['class'=>'form-control','required','accept'=>'image/x-png, image/gif, image/jpeg','id'=>'upload_image']) !!}
        </div>
      </div>
    </div>
    <div id="footer" class="modal-footer">
      <div class="form-group">
        <div class="controls">
          {!!Form::submit("Simpan",["class"=>"btn btn-primary submit"])!!}
          <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
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
          url: "{{URL::route('administrator_manage_product_colour_read')}}",
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
          "title": "Category",
          "width": "200px"
        },{
          "data": "product",
          "title": "Product",
          "width": "200px"
        },{
          "data": "name",
          "title": "Name",
          "width": "200px"
        },{
          "data": "image_url",
          "title": "Image",
          "width": "80px"
        },{
          "data": "status",
          "title": "Status",
          "width": "100px"
        },{
          "data": "action",
          "title": "Action",
          "width": "150px"
        }],
        "columnDefs": [{
          "targets": 3,
          "data": "image_url",
          "render": function ( data, type, full, meta ) {
            return '<td><center><a data-src="'+data+'" data-toggle="tooltip" title="View Image" class="btn btn-sm btn-primary" onClick="view(this)"> <i class="fa fa-eye"></i> View</a></td></center>';
          }
        }],
        deferRender: true,
     });
    });

    function initializeModal(type,mode,element,title,id,name,category_id,product_id,status){
      $.spin("show");
      $("#modal-content").html(element);
      $("#title").html(title);

      if(type == "data"){
        if(mode == "update" || mode == "delete"){
          $("#id").val(id);

          if(mode == "update"){
            $("#name").val(name);
            $("#status").val(status);
          }else if(mode == "delete"){
            $("#name").html(name);

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
              url : '{{URL::route('administrator_manage_product_colour_get_category')}}',
              type: 'POST',
              dataType: 'JSON',
              data: {"_token":"{{ csrf_token() }}"},
              success : function(data){
                $("#category_id").empty();
                $("#category_id").append('<option value="">Harap Pilih Category</option>')

                $.each(data,function(key,value){
                  $("#category_id").append('<option value="'+key+'">'+value+'</option>');
                });

                if(mode == "update"){
                  $("#category_id").val(category_id);
                  $("#category_id").change();
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

          $("#category_id").change(function(){
            if($("#category_id").val()!=""){
              $.ajax({
                url : '{{URL::route('administrator_manage_product_colour_get_product')}}',
                type: 'POST',
                dataType: 'JSON',
                data: {"_token":"{{ csrf_token() }}","category_id":$("#category_id").val()},
                success : function(data){
                  $("#product_id").empty();
                  $("#product_id").append('<option value="">Harap Pilih Produk</option>');

                  $.each(data,function(key,value){
                    $("#product_id").append('<option value="'+key+'">'+value+'</option>');
                  });

                  if(mode == "update" && $("#category_id").val() == category_id){
                    $("#product_id").val(product_id);
                  }

                  $(".product").show();

                  $.spin("hide");
                  $('#modal_view').modal('show');
                  $('#modal_view').on('hidden.bs.modal',function(){
                    $("#modal-content").html("");
                  });
                }
              });
            }else{
              $(".product").hide();
              $("#product_id").empty();
              $("#product_id").append('<option value="">Harap Pilih Produk</option>');
            }
          });
        }
      }else if(type=="image"){
        if(mode == "read"){
          $("#id").val(id);
          $("#image_url").attr('src',name);
        }

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

        var data = getModalFormData();

        if(typeof $("#image_url")!="undefined"){
          if($("#image").val()==""){
            $.spin("hide");
            alert("Harap pilih gambar yang ingin di upload!");
            return false;
          }
          data = new FormData($("#popup_form")[0]);
        }

        $.ajax({
          url : url,
          type: 'POST',
          dataType: 'JSON',
          data: data,
          processData: false,
          contentType: false,
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
            return true;
          }
        });
      });
    }

    function create(){
      initializeModal('data','create',$("#modal-template").html(),"Tambah Data Baru");

      setSubmitModalEvent('{{URL::route('administrator_manage_product_colour_create')}}');
    }

    function edit(e) {
      initializeModal('data',"update",$("#modal-template").html(),"Edit Data",$(e).data('id'),$(e).data('name'),$(e).data('category_id'),$(e).data('product_id'),$(e).data('status'));

      setSubmitModalEvent('{{URL::route('administrator_manage_product_colour_update')}}');
    }

    function destroy(e) {
      initializeModal('data',"delete",$("#modal-template-delete").html(),"Delete Data",$(e).data('id'),$(e).data('name'));

      setSubmitModalEvent('{{URL::route('administrator_manage_product_colour_destroy')}}');
    }

    function view(e){
      initializeModal('image',"read",$("#modal-template-image").html(),"View Image",$(e).parent().parent().next().next().find("center").find("a").data('id'),$(e).data('src'));

      setSubmitModalEvent('{{URL::route('administrator_manage_product_colour_upload')}}');
    }
  </script>
@stop
