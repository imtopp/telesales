@extends('backend\layout\template')

@section('title', 'Manage Courier Package')

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
        <h2>Manage Courier Package Page</h2>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <h4>Welcome to Manage Courier Package Page</h4>
        <span>
          Selamat datang di halaman manage courier package. halaman ini digunakan untuk mengatur package kurir pada aplikasi {{ config('settings.app_name') }}
          anda dapat mengatur kurir pada tabel dibawah.
        </span>
        <table id="datatable" class="table table-striped table-bordered">
          <thead>
            <tr>
              <th>Courier</th>
              <th>Nama</th>
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
            {!! Form::label("courier_id", "Courier") !!}
            {!! Form::select('courier_id', [''=>'Harap Pilih Courier'], null, ["class"=>"form-control","required","readonly","id"=>"courier_id"]); !!}
          </div>
        </div>
        <div class="form-group">
          <div class="controls">
            {!! Form::label("name", "Nama") !!}
            {!! Form::text("name",null,["class"=>"form-control","required","readonly","id"=>"name"]) !!}
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

  <script>
  var table;
    $(document).ready(function() {
      table = $('#datatable').dataTable({
        dom: 'frtipl',
        "processing": true,
        "serverSide": true,
        ajax: {
          data: {"_token":"{{ csrf_token() }}"},
          url: "{{URL::route('backend_manage_courier_package_read')}}",
          type: "POST",
          error: function(){  // error handling
            $(".lookup-error").html("");
            $("#lookup").append('<tbody class="employee-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
            $("#lookup_processing").css("display","none");
          }
        },
        pageLength : 10,
        "columns": [{
          "data": "courier",
          "title": "Courier"
        },{
          "data": "name",
          "title": "Name"
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

    function initializeModal(element,title,id,name,courier_id,status){
      $("#modal-content").html(element);
      $("#title").html(title);
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
      if($("#courier_id").is("select")){
        $(function(){
          $.ajax({
            url : '{{URL::route('backend_manage_courier_package_get_courier')}}',
            type: 'POST',
            dataType: 'JSON',
            data: {"_token":"{{ csrf_token() }}"},
            success : function(data){
              $.each(data,function(key,value){
                $("#courier_id").append('<option value="'+key+'">'+value+'</option>');
              });
              if(typeof courier_id != 'undefined'){
                $("#courier_id").val(courier_id);
              }
            }
          });
        });
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

    function edit(e) {
      initializeModal($("#modal-template").html(),"Edit Data",$(e).data('id'),$(e).data('name'),$(e).data('courier_id'),$(e).data('status'));
      showModal();
      $('#modal_view').on('hidden.bs.modal',function(){
        resetModal();
      });

      setSubmitModalEvent('{{URL::route('backend_manage_courier_package_update')}}');
    }
  </script>
@stop