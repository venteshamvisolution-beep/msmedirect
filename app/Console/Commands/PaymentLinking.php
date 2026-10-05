<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Response;
use App\Mail\SendPaymentLink;
use App\Mail\DeclineConnectionRequest;
use Mail;

class PaymentLinking extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'paymentLinking:cron';

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
            
            $paymentMails = DB::table('front_notifications')->whereIn('type',['payment_link','declined'])
                ->where('is_mail',true)
                ->where('is_mail_sent',false)
                ->get()->toArray();
            
            
            foreach($paymentMails as $paymentMail){
                $paymentMail = (array) $paymentMail;
                $sendMail = [];
                $sendMail['desc'] = $paymentMail['description'];
                $sendMail['link'] = $paymentMail['link'];
                
                if($paymentMail['is_from_conn'] == "from"){
                    $email = DB::table('users')->whereId($paymentMail['from_notification'])->get()->toArray();
                    $email = (array) current($email);
                    $sendMail['name'] = $email['name'];
                    if($paymentMail['type'] == "declined"){
                        Mail::to($email['email'])->send(new DeclineConnectionRequest($sendMail));
                    }else{
                        
                    Mail::to($email['email'])->send(new SendPaymentLink($sendMail));
                    }
                    
                    
                }else{
                    $email = DB::table('users')->whereId($paymentMail['conn_notification'])->get()->toArray();
                    $email = (array) current($email);
                    $sendMail['name'] = $email['name'];
                    Mail::to($email['email'])->send(new SendPaymentLink($sendMail));
                }
                DB::table('front_notifications')->whereId($paymentMail['id'])->update(['is_mail_sent'=>1]);
                
                
            }
            
        }catch (\Exception $e) {
            return Response::json(['error' =>$e->getMessage()], 404);
        }
    }
}
