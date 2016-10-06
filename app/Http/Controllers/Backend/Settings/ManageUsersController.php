<?php

namespace App\Http\Controllers\Backend\Settings;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesResources;
use Illuminate\Contracts\Cache\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User as UserModel;
use App\Models\UserRole as UserRoleModel;
use DB;
use File;
use DateTime;

class ManageUsersController extends BaseController
{
  use AuthorizesRequests, AuthorizesResources, DispatchesJobs, ValidatesRequests;

  //Render Page
  public function index(){
    $user_role = UserRoleModel::lists('name','id');
    return view('backend/settings/manage_users',['user_role'=>$user_role]);
  }

  //Read All Users
  public function read(){
    // storing  request (ie, get/post) global array to a variable
    $requestData= $_REQUEST;

    $columns = array(
    // datatable column index  => database column name
        0 => 'email',
        1 => 'role',
        2 => 'status'
    );

    $totalData = UserModel::count();
    $totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

    if( !empty($requestData['search']['value']) ) {
      // if there is a search parameter
      $model = DB::table('users')
                ->select('users.id', 'users.email', 'user_roles.name AS role', 'users.status')
                ->join('user_roles', 'user_roles.id', '=', 'users.role_id')
                ->where('users.email','LIKE',$requestData['search']['value'].'%')
                ->orWhere('users.status','LIKE',$requestData['search']['value'].'%')
                ->orWhere('user_roles.name','LIKE',$requestData['search']['value'].'%');
      $totalFiltered = $model->count();
      $query = $model
                ->orderBy($columns[$requestData['order'][0]['column']],$requestData['order'][0]['dir'])
                ->skip($requestData['start'])
                ->take($requestData['length'])
                ->get();
    } else {
      $query = DB::table('users')
                ->select('users.id', 'users.email', 'user_roles.name AS role', 'users.status')
                ->join('user_roles', 'user_roles.id', '=', 'users.role_id')
                ->orderBy($columns[$requestData['order'][0]['column']],$requestData['order'][0]['dir'])
                ->skip($requestData['start'])
                ->take($requestData['length'])
                ->get();
    }

    $data = array();
    foreach($query as $row) {  // preparing an array
        $nestedData=array();

        $nestedData[$columns[0]] = $row->email;
        $nestedData[$columns[1]] = $row->role;
        $nestedData[$columns[2]] = $row->status;
        if($row->role!="Customer"){
          $nestedData['action'] = '<td><center>
                           <a href="#" data-id="'.$row->id.'" data-email="'.$row->email.'" data-role="'.$row->role.'" data-status="'.$row->status.'" data-toggle="tooltip" title="Edit" class="btn btn-sm btn-warning edit" onClick="edit(this)"> <i class="fa fa-pencil"></i> </a>
                           <a href="#" data-id="'.$row->id.'" data-email="'.$row->email.'" data-toggle="tooltip" title="Hapus" class="btn btn-sm btn-danger destroy" onClick="destroy(this)"> <i class="fa fa-trash"></i> </a>
                           </center></td>';
        }else{
          $nestedData['action'] = "";
        }

        $data[] = $nestedData;
    }

    $json_data = array(
      "draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
      "recordsTotal"    => intval( $totalData ),  // total number of records
      "recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
      "data"            => $data   // total data array
    );

    return response()->json($json_data);
  }

  //Create New User
  public function create(){
    $date = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s')); //initialize date parameter
    $user_role = UserRoleModel::where(["name"=>"Administrator"])->first();

    $user = new UserModel;
    $user->email = $_POST['email'];
    $user->password = Hash::make(substr(md5(uniqid(mt_rand(), true)), 0, 8));
    $user->role_id = $user_role->id;
    $user->status = $_POST['status'];
    $user->input_date = $date->format('Y-m-d H:i:s');
    $user->input_by = Auth::User()->email;
    $user->update_date = $date->format('Y-m-d H:i:s');
    $user->update_by = Auth::User()->email;

    try {
      $success = $user->save();
      $message = 'Create new user is success!';
    } catch (Exception $ex) {
      $success = false;
      $message = $ex->getMessage();
    }

    return response()->json(['success'=>$success,'message'=>$message]);
  }

  //Update Existing User
  public function update(){
    $date = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s')); //initialize date parameter
    $user = UserModel::where(['id'=>$_POST['id']])->first();

    $user->email = $_POST['email'];
    $user->status = $_POST['status'];
    $user->update_date = $date->format('Y-m-d H:i:s');
    $user->update_by = Auth::User()->email;

    try {
      $success = $user->save();
      $message = 'Edit user is success!';
    } catch (Exception $ex) {
      $success = false;
      $message = $ex->getMessage();
    }

    return response()->json(['success'=>$success,'message'=>$message]);
  }

  //Destroy Existing User
  public function destroy(){
    try {
      $success = UserModel::destroy($_POST['id']);
      $message = 'Delete user is success!';
    } catch (Exception $ex) {
      $success = false;
      $message = $ex->getMessage();
    }

    return response()->json(['success'=>$success,'message'=>$message]);
  }
}