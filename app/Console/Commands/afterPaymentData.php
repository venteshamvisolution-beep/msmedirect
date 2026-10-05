<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Response;
use App\Mail\SendMailData;
use Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
class afterPaymentData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'afterPaymentData:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try{
          Log::emergency('Message');  
          $connectionMails = DB::table('connections')
                            ->where('status',3)
                            ->where('from_payment_status', true)
                            ->where('conn_payment_status', true)
                            ->where(function ($query) {
                                $query->whereNull('conn_mail_status')
                                      ->orWhereNull('from_mail_status');
                            })
                            ->get()
                            ->toArray();
            
            foreach($connectionMails as $connectionMail){
                if($connectionMail->from_mail_status == ''){
                    $user = DB::table('users')->whereId($connectionMail->from_id)->get()->toArray();
                    $user = (array) current($user);
                    $users = [];
                    
                    
                    $userData = DB::table('user_data')->whereId($connectionMail->conn_user_data)->get()->toArray();
                    $userData = (array) current($userData);
                    $users['user_name'] = $user['name'];
                    $users['name'] = $userData['organization_name'];
                    $users['email'] = $userData['email'];
                    $users['pincode'] = $userData['pincode'];
                    $users['contact_no'] = $userData['contact_no'];
                    $users['city'] = $userData['city'];
                    $users['state'] = $userData['state'];
                    $users['business_description'] = $userData['business_description'];
                    
                    $users['msme_name'] = DB::table('users')->whereId($connectionMail->conn_id)->value('name');
                    
                    Mail::to($user['email'])->send(new SendMailData($users));
                    Mail::to('gilchrist.fiveonline@gmail.com')->send(new SendMailData($users));
                    
                     $notification = [];
       
                    $notification['user_id'] = $connectionMail->from_id;
                    $notification['notification_id'] = 5;
                    $notification['from_notification'] = $connectionMail->from_id;
                    $notification['conn_notification'] = $connectionMail->conn_id;
                    $notification['is_from_conn'] = 'from';
                    $count = DB::table('front_notifications')->where($notification)->count();
                    $notification['type'] = 'text';
                    $notification['is_mail'] = true;
                    $notification['is_mail_sent'] = false;
                    $notification['created_at'] = Carbon::now();
                    
                    
                    if($count == 0){
                        DB::table('front_notifications')->insert($notification);
                        
                    }
                    
                    DB::table('connections')->whereId($connectionMail->id)->update(['from_mail_status'=>1]);
                    
                }
                
                if($connectionMail->conn_mail_status == ''){
                    $user = DB::table('users')->whereId($connectionMail->conn_id)->get()->toArray();
                     $user = (array) current($user);
                     $users = [];
                    
                    
                    $userData = DB::table('user_data')->whereId($connectionMail->from_user_data)->get()->toArray();
                    $userData = (array) current($userData);
                    $users['user_name'] = $user['name'];
                    $users['name'] = $userData['organization_name'];
                    $users['email'] = $userData['email'];
                    $users['pincode'] = $userData['pincode'];
                    $users['contact_no'] = $userData['contact_no'];
                    $users['city'] = $userData['city'];
                    $users['state'] = $userData['state'];
                    $users['business_description'] = $userData['business_description'];
                    
                    $users['msme_name'] = DB::table('users')->whereId($connectionMail->from_id)->value('name');
                    
                    
                    Mail::to($user['email'])->send(new SendMailData($users));
                    
                    
                    
                    
                    // Mail::to($user['email'])->send(new SendMailData($users));
                    
                     $notification = [];
       
                    $notification['user_id'] =$connectionMail->conn_id;
                    $notification['notification_id'] = 4;
                    $notification['from_notification'] = $connectionMail->from_id;
                    $notification['conn_notification'] = $connectionMail->conn_id;
                    $notification['is_from_conn'] = 'conn';
                    $count = DB::table('front_notifications')->where($notification)->count();
                    $notification['type'] = 'text';
                    $notification['is_mail'] = true;
                    $notification['is_mail_sent'] = false;
                    $notification['created_at'] = Carbon::now();
                    
                    
                    if($count == 0){
                        DB::table('front_notifications')->insert($notification);
                        
                    }
                    DB::table('connections')->whereId($connectionMail->id)->update(['conn_mail_status'=>1]);
                    
                    
                }
                
            }
        
        }catch (\Exception $e) {
            return Response::json(['error' =>$e->getMessage()], 404);
        }
    }
}
