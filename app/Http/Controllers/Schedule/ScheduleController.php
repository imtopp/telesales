<?php

namespace App\Http\Controllers\Schedule;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesResources;
use App\Models\ProductFgCode as ProductFgCodeModel;
use App\Models\User as UserModel;
use App\Models\Transaction as TransactionModel;
use App\Models\TransactionStatus as TransactionStatusModel;
use DB;
use DateTime;
use Mail;

class ScheduleController extends BaseController
{
  use AuthorizesRequests, AuthorizesResources, DispatchesJobs, ValidatesRequests;

  public function notifyExpiredTransaction(){
    $date = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s')); //initialize date parameter
    $key = "t3rs3r@h";
    $success = true;
    $message = "Notification to customer is success";
    $transactions = TransactionModel::select('transaction.*',DB::raw('DATE_FORMAT(transaction.input_date + INTERVAL 1 DAY,"%Y-%m-%d 23:59:59") AS expired_date'))
                                    ->leftJoin(DB::raw('(
                                      SELECT transaction_status.*
                                      FROM (
                                        SELECT transaction_status.transaction_id,MAX(transaction_status.input_date) AS input_date
                                        FROM transaction_status
                                        GROUP BY transaction_status.transaction_id
                                      ) recent
                                      LEFT JOIN transaction_status ON transaction_status.transaction_id=recent.transaction_id AND transaction_status.input_date=recent.input_date
                                      ) transaction_status'),'transaction.id','=','transaction_status.transaction_id')
                                    ->whereRaw('transaction.payment_method = "virtual account BSM" AND transaction_status.status = "Order Received" AND transaction.input_date + INTERVAL 24 HOUR <= CURRENT_TIMESTAMP')
                                    ->get();

    foreach($transactions as $transaction){
      try{
        $success = Mail::send('schedule.emails.transaction_notification_customer_payment_warning', ["product"=>$transaction->product_name." - ".$transaction->product_colour,"date_created"=>$transaction->input_date,"date_expired"=>$transaction->expired_date], function($msg) use ($transaction,$key) {
          $msg->from('administrator-'.str_replace(' ','_',strtolower(config('settings.app_name'))).'@smartfren.com', "Administrator - ".config('settings.app_name'));
          $msg->to($this->decrypt($key,$transaction->customer_email), $this->decrypt($key,$transaction->customer_name))->subject('Transaction notifications');
        });
      } catch (Exception $ex) {
        $success = false;
        $message = $ex->getMessage();
        break;
      }
    }

    return response()->json(["success"=>$success,"message"=>$message]);
  }

  public function updateExpiredTransaction(){
    $date = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s')); //initialize date parameter
    $success = true;
    $key = "t3rs3r@h";
    DB::beginTransaction();
    $transactions = TransactionModel::select('transaction.*')
                                    ->leftJoin(DB::raw('(
                                      SELECT transaction_status.*
                                      FROM (
                                        SELECT transaction_status.transaction_id,MAX(transaction_status.input_date) AS input_date
                                        FROM transaction_status
                                        GROUP BY transaction_status.transaction_id
                                      ) recent
                                      LEFT JOIN transaction_status ON transaction_status.transaction_id=recent.transaction_id AND transaction_status.input_date=recent.input_date
                                      ) transaction_status'),'transaction.id','=','transaction_status.transaction_id')
                                    ->whereRaw('transaction.payment_method = "virtual account BSM" AND transaction_status.status = "Order Received" AND transaction.input_date + INTERVAL 24 HOUR <= CURRENT_TIMESTAMP')
                                    ->get();

    foreach($transactions as $transaction){
      if($success){
        $transaction_status = new TransactionStatusModel;

        $transaction_status->transaction_id = $transaction->id;
        $transaction_status->status = "Order Expired";
        $transaction_status->input_date = $date->format('Y-m-d H:i:s');
        $transaction_status->input_by = "Auto Update";
        $transaction_status->update_date = $date->format('Y-m-d H:i:s');
        $transaction_status->update_by = "Auto Update";

        try {
          $success = $transaction_status->save();
          $message = 'Update transaction success!';
        } catch (Exception $ex) {
          DB::rollback();
          $success = false;
          $message = $ex->getMessage();
          break;
        }
      }
    }

    if($success){
      DB::commit();
    }

    if($success){
      foreach($transactions as $transaction){
        try{
          $success = Mail::send('schedule.emails.transaction_notification_customer_payment_expired', ["product"=>$transaction->product_name." - ".$transaction->product_colour,"date_created"=>$transaction->input_date], function($msg) use ($transaction,$key) {
            $msg->from('administrator-'.str_replace(' ','_',strtolower(config('settings.app_name'))).'@smartfren.com', "Administrator - ".config('settings.app_name'));
            $msg->to($this->decrypt($key,$transaction->customer_email), $this->decrypt($key,$transaction->customer_name))->subject('Transaction notifications');
          });
        } catch (Exception $ex) {
          $success = false;
          $message = $ex->getMessage();
          break;
        }
      }
    }

    return response()->json(["success"=>$success,"message"=>$message]);
  }

  public function notifyLowStock(){
    $date = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s')); //initialize date parameter
    $success = true;
    $message = "Notification low stock is success";
    $list = array();
    $fg_codes = ProductFgCodeModel::select('product_fg_code.fg_code','product_fg_code.stock','product_colour.name AS colour','product.name AS product','product_category.name AS category')
                                    ->leftJoin('product_colour','product_colour.id','=','product_fg_code.product_colour_id')
                                    ->leftJoin('product','product.id','=','product_colour.product_id')
                                    ->leftJoin('product_category','product_category.id','=','product.category_id')
                                    ->whereRaw('product_fg_code.status = "active" AND product_fg_code.stock <= product_fg_code.min_stock_notif')
                                    ->get();

    foreach($fg_codes as $fg_code){
      $list[] = array("fg_code"=>$fg_code->fg_code,"name"=>$fg_code->category." - ".$fg_code->product." - ".$fg_code->colour,"stock"=>$fg_code->stock);
    }

    $users = UserModel::select('users.email')
                      ->leftJoin('user_roles','user_roles.id','=','users.role_id')
                      ->where(["users.status"=>"active","user_roles.name"=>"Administrator"])
                      ->get();

    foreach($users as $user){
      try{
        $success = Mail::send('schedule.emails.stock_notification_warning', ["list"=>$list], function($msg) use ($user) {
          $msg->from('administrator-'.str_replace(' ','_',strtolower(config('settings.app_name'))).'@smartfren.com', "Administrator - ".config('settings.app_name'));
          $msg->to($user->email, $user->email)->subject('Transaction notifications');
        });
      } catch (Exception $ex) {
        $success = false;
        $message = $ex->getMessage();
      }
    }

    return response()->json(["success"=>$success,"message"=>$message]);
  }

  private function decrypt($key,$encrypted){
    $data = base64_decode($encrypted);
    $iv = substr($data, 0, mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128,MCRYPT_MODE_CBC));

    $decrypted = rtrim(
      mcrypt_decrypt(
        MCRYPT_RIJNDAEL_128,
        hash('sha256',$key,true),
        substr($data, mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC)),
        MCRYPT_MODE_CBC,
        $iv
      ),
      "\0"
    );

    return $decrypted;
  }
}
