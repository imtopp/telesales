@extends('backend\layout\template')

@section('title', 'GED Courier Price Category')

@section('sidebar-menu')
  @include('backend\layout\sidebar_menu_content')
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
        <h2>GED Courier Price Category</h2>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <h4>Welcome to GED Courier Price Category Page</h4>
        <div class="col-md-12" style="margin-bottom:20px">
          <span>
            Selamat datang di halaman GED Courier Price Category. halaman ini digunakan untuk mengatur kategori harga untuk kurir GED pada aplikasi {{ config('settings.app_name') }}
            anda dapat mengatur kategori harga pada bagian berikut.
          </span>
        </div>
        {!!Form::open(["id"=>"price_category_form","class"=>"form form-horizontal"])!!}
        <div class="row">
          <div class="col-md-4 category-block">
            <div class="row" style="text-align:center">
              <h2>Category 1</h2>
            </div>
            <div class="row">
              <div class="col-md-5">
                <div class="form-group">
                  <div class="controls">
                    {!! Form::input("number","category_1_min_price",1,["class"=>"form-control","required","readonly",'min'=>'1','max'=>'1',"id"=>"category_1_min_price"]) !!}
                  </div>
                </div>
              </div>
              <div class="col-md-2 middle-block">
                <span>-</span>
              </div>
              <div class="col-md-5">
                <div class="form-group">
                  <div class="controls">
                    {!! Form::input("number","category_1_max_price",2,["class"=>"form-control","required",'min'=>'2',"id"=>"category_1_max_price"]) !!}
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-4 category-block">
            <div class="row" style="text-align:center">
              <h2>Category 2</h2>
            </div>
            <div class="row">
              <div class="col-md-5">
                <div class="form-group">
                  <div class="controls">
                    {!! Form::input("number","category_2_min_price",3,["class"=>"form-control","required",'min'=>'3',"id"=>"category_2_min_price"]) !!}
                  </div>
                </div>
              </div>
              <div class="col-md-2 middle-block">
                <span>-</span>
              </div>
              <div class="col-md-5">
                <div class="form-group">
                  <div class="controls">
                    {!! Form::input("number","category_2_max_price",4,["class"=>"form-control","required",'min'=>'4',"id"=>"category_2_max_price"]) !!}
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-4 category-block">
            <div class="row" style="text-align:center">
              <h2>Category 3</h2>
            </div>
            <div class="row">
              <div class="col-md-5">
                <div class="form-group">
                  <div class="controls">
                    {!! Form::input("number","category_3_min_price",5,["class"=>"form-control","required",'min'=>'5',"id"=>"category_3_min_price"]) !!}
                  </div>
                </div>
              </div>
              <div class="col-md-2 middle-block">
                <span>-</span>
              </div>
              <div class="col-md-5">
                <div class="form-group">
                  <div class="controls">
                    {!! Form::text("category_3_max_price","~",["class"=>"form-control","required","readonly","id"=>"category_3_max_price"]) !!}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row footer-block">
          <div class="form-group">
            <div class="controls">
              {!!Form::submit("Save",["class"=>"btn btn-primary submit"])!!}
            </div>
          </div>
        </div>
        {!!Form::close()!!}
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="modal_view" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content" id="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 id="title" class="modal-title"></h4>
      </div>
      <div class="modal-body">
        <div id="message">
        </div>
      </div>
      <div id="footer" class="modal-footer">
        <div class="form-group">
          <div class="controls">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('page-js-script')
  <script>
    $(document).ready(function(){
      $(function(){
        $.ajax({
          url : '{{URL::route('backend_manage_courier_ged_price_category_read')}}',
          type: 'POST',
          dataType: 'JSON',
          data: {"_token":"{{ csrf_token() }}"},
          success : function(data){
            $.each( data, function( index, value ){
              if(value==0)
                value="~";
              $("#"+index).val(value);
            });
          }
        });
      });

      $("#category_1_max_price").change(function(){
        if($("#category_1_max_price").val()<=$("#category_1_min_price").val())
          $("#category_1_max_price").val(parseInt($("#category_1_min_price").val())+1);
        $("#category_2_min_price").val(parseInt($("#category_1_max_price").val())+1);
        $("#category_2_max_price").val(parseInt($("#category_2_min_price").val())+1);
        $("#category_3_min_price").val(parseInt($("#category_2_max_price").val())+1);
      });
      $("#category_2_min_price").change(function(){
        if($("#category_2_min_price").val()<=$("#category_1_max_price").val())
          $("#category_2_min_price").val(parseInt($("#category_1_max_price").val())+1);
        $("#category_2_max_price").val(parseInt($("#category_2_min_price").val())+1);
        $("#category_3_min_price").val(parseInt($("#category_2_max_price").val())+1);
      });
      $("#category_2_max_price").change(function(){
        if($("#category_2_max_price").val()<=$("#category_2_min_price").val())
          $("#category_2_max_price").val(parseInt($("#category_2_min_price").val())+1);
        $("#category_3_min_price").val(parseInt($("#category_2_max_price").val())+1);
      });
      $("#category_3_min_price").change(function(){
        if($("#category_3_min_price").val()<=$("#category_2_max_price").val())
          $("#category_3_min_price").val(parseInt($("#category_2_max_price").val())+1);
      });

      $("#price_category_form").submit(function(e) {
        e.preventDefault();

        var data = {};
        $("form#price_category_form :input").each(function(){
          data[$(this).attr('name')]=$(this).val();
        });

        $.ajax({
          url : '{{URL::route('backend_manage_courier_ged_price_category_update')}}',
          type: 'POST',
          dataType: 'JSON',
          data: data,
          success : function(data){
            $("#title").html("Pesan");
            $("#message").html(data.message);
            $("#footer").html('<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>');
            $('#modal_view').modal('show');
            $('#modal_view').on('hidden.bs.modal',function(){
              $("#modal-content").html("");
            });
          }
        });
      });
    });
  </script>
@stop
