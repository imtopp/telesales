@extends('backend\layout\template')

@section('title', 'Manage Courier')

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

@section('page-css-script')
<style>
  .category-block{
    border-width: medium;
    border-style: double;
  }
  .middle-block{
    text-align:center;
    font-size:18pt;
  }
  .footer-block{
    margin-top:20px;
    text-align:right;
  }
</style>
@endsection

@section('content')
<div class="row">
  <div class="col-md-12 col-sm-12 col-xs-12">
    <div class="x_panel">
      <div class="x_title">
        <h2>Manage Courier Page</h2>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <h4>Welcome to Manage Courier Page</h4>
        <span>
          Selamat datang di halaman manage courier. halaman ini digunakan untuk mengatur kurir pada aplikasi {{ config('settings.app_name') }}
          anda dapat mengatur kurir pada tabel dibawah.
        </span>
        <table id="datatable" class="table table-striped table-bordered">
          <thead>
            <tr>
              <th>Nama</th>
              <th>Price Category</th>
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

  <script type = "text/template" id="modal-template-price-category">
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
            {!! Form::button("Add New Price Category",["class"=>"btn btn-default","id"=>"add_price_category"]) !!}
          </div>
        </div>
        <div class="row" id="price-category-wrapper">
          <div class="col-md-5">
            <div class="form-group">
              <div class="controls">
                {!! Form::input("number","category_price_1_min_price",1,["class"=>"form-control","required","readonly",'min'=>'1','max'=>'1',"id"=>"category_price_1_min_price"]) !!}
              </div>
            </div>
          </div>
          <div class="col-md-2 middle-block">
            <span>-</span>
          </div>
          <div class="col-md-5">
            <div class="form-group">
              <div class="controls">
                {!! Form::input("text","category_price_1_max_price","~",["class"=>"form-control","required",'readonly',"id"=>"category_price_1_max_price"]) !!}
              </div>
            </div>
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

            if (spin == "show") {
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
          url: "{{URL::route('administrator_manage_courier_read')}}",
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
          "data": "name",
          "title": "Name",
          "width": "240px"
        },{
          "data": "status",
          "title": "Status",
          "width": "150px"
        },{
          "data": "price_category",
          "title": "Kategori Harga",
          "width": "200px"
        },{
          "data": "action",
          "title": "Action",
          "width": "200px"
        }],
        deferRender: true,
     });
    });

    function initializeModal(type,mode,element,title,id,name,status){
      $.spin("show");
      $("#modal-content").html(element);
      $("#title").html(title);

      if(type == "courier"){
        if(mode == "update" || mode == "delete"){
          $("#id").val(id);

          if(mode == "update"){
            $("#name").val(name);
            $("#status").val(status);
          }else if(mode == "delete"){
            $("#name").html(name);
          }
        }

        $.spin("hide");
        $('#modal_view').modal('show');
        $('#modal_view').on('hidden.bs.modal',function(){
          $("#modal-content").html("");
        });
      }else if(type == "price_category"){
        if(mode == "update"){
          $("#id").val(id);

          $("#add_price_category").click(function(){
            var min,id;

            $("#price-category-wrapper").find("input[type=text]").each(function() {
              min = parseInt($(this).parent().parent().parent().prev().prev().find("input[type=number]").attr("min"));
              id = $(this).attr('id');
              $("<input type='number' />").attr({ id: $(this).attr('id'), name: $(this).attr('name'), class: $(this).attr('class'), required: $(this).attr('required'), min: min+1, value: min+1}).insertBefore(this);
            }).remove();

            $("#price-category-wrapper").append('<div class="col-md-5">'
            +'<div class="form-group">'
            +'<div class="controls">'
            +'{!! Form::input("number","min_price",0,["class"=>"form-control","required","min"=>"0","id"=>"min_price"]) !!}'
            +'</div>'
            +'</div>'
            +'</div>'
            +'<div class="col-md-2 middle-block">'
            +'<span>-</span>'
            +'</div>'
            +'<div class="col-md-5">'
            +'<div class="form-group">'
            +'<div class="controls">'
            +'{!! Form::text("max_price","~",["class"=>"form-control","required","readonly","id"=>"max_price"]) !!}'
            +'</div>'
            +'</div>'
            +'</div>');

            $("#min_price").attr("min",min+2).attr("id","category_price_"+(parseInt(id.split("category_price_")[1].split("_max_price")[0])+1)+"_min_price").attr("name","category_price_"+(parseInt(id.split("category_price_")[1].split("_max_price")[0])+1)+"_min_price").val(min+2)
            $("#max_price").attr("id","category_price_"+(parseInt(id.split("category_price_")[1].split("_max_price")[0])+1)+"_max_price").attr("name","category_price_"+(parseInt(id.split("category_price_")[1].split("_max_price")[0])+1)+"_max_price");

            $("#"+id).change(function(){
              if(parseInt($("#"+"category_price_"+(parseInt(id.split("category_price_")[1].split("_max_price")[0]))+"_min_price").val())>=parseInt($("#"+id).val())){
                $("#"+id).val(parseInt($("#"+"category_price_"+(parseInt(id.split("category_price_")[1].split("_max_price")[0]))+"_min_price").val())+1);
              }
              $("#"+"category_price_"+(parseInt(id.split("category_price_")[1].split("_max_price")[0])+1)+"_min_price").val(parseInt($("#"+id).val())+1);
              $("#"+"category_price_"+(parseInt(id.split("category_price_")[1].split("_max_price")[0])+1)+"_min_price").change();
            });

            $("#"+"category_price_"+(parseInt(id.split("category_price_")[1].split("_max_price")[0])+1)+"_min_price").change(function(){
              if(parseInt($("#"+"category_price_"+(parseInt(id.split("category_price_")[1].split("_max_price")[0])+1)+"_min_price").val())<=parseInt($("#"+id).val()) || parseInt($("#"+"category_price_"+(parseInt(id.split("category_price_")[1].split("_max_price")[0])+1)+"_min_price").val())>(parseInt($("#"+id).val())+1)){
                $("#"+"category_price_"+(parseInt(id.split("category_price_")[1].split("_max_price")[0])+1)+"_min_price").val(parseInt($("#"+id).val())+1);
              }
              if($("#"+"category_price_"+(parseInt(id.split("category_price_")[1].split("_max_price")[0])+1)+"_max_price").attr('type')!="text"){
                $("#"+"category_price_"+(parseInt(id.split("category_price_")[1].split("_max_price")[0])+1)+"_max_price").val(parseInt($("#"+"category_price_"+(parseInt(id.split("category_price_")[1].split("_max_price")[0])+1)+"_min_price").val())+1);
                $("#"+"category_price_"+(parseInt(id.split("category_price_")[1].split("_max_price")[0])+1)+"_max_price").change();
              }
            });
          });

          $.spin("hide");
          $('#modal_view').modal('show');
          $('#modal_view').on('hidden.bs.modal',function(){
            $("#modal-content").html("");
          });
        }
      }
    }

    function getModalFormData(){
      var popup_form = {};
      $("form#popup_form :input").each(function(){
        if(typeof $(this).attr('name') != "undefined" && $(this).attr('name').split($(this).attr('name').substr($(this).attr('name').length-10))[0].substring(0,8) == "category"){
          if(typeof popup_form[$(this).attr('name').split($(this).attr('name').substr($(this).attr('name').length-10))[0]] == "undefined")
            popup_form[$(this).attr('name').split($(this).attr('name').substr($(this).attr('name').length-10))[0]] = {};
          popup_form[$(this).attr('name').split($(this).attr('name').substr($(this).attr('name').length-10))[0]][$(this).attr('name').substr($(this).attr('name').length-9)] = $(this).val();
        }else{
          popup_form[$(this).attr('name')] = $(this).val();
        }
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

    function create(e) {
      initializeModal("courier","create",$("#modal-template").html(),"Tambah Data Baru");

      setSubmitModalEvent('{{URL::route('administrator_manage_courier_create')}}');
    }

    function edit(e) {
      initializeModal("courier","update",$("#modal-template").html(),"Edit Data",$(e).data('id'),$(e).data('name'),$(e).data('status'));

      setSubmitModalEvent('{{URL::route('administrator_manage_courier_update')}}');
    }

    function destroy(e) {
      initializeModal("courier","delete",$("#modal-template-delete").html(),"Delete Data",$(e).data('id'),$(e).data('name'));

      setSubmitModalEvent('{{URL::route('administrator_manage_courier_destroy')}}');
    }

    function price_category(e) {
      if($(e).data('reset') == true){
        if(!confirm('Apakah anda yakin akan melakukan reset price category?')){
          return 0;
        }
      }

      initializeModal("price_category","update",$("#modal-template-price-category").html(),"Price Category Data",$(e).data('id'));

      setSubmitModalEvent('{{URL::route('administrator_manage_courier_price_category')}}');
    }
  </script>
@stop
