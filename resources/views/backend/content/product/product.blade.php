@extends('backend\layout\template')

@section('title', 'Manage Product')

@section('sidebar-menu')
  @include('backend\layout\sidebar_menu_content')
@endsection

@section('page-css-file')
<!-- datatables bootstrap -->
<link href="{{ URL::asset('assets/vendors/datatables.net-bs/css/dataTables.bootstrap.min.css') }}" rel="stylesheet">
<!-- datatables bootstrap buttons-->
<link href="{{ URL::asset('assets/vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css') }}" rel="stylesheet">
<!-- wsywig editor summernote -->
<link href="{{ URL::asset('assets/vendors/summernote/dist/summernote.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="row">
  <div class="col-md-12 col-sm-12 col-xs-12">
    <div class="x_panel">
      <div class="x_title">
        <h2>Manage Product Page</h2>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <h4>Welcome to Manage Product Page</h4>
        <span>
          Selamat datang di halaman manage product. halaman ini digunakan untuk mengatur product pada aplikasi {{ config('settings.app_name') }}
          anda dapat mengatur produk pada tabel dibawah.
        </span>
        <table id="datatable" class="table table-striped table-bordered">
          <thead>
            <tr>
              <th>Nama</th>
              <th>Category</th>
              <th>Description</th>
              <th>Image</th>
              <th>Hit Count</th>
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
<!-- wsywig editor summernote -->
<script src="{{ URL::asset('assets/vendors/summernote/dist/summernote.min.js') }}"></script>
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
            {!! Form::label("name", "Nama") !!}
            {!! Form::text("name",null,["class"=>"form-control","required","id"=>"name"]) !!}
          </div>
        </div>
        <div class="form-group">
          <div class="controls">
            {!! Form::label("category_id", "Category") !!}
            {!! Form::select('category_id', $category, null, ["class"=>"form-control","required","id"=>"category_id"]); !!}
          </div>
        </div>
        <div class="form-group">
          <div class="controls">
            {!! Form::label("hit_count", "Hit Count") !!}
            {!! Form::input("number","hit_count",0,["class"=>"form-control","required","readonly","id"=>"hit_count"]) !!}
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
            {!! Form::file('upload_image', ['required','accept'=>'image/x-png, image/gif, image/jpeg','id'=>'upload_image']) !!}
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

  <script type = "text/template" id="modal-template-description">
    {!!Form::open(["id"=>"popup_form","class"=>"form form-horizontal","files"=>true])!!}
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
            {!! Form::textarea('description',null,['class'=>'form-control','required','placeholder'=>'Content','id'=>'description']) !!}
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
      $("#manage-product-product").addClass("current-page");
      $("#manage-product-product").parent().show();
      $("#manage-product-product").parent().parent().addClass("active");

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
          url: "{{URL::route('backend_manage_product_product_read')}}",
          type: "POST",
          error: function(){  // error handling
            $(".lookup-error").html("");
            $("#lookup").append('<tbody class="employee-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
            $("#lookup_processing").css("display","none");
          }
        },
        pageLength : 10,
        "columns": [{
          "data": "name",
          "title": "Name"
        },{
          "data": "category",
          "title": "Category"
        },{
          "data": "description",
          "title": "Description"
        },{
          "data": "image_url",
          "title": "Image"
        },{
          "data": "hit_count",
          "title": "Hit Count"
        },{
          "data": "status",
          "title": "Status"
        },{
          "data": "action",
          "title": "Action"
        }],
        "columnDefs": [{
          "targets": 2,
          "data": "description",
          "render": function ( data, type, full, meta ) {
            return '<td><center><a href="#" data-toggle="tooltip" title="Edit Description" class="btn btn-sm btn-primary" onClick="description(this)"> <i class="fa fa-file-text-o"></i> View</a><span id="description_content" style="display:none">'+data+'</span></td></center>';
          }
        },{
          "targets": 3,
          "data": "image_url",
          "render": function ( data, type, full, meta ) {
            return '<td><center><a href="#" data-src="'+data+'" data-toggle="tooltip" title="View Image" class="btn btn-sm btn-primary" onClick="view(this)"> <i class="fa fa-eye"></i> View</a></td></center>';
          }
        }],
        deferRender: true,
     });
    });

    function initializeModal(type,element,title,id,name,category_id,hit_count,status){
      $("#modal-content").html(element);
      $("#title").html(title);
      if(type=="data"){
        if(typeof id != 'undefined'){
          $("#id").val(id);
        }
        if(typeof name != 'undefined'){
          if($("#name").is("input")){
            $("#name").val(name);
          }else{
            $("#name").html(name);
          }
        }
        if(typeof category_id != 'undefined'){
          $("#category_id").val(category_id);
        }
        if(typeof hit_count != 'undefined'){
          $("#hit_count").val(hit_count);
        }
        if(typeof status != 'undefined'){
          $("#status").val(status);
        }
      }else if(type=="image"){
        $("#id").val(id);
        $("#image_url").attr('src',name);
      }else if(type=="description"){
        $("#id").val(id);
        $('#description').html(name);
        $('#description').summernote({
          toolbar: [
            // [groupName, [list of button]]
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough', 'superscript', 'subscript']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['height', ['height']]
          ],
          height:300,
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
      $("form#popup_form :input").each(function(){
        popup_form[$(this).attr('name')]=$(this).val();
      });
      return popup_form;
    }

    function setSubmitModalEvent(url){
      $("#popup_form").submit(function(e) {
        e.preventDefault();

        var data = getModalFormData();

        if(typeof $("#image_url")!="undefined"){
          if($("#image").val()==""){
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
            showModal();
            $('#modal_view').on('hidden.bs.modal',function(){
              resetModal();
            });
            table.fnReloadAjax();
            return true;
          }
        });
      });
    }

    function create(){
      initializeModal("data",$("#modal-template").html(),"Tambah Data Baru");
      showModal();
      $('#modal_view').on('hidden.bs.modal',function(){
        resetModal();
      });

      setSubmitModalEvent('{{URL::route('backend_manage_product_product_create')}}');
    }

    function edit(e) {
      initializeModal("data",$("#modal-template").html(),"Edit Data",$(e).data('id'),$(e).data('name'),$(e).data('category_id'),$(e).data('hit_count'),$(e).data('status'));
      showModal();
      $('#modal_view').on('hidden.bs.modal',function(){
        resetModal();
      });

      setSubmitModalEvent('{{URL::route('backend_manage_product_product_update')}}');
    }

    function destroy(e) {
      initializeModal("data",$("#modal-template-delete").html(),"Delete Data",$(e).data('id'),$(e).data('name'));
      showModal();
      $('#modal_view').on('hidden.bs.modal',function(){
        resetModal();
      });

      setSubmitModalEvent('{{URL::route('backend_manage_product_product_destroy')}}');
    }

    function view(e){
      initializeModal('image',$("#modal-template-image").html(),"View Image",$(e).parent().parent().next().next().next().find("center").find("a").data('id'),$(e).data('src'));
      showModal();
      $('#modal_view').on('hidden.bs.modal',function(){
        resetModal();
      });

      setSubmitModalEvent('{{URL::route('backend_manage_product_product_upload')}}');
    }

    function description(e){
      initializeModal('description',$("#modal-template-description").html(),"Description",$(e).parent().parent().next().next().next().next().find("center").find("a").data('id'),$(e).next().html());
      showModal();
      $('#modal_view').on('hidden.bs.modal',function(){
        resetModal();
      });

      setSubmitModalEvent('{{URL::route('backend_manage_product_product_description')}}');
    }
  </script>
@stop
