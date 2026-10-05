<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Response;
use App\Mail\SendPaymentLink;
use Mail;



class paymentLink extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'paymentLink:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try{
            $paymentMails = DB::table('notifications')->where('link','payment_link')
                ->where('is_mail',true)
                ->where('is_mail_sent',false)
                ->get()->toArray();
            
            foreach($paymentMails as $paymentMail){
                $sendMail = [];
                $sendMail['desc'] = $paymentMail['description'];
                $sendMail['link'] = $paymentMail['link'];
                
                if($paymentMail['link'] == "from"){
                    $email = DB::table('users')->whereId($paymentMail['from_notification'])->get()->toArray();
                    $email = (array) current($email);
                    $sendMail['name'] = $email['name'];
                    Mail::to($email['email'])->send(new SendPaymentLink($sendMail));
                    
                }else{
                    $email = DB::table('users')->whereId($paymentMail['conn_notification'])->get()->toArray();
                    $email = (array) current($email);
                    $sendMail['name'] = $email['name'];
                    Mail::to($request->get('email'))->send(new SendPaymentLink($sendMail));
                }
                DB::table('notifications')->where($paymentMail['id'])->update(['is_mail_sent'=>true]);
                
                
            }
            
        }catch (\Exception $e) {
            return Response::json(['error' =>$e->getMessage()], 404);
        }
    }
}
