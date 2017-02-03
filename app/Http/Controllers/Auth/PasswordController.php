<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\ResetPassword;
use DateTime;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Auth\ForgetPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use Mail;
use DB;
//use Illuminate\Foundation\Auth\ResetsPasswords;

class PasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    //use ResetsPasswords;

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

        $this->middleware('guest');
    }

    /**
     * Show the application forget password form.
     *
     * @return Response
     */
    public function getForgetPassword()
    {
        return view('forget_password');
    }

    /**
     * Handle a forget password request to the application.
     *
     * @param  ForgetPasswordRequest  $request
     * @return Response
     */
    public function postForgetPassword(ForgetPasswordRequest $request)
    {
        $date = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s')); //initialize date parameter
        $email = $request->only('email');

        $user = User::where(['email'=>$email,'status'=>'active'])->first();

        if(isset($user)){
          DB::beginTransaction();
          $success = true;
          $reset_password = ResetPassword::where(['user_email'=>$user->email,'status'=>'active'])->first();

          if(isset($reset_password)){
            $reset_password->status = "inactive";
            $reset_password->update_date = $date->format('Y-m-d H:i:s');
            $reset_password->update_by = 'New Request Reset Password';

            try {
              $success = $reset_password->save();
            } catch (Exception $ex) {
              DB::rollback();
              $success = false;
              $message = $ex->getMessage();
            }
          }

          if($success){
            $reset_password = new ResetPassword;

            $reset_password->user_email = $user->email;
            $reset_password->token = csrf_token();
            $reset_password->status = "active";
            $reset_password->input_date = $date->format('Y-m-d H:i:s');
            $reset_password->input_by = 'Self Reset Password';
            $reset_password->update_date = $date->format('Y-m-d H:i:s');
            $reset_password->update_by = 'Self Reset Password';

            try {
              $success = $reset_password->save();
            } catch (Exception $ex) {
              DB::rollback();
              $success = false;
              $message = $ex->getMessage();
            }
          }

          if($success){
            DB::commit();
          }else{
            return \Redirect::route('forget_password')->withErrors([
                'email' => $message,
            ]);
          }

          $link = \URL::route('reset_password').'?'.'id='.$reset_password->id.'&'.'token='.$reset_password->token;

          Mail::send('emails.reset_password', ['link'=>$link], function($msg) use ($user) {
            $msg->from('administrator-'.str_replace(' ','_',strtolower(config('settings.app_name'))).'@smartfren.com', "Administrator - ".config('settings.app_name'));
            $msg->to($user->email)->subject('Reset Password');
          });

          return \Redirect::route('login')->withErrors([
              'email' => 'Harap periksa Email anda untuk link Reset Password.',
          ]);
        }

        return \Redirect::route('login')->withErrors([
            'email' => 'Maaf user/password salah. Silahkan coba kembali.',
        ]);
    }

    /**
     * Show the application reset password form.
     *
     * @return Response
     */
    public function getResetPassword()
    {
        $date = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s')); //initialize date parameter
        $reset_password = ResetPassword::where(['id'=>$_REQUEST['id'],'token'=>$_REQUEST['token'],'status'=>'active'])->whereRaw('input_date + INTERVAL 2 HOUR >="'.$date->format('Y-m-d H:i:s').'"')->first();

        if(isset($reset_password)){
          return view('reset_password',['id'=>$reset_password->id,'email'=>$reset_password->user_email]);
        }

        abort(404, 'Not Found');
    }

    /**
     * Handle a reset password request to the application.
     *
     * @param  ResetPasswordRequest  $request
     * @return Response
     */
    public function postResetPassword(ResetPasswordRequest $request)
    {
        DB::beginTransaction();
        $user = User::where(['email'=>$request->email])->first();
        $date = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s')); //initialize date parameter
        $user->password = Hash::make($request->password);
        $user->update_date = $date->format('Y-m-d H:i:s');
        $user->update_by = 'Self Reset Password';

        try {
          $success = $user->save();
        } catch (Exception $ex) {
          DB::rollback();
          $success = false;
          $message = $ex->getMessage();
        }

        if($success){
          $reset_password = ResetPassword::where(['id'=>$_REQUEST['id'],'user_email'=>$user->email,'status'=>'active'])->first();
          $reset_password->status = "inactive";
          $reset_password->update_date = $date->format('Y-m-d H:i:s');
          $reset_password->update_by = 'Self Reset Password';

          try {
            $success = $reset_password->save();
            $message = 'Reset Password Sukses, Silahakan login menggunakan password baru.';
          } catch (Exception $ex) {
            DB::rollback();
            $success = false;
            $message = $ex->getMessage();
          }
		  
		  if($success){
			  DB::commit();
		  }else{
			  DB::rollback();
		  }
        }

        return \Redirect::route('login')->withErrors([
            'email' => $message,
        ]);
    }
}
