<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\UserRole as UserRoleModel;
use DateTime;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use DB;

class AuthController extends Controller {

    /**
     * the model instance
     * @var User
     */
    protected $user;
    /**
     * The Guard implementation.
     *
     * @var Authenticator
     */
    protected $auth;

    /**
     * Create a new authentication controller instance.
     *
     * @param  Authenticator  $auth
     * @return void
     */
    public function __construct(Guard $auth, User $user)
    {
        $this->user = $user;
        $this->auth = $auth;

        $this->middleware('guest', ['except' => ['getLogout']]);
    }

    /**
     * Show the application registration form.
     *
     * @return Response
     */
    public function getRegister()
    {
        return view('signup');
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  RegisterRequest  $request
     * @return Response
     */
    public function postRegister(RegisterRequest $request)
    {
        $role = UserRoleModel::where(['name'=>'customer'])->first();
        $date = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s')); //initialize date parameter
        DB::beginTransaction();

        $this->user->email = $request->email;
        $this->user->password = Hash::make($request->password);
        $this->user->role_id = $role->id;
        $this->user->input_date = $date->format('Y-m-d H:i:s');
        $this->user->input_by = 'Self Registration';
        $this->user->update_date = $date->format('Y-m-d H:i:s');
        $this->user->update_by = 'Self Registration';

        try {
          $success = $this->user->save();
          $message = "Registrasi Berhasil Silahkan Login Menggunakan User Anda.";
        } catch (Exception $ex) {
          DB::rollback();
          $success = false;
          $message = $ex->getMessage();
        }

        if($success){
          DB::commit();
        }

        return \Redirect::route('login')->withErrors([
            'email' => $message,
        ]);
    }

    /**
     * Show the application login form.
     *
     * @return Response
     */
    public function getLogin()
    {
        return view('signin');
    }

    /**
     * Handle a login request to the application.
     *
     * @param  LoginRequest  $request
     * @return Response
     */
    public function postLogin(LoginRequest $request)
    {
        $login_info = $request->only('email', 'password');
        $login_info['status'] = 'active';
        if ($this->auth->attempt($login_info))
        {
            if($this->auth->user()->userRole->name=="Customer"){
              return redirect('/');
            }else if($this->auth->user()->userRole->name=="Administrator"){
              return \Redirect::route('administrator_home');
            }else if($this->auth->user()->userRole->name=="Telesales"){
              return \Redirect::route('telesales_home');
            }else if($this->auth->user()->userRole->name=="Digital & IOT"){
              return \Redirect::route('digitaliot_home');
            }
        }

        return \Redirect::route('login')->withErrors([
            'email' => 'Maaf user/password salah. Silahkan coba kembali.',
        ]);
    }

    /**
     * Log the user out of the application.
     *
     * @return Response
     */
    public function getLogout()
    {
        $this->auth->logout();

        return \Redirect::route('login');
    }
}
