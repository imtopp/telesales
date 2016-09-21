<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesResources;
use Illuminate\Support\Facades\Auth;
use App\Models\User as UserModel;
use App\Models\UserRole as UserRoleModel;
use File;
use DateTime;

class MainController extends BaseController
{
  use AuthorizesRequests, AuthorizesResources, DispatchesJobs, ValidatesRequests;

  //public function for showing all product at frontpage
  public function login(){
    return view('login'); //display list_product view with all_category and all_product
  }

  public function userRegistration(){
    $role = UserRoleModel::where(['name'=>'customer'])->first();
    $user = new UserModel;
    $date = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s')); //initialize date parameter

    if(isset($_POST) && count($_POST)!=0){
      $existing_user = UserModel::where(['email'=>$_POST['register_form']['email']])->first();
      if(!isset($existing_user)){
        $user->email = $_POST['register_form']['email'];
        $user->password = hash('sha256',$_POST['register_form']['password'],true);
        $user->role_id = $role->id;
        $user->input_date = $date->format('Y-m-d H:i:s');
        $user->input_by = 'Self Registration';
        $user->input_date = $date->format('Y-m-d H:i:s');
        $user->update_by = 'Self Registration';

        try {
          $success = $user->save();
          $message = 'Registrasi sukses, silahkan Login menggunakan akun yang telah dibuat.';
        } catch (Exception $ex) {
          $success = false;
          $message = $ex->getMessage();
        }
      }else{
        $success = false;
        $message = 'Maaf registrasi tidak berhasil, email tersebut telah terdaftar.<br/>Silahkan gunakan menu lost password apabila anda lupa password.';
      }
    }else{
      $success = false;
      $message = 'Maaf registrasi tidak berhasil, data tidak lengkap.';
    }

    return response()->json(['success'=>$success,'message'=>$message]);
  }

  public function userLogin(){
    $user = UserModel::where(['email'=>$_POST['login_form']['email'],'password'=>hash('sha256',$_POST['login_form']['password'],true)])->first();
    if(Auth::attempt(array('email' => $_POST['login_form']['email'], 'password' => hash('sha256',$_POST['login_form']['password'],true)))){
      $role = UserRoleModel::where(['id'=>$user->role_id])->first();
      $success = true;
      $message = 'Login success!';
    }else{
      $success = false;
      $message = 'Login gagal, user/password tidak sesuai.<br/>Apabila anda belum pernah mendaftar silahkan melakukan registrasi.';
    }
    return response()->json(['success'=>$success,'message'=>$message,'role'=>isset($role)?$role:null]);
  }
}
