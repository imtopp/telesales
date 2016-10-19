@extends('backend\layout\template')

@section('title', 'Application Properties')

@section('sidebar-menu')
  @include('backend\administrator\layout\sidebar_menu_settings')
@endsection

@section('content')
<div class="row">
  <div class="col-md-12 col-sm-12 col-xs-12">
    <div class="x_panel">
      <div class="x_title">
        <h2>Application Properties</h2>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <h4>This is configuration of {{ config('settings.app_name') }} Application Properties Page</h4>
        <table id="table_properties">
          <thead>
            <tr>
              <th>Name</th>
              <th>Value</th>
            </tr>
          </thead>
          <tfoot>
            <tr>
              <th>Name</th>
              <th>Value</th>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection

@section('page-js-script')
  <script>
    $(document).ready(function() {
      $("#application_properties").addClass("current-page");
      $('#table_properties').DataTable({
        "ajax": {
            "url": "{{URL::route('administrator_get_application_properties')}}",
            "type": "POST",
            "data": {"_token":"{{ csrf_token() }}"},
        },
        "columns": [
            { "data": "name" },
            { "data": "value" }
        ]
      });
    });
  </script>
@stop
