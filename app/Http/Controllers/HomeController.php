<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use App\Mail\ContactUsMail;
use App\Mail\ConnectionRequest;
use Mail;
use Session;
use Razorpay\Api\Api;
use App\Models\Pincode;
use App\Models\Coupons;
use Crypt;
use Auth;
use Str;
class HomeController extends Controller
{
    public function saveContactUs(Request $request){
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
            // 'company_name' => 'required|string|max:255',
            'mobile_number' => 'required|numeric|digits:10|regex:/^\d{10}$/',
            'message' => 'required|string',
        ]);
        
        $requestData = $request->all();
        $requestData['created_at'] = Carbon::now();
        unset($requestData['_token']);
        DB::table('contact_us')->insert($requestData);
        // Mail::to('vignesh.fiveonline@gmail.com')->send(new ContactUsMail($requestData));
        return back()->with('message', "Thank you for your message we'll get back to you shortly.");
    }
    
    public function msmeScheme(Request $request){
        $schemes = DB::table('schemes')->select('*','schemes_master.name as category_name')
                                        ->where('schemes_master.is_active',true)
                                        ->leftjoin('schemes_master','schemes_master.id','schemes.category_id');
                                        

        $schemeMaster = DB::table('schemes_master')->where('is_active',true)->pluck('name','id')->toArray();
        $param = $request->input('param');
        if($param != ''){
            $schemes = $schemes->where('schemes.category_id',$param);
        }
        $schemes = $schemes->get()->toArray();
        
        return view('info.scheme')->with('schemes',$schemes)->with('schemeMaster',$schemeMaster)->with('param',$param);
    }
    
    public function notifications(Request $request)
    {
        if(!Auth::check()){
            return redirect()->to('login');
        }
        // Auth::logout();
        
        $notifications = DB::table('front_notifications')->where('user_id',Auth::user()->id)->orderBy('id','desc')->get()->toArray();
        
        
        
         return view('notifications')->with('notifications',$notifications);
    
    }
    
     public function getPincodeData(Request $request)
    {
        $pincode = $request->input('pincode');

        // Validate pincode (you may add more validation rules as needed)
        $request->validate([
            'pincode' => 'required|numeric|digits:6',
        ]);

        // Find the Pincode data from the database
        $pincodeData = Pincode::where('Pincode', $pincode)->first();

        if ($pincodeData) {
            return response()->json([
                'city' => $pincodeData->City,
                'state' => $pincodeData->State,
            ]);
        } else {
            return response()->json(['error' => 'Pincode not found'], 404);
        }
    }
    
    protected function getConnectionCount($connId, $fromId)
    {
        return DB::table('connections')
            ->where('conn_id', $connId)
            ->where('from_id', $fromId)
            ->count();
    }
    
    protected function checkMsmeType_old($applicantEmail, $recipentId)
    {
        $applicantData = DB::table('user_data')->where('approve',true)
                                                    ->where('email',Auth::user()->email)->get()->toArray();
         
        $applicantData = (array) current($applicantData);
                                          
        $businessSize = json_decode($applicantData['business_size'],true);
        $recipentData = DB::table('user_data')->whereId($recipentId);
         
        $recipentData = $recipentData->where(function ($query) use ($businessSize) {
            foreach ($businessSize as $value) {
                $query->orWhereJsonContains('business_size', $value);
            }
        });
        
        $recipentData = $recipentData->get()->toArray();
        // dd($recipentId);       
        $returnArray = ['applicantArray'=>$applicantData,'recipentData'=>$recipentData];
        return $returnArray;
    }
    
    protected function checkMsmeType($applicantEmail, $recipentId)
    {
        $applicantData = DB::table('user_data')->where('approve',true)
                                                    ->where('email',Auth::user()->email)->get()->toArray();
                                                    
        // $connUserId = DB::table('user_data')->where('email',$applicantEmail)->value('id');
        $connUserId = $applicantData[0]->email;
        // dd($applicantData[0]->email);
        $applicantData = (array) current($applicantData);
                                          
        $businessSize = json_decode($applicantData['business_size'],true);
        $recipentData = DB::table('user_data')->whereId($connUserId);
         
        $recipentData = $recipentData->where(function ($query) use ($businessSize) {
            foreach ($businessSize as $value) {
                $query->orWhereJsonContains('business_size', $value);
            }
        });
        
        $recipentData = $recipentData->get()->toArray();
        // dd($recipentData);       
        $returnArray = ['applicantArray'=>$applicantData,'recipentData'=>$recipentData];
        return $returnArray;
    }
    
    protected function timeLineData($status){
        $timeline = [];
        $timeline[0]['status'] =  $status;
        $timeline[0]['created_at'] =  Carbon::now();
        return $timeline;
    }
    
    
    
    
    
    
   public function requestConnection(Request $request, $id)
{
    // 1. If not logged in, store target & redirect to login
    if (!Auth::check()) {
        Session::put('requestConnectionMessage', $id);
        return redirect()->to('login');
    }

    // 2. Decrypt target user_data id
    $userDataId = Crypt::decrypt($id);

    // 3. Resolve target user (recipient)
    $recipientUserData = DB::table('user_data')->where('id', $userDataId)->first();
    if (!$recipientUserData) {
        return redirect()->back()->with('message', 'Invalid MSME selected');
    }

    $recipientUserId = DB::table('users')->where('email', $recipientUserData->email)->value('id');
    if (!$recipientUserId) {
        return redirect()->back()->with('message', 'Recipient user not found');
    }

    $loggedInUserId = Auth::user()->id;

    // 4. Prevent duplicate connections (both directions)
    if (
        $this->getConnectionCount($recipientUserId, $loggedInUserId) >= 1 ||
        $this->getConnectionCount($loggedInUserId, $recipientUserId) >= 1
    ) {
        return redirect()->back()->with('message', 'You have already requested this connection');
    }

    // 5. Fetch applicant (logged-in) user_data
    $applicantUserData = DB::table('user_data')
        ->where('email', Auth::user()->email)
        ->first();

    if (!$applicantUserData) {
        return redirect()->back()->with('message', 'Applicant profile not found');
    }

    // 6. ✅ CORRECT business_size compatibility check
    $applicantSizes = json_decode($applicantUserData->business_size, true) ?? [];
    $recipientSizes = json_decode($recipientUserData->business_size, true) ?? [];

    $sizeMatch = array_intersect($applicantSizes, $recipientSizes);

    if (empty($sizeMatch)) {
        return redirect()->back()->with(
            'message',
            'Sorry, this MSME does not deal with ' . implode(',', $applicantSizes)
        );
    }

    // 7. Create connection
    $connectionData = [
        'conn_id'                  => $recipientUserId,
        'from_id'                  => $loggedInUserId,
        'created_at'               => Carbon::now(),
        'status'                   => 0,
        'conn_user_data'           => $recipientUserData->id,
        'conn_business_type_id'    => $recipientUserData->business_type,
        'from_user_data'           => $applicantUserData->id,
        'from_business_type_id'    => $applicantUserData->business_type,
        'timeline'                 => json_encode($this->timeLineData('Connection Requested')),
    ];

    $connectionId = DB::table('connections')->insertGetId($connectionData);

    // 8. Encode connection IDs
    DB::table('connections')->where('id', $connectionId)->update([
        'conn_encode' => Crypt::encrypt('conn:' . $recipientUserId . ':' . $connectionId),
        'from_encode' => Crypt::encrypt('from:' . $loggedInUserId . ':' . $connectionId),
    ]);

    // 9. Notification data
    $businessCategoryName = DB::table('business_category')
        ->where('id', $applicantUserData->business_category)
        ->value('name');

    $subBusinessCategoryName = DB::table('business_sub_category')
        ->where('id', $applicantUserData->business_sub_category)
        ->value('name');

    DB::table('front_notifications')->insert([
        'user_id'            => $recipientUserId,
        'from_notification'  => $loggedInUserId,
        'conn_notification'  => $recipientUserData->id,
        'notification_id'    => 2,
        'description'        => $businessCategoryName . ' ' . $subBusinessCategoryName . ' has shown interest in your profile',
        'link'               => route('userConnections'),
        'type'               => 'request_connection',
        'is_mail'            => true,
        'is_mail_sent'       => true,
        'created_at'         => Carbon::now(),
    ]);

    // 10. Send email
    $mailData = [
        'name' => $businessCategoryName . ' ' . $subBusinessCategoryName,
        'link' => route('userConnections'),
    ];

    // Mail::to($recipientUserData->email)->send(new ConnectionRequest($mailData));

    return redirect()->back()->with('message', 'Connection Request Processed');
}

    
    
    
    
    public function userConnections(Request $request){
       
        if(!Auth::check()){
            return redirect()->to('login');
        }
        
      
        $connectionRequested = DB::table('connections')->select('connections.status','user_data.business_description','connections.from_encode','connections.created_at as connection_created_at','business_category.name as business_category_name','connections.from_id','connections.conn_id','connections.conn_encode','connections.id as conn_id',)
                                                    ->leftjoin('users','users.id','connections.from_id')
                                                    ->leftjoin('user_data','user_data.email','users.email')
                                                    ->leftjoin('business_category','business_category.id','user_data.business_category')
                                                   ->where(function ($query) {
                                    $query->where('connections.from_id',Auth::user()->id)
                                          ->orWhere('connections.conn_id',Auth::user()->id);
                                })
                                            
                                                    // ->where('user_data.email',Auth::user()->email)
                                                    ->get()->toArray();
         
       
       $connectionRequest = DB::table('connections')->select('connections.status','user_data.business_description','connections.conn_encode','connections.created_at as connection_created_at','business_category.name as business_category_name')
                                                    ->leftjoin('users','users.id','connections.from_id')
                                                    ->leftjoin('user_data','user_data.email','users.email')
                                                    ->leftjoin('business_category','business_category.id','user_data.business_category')
                                                    ->where('connections.status',0)
                                                    ->where('connections.conn_id',Auth::user()->id)
                                                    ->get()->toArray();
                                                    
                                                    
        $msme_type = DB::table('user_data')->select('user_data.msme_type')
                                            ->where('user_data.id',Auth::user()->id)
                                            ->get()->first();
        return view('userConnections')->with('connectionRequests',$connectionRequested)->with('connectionAccept',$connectionRequest)->with('msme_type',$msme_type);
        
    }
    
    public static function sendNotification($connectionArray,$urlType="accept"){
        // $connectionArray = (array) current($connectionArray);
        
        $formUserData = DB::table('user_data')->select('business_sub_category.name as sub_category_name','business_category.name as business_category_name')->leftjoin('business_category','business_category.id','user_data.business_category')
        ->leftjoin('business_sub_category','business_sub_category.id','user_data.business_sub_category')->
            where('user_data.id',$connectionArray['from_user_data'])->get()->toArray();
        
        $fromUserData = (array) current($formUserData);
        
        
        
        
        $notification = [];
        // $url = url('/accept-connection/'.$connectionArray['from_encode']);
        $url = url("//user-connections");
        $notification['user_id'] = $connectionArray['from_id'];
        $notification['notification_id'] = 1;
        $notification['from_notification'] = $connectionArray['from_id'];
        $notification['conn_notification'] = $connectionArray['conn_id'];
        $notification['is_from_conn'] = 'from';
        $count = DB::table('front_notifications')->where($notification)->count();
        
        
        if($urlType == 'accept'){
            $notification['description'] = $fromUserData['business_category_name'].' '.$fromUserData['sub_category_name'].' this is your payment link -:';
            
        $notification['link'] = $url;
        $notification['type'] = 'payment_link';
        }else{
            
            $notification['description'] = $fromUserData['business_category_name'].' '.$fromUserData['sub_category_name'].' Declined';
        
        $notification['link'] = '';
        $notification['type'] = 'declined';
            
        }
        
        
        $notification['is_mail'] = true;
        $notification['is_mail_sent'] = false;
        $notification['created_at'] = Carbon::now();
        
        
        if($count == 0){
            DB::table('front_notifications')->insert($notification);
            
        }
        
        if($urlType == "declined"){
            return true;
        }
        
        
        $connUserData = DB::table('user_data')->select('business_sub_category.name as sub_category_name','business_category.name as business_category_name')->leftjoin('business_category','business_category.id','user_data.business_category')
        ->leftjoin('business_sub_category','business_sub_category.id','user_data.business_sub_category')->
            where('user_data.id',$connectionArray['conn_user_data'])->get()->toArray();
        
        $connUserData = (array) current($connUserData);
        
        
        $notification = [];
        // $url = url('/accept-connection/'.$connectionArray['conn_encode']);
        $url = url("//user-connections");
        $notification['user_id'] = $connectionArray['conn_id'];
        $notification['notification_id'] = 1;
        $notification['from_notification'] = $connectionArray['from_id'];
        $notification['conn_notification'] = $connectionArray['conn_id'];
        $notification['is_from_conn'] = 'conn';
        $count = DB::table('front_notifications')->where($notification)->count();
    //   $notification['description'] = $fromUserData['business_category_name'].' '.$fromUserData['sub_category_name'].' this is your payment link -:';
       
       if($urlType == 'accept'){
            $notification['description'] = $fromUserData['business_category_name'].' '.$fromUserData['sub_category_name'].' this is your payment link -:';
            
        $notification['link'] = $url;
        $notification['type'] = 'payment_link';
        }else{
            $notification['description'] = $fromUserData['business_category_name'].' '.$fromUserData['sub_category_name'].' Declined';
        
        $notification['link'] = '';
        $notification['type'] = 'text';
            
        }
        $notification['is_mail'] = true;
        $notification['is_mail_sent'] = false;
        $notification['created_at'] = Carbon::now();
        
        
        if($count == 0){
            DB::table('front_notifications')->insert($notification);
            
        }
        
        return true;
        
        
    }
    
    public function declineConnection(Request $request,$id){
        try{
            if(!Auth::check()){
                return redirect()->to('login');
            }
            
            $decryptString = Crypt::decrypt($id);
            $decrypt = explode(':',$decryptString);
            
            if(count($decrypt) != 3){
                return abort(403);
            }
            
            $connectionArray = DB::table('connections')
                                        ->where('conn_encode',$id)
                                        
                                        ->whereId($decrypt[2])
                                        ->where('status',0)
                                         ->get()->toArray();
            
            if(count($connectionArray) < 1){
                return abort(403);
            }
            $connectionArray = (array) current($connectionArray);
            Self::sendNotification($connectionArray,'declined');
            $timeline = json_decode($connectionArray['timeline'],true);
            $newTimeLine = [];
            $newTimeLine = ['status'=>'Connection Declined','created_at'=>Carbon::now()];
            
            
            array_push($timeline,$newTimeLine);
            
            DB::table('connections')->where('conn_encode',$id)
                                        ->update(['status'=>2]);
                
            return back()->with('message', "You have declined a connection request");
                                          
        }catch (\Exception $e) {
            return $e->getMessage();
        }
            
    }
    
    public function acceptConnection(Request $request, $id, $coupon_discount = 0)
{
    try {
        if (!Auth::check()) {
            return redirect()->to('login');
        }

        $decryptString = Crypt::decrypt($id);
        $decrypt = explode(':', $decryptString);

        if (count($decrypt) !== 3 || !in_array($decrypt[0], ['conn', 'from'])) {
            abort(403, 'Invalid connection token');
        }

        $connectionId = $decrypt[2];

        $connection = DB::table('connections')
            ->where('id', $connectionId)
            ->whereIn('status', [0, 1, 4, 5])
            ->first();

        if (!$connection) {
            return redirect()->back()->with('message', 'Invalid or expired connection request');
        }

        if (Auth::user()->id != $connection->{$decrypt[0] . '_id'}) {
            abort(403);
        }

        $timeline = json_decode($connection->timeline, true) ?? [];
        $timeline[] = [
            'status' => 'Connection Approved',
            'created_at' => Carbon::now(),
        ];

        $msmeType = DB::table('user_data')
            ->whereId($connection->{$decrypt[0] . '_user_data'})
            ->value('msme_type');

        $Amount = (float) DB::table('charges')
            ->where('name', $msmeType)
            ->value('price');

        $coupon_discount = (float) $coupon_discount;
        if ($coupon_discount < 0 || $coupon_discount > $Amount) {
            $coupon_discount = 0;
        }

        $totalAmount = max(0, $Amount - $coupon_discount);
        
        // echo "<pre>";print_r($connection);
        // echo "<pre>";print_r($decrypt);exit;

        if ($connection->{$decrypt[0] . '_payment_status'} == false) {

            // HDFC SmartGateway Order Creation
            $hdfcOrderId = 'ORD' . substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 15);
            $customerId = 'CUST' . Auth::user()->id;

            $payload = [
                'order_id' => $hdfcOrderId,
                'amount' => number_format($totalAmount, 2, '.', ''),
                'customer_id' => $customerId,
                'customer_email' => Auth::user()->email,
                'customer_phone' => Auth::user()->contact_no,
                'return_url' => route('payment'),
                'description' => 'Connection Payment',
                'udf1' => $id, // Using udf1 to pass the encrypted ID
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, config('services.hdfc.base_url') . '/orders');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));

            $verifySsl = config('services.hdfc.verify_ssl');
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $verifySsl);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $verifySsl ? 2 : 0);

            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Basic ' . base64_encode(config('services.hdfc.api_key') . ':'),
                'x-merchantid: ' . config('services.hdfc.merchant_id'),
                'Content-Type: application/x-www-form-urlencoded',
                'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                'Accept: application/json, text/plain, */*',
                'Connection: keep-alive'
            ]);

            $responseBody = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            $paymentLink = '';
            if ($httpCode >= 200 && $httpCode < 300) {
                $sessionData = json_decode($responseBody, true);
                if (isset($sessionData['payment_links']['web'])) {
                    $paymentLink = $sessionData['payment_links']['web'];
                }
            } else {
                \Log::error('HDFC Order Creation Failed', ['response' => $responseBody, 'error' => $curlError]);
                return redirect()->back()->with('message', 'Payment gateway initialization failed. Please try again.');
            }

            DB::table('connections')
                ->where('id', $connectionId)
                ->update([
                    $decrypt[0] . '_hdfc_order_id' => $hdfcOrderId,
                    'is_acceptance' => true,
                    'status' => 1,
                    'timeline' => json_encode($timeline),
                ]);

            self::sendNotification((array) $connection);

            $userData = DB::table('user_data')
                ->select('organization_name', 'email', 'contact_no')
                ->where('email', Auth::user()->email)
                ->first();

            return view('acceptConnection', [
                'order_id' => $hdfcOrderId,
                'payment_link' => $paymentLink,
                'userData' => $userData,
                'amount' => $totalAmount,
            ]);
        }

        return redirect()->back()->with('message', 'Payment already completed');

    } catch (\Exception $e) {
        \Log::error('AcceptConnection Exception: ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());
        return redirect()->back()->with('message', 'Something went wrong. Please try again.');
    }
}

    
    public function payment(Request $request){
        $orderId = $request->input('order_id');
        
        if (!$orderId) {
            return redirect()->to('/user-connections')->with('message', 'Invalid Payment Response');
        }

        // Server-to-Server call to check order status
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, config('services.hdfc.base_url') . '/orders/' . $orderId);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $verifySsl = config('services.hdfc.verify_ssl');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $verifySsl);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $verifySsl ? 2 : 0);

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Basic ' . base64_encode(config('services.hdfc.api_key') . ':'),
            'x-merchantid: ' . config('services.hdfc.merchant_id'),
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'Accept: application/json, text/plain, */*',
            'Connection: keep-alive'
        ]);

        $responseBody = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        $status = false;
        $amount = 0;
        $paymentStatusMessage = 'Payment Failed or Pending';
        $hdfcPaymentId = null;

        if ($httpCode >= 200 && $httpCode < 300) {
            $orderStatusData = json_decode($responseBody, true);
            $apiStatus = $orderStatusData['status'] ?? '';
            $amount = $orderStatusData['amount'] ?? 0;
            $hdfcPaymentId = $orderStatusData['txn_id'] ?? ($orderStatusData['id'] ?? null);

            // Log raw response
            DB::table('payment_logs')->insert([
                'order_id' => $orderId,
                'payment_id' => $hdfcPaymentId,
                'status' => $apiStatus,
                'amount' => $amount,
                'raw_response' => json_encode($orderStatusData),
                'customer_id' => $orderStatusData['customer_id'] ?? null,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ]);

            if ($apiStatus === 'CHARGED' || $apiStatus === 'SUCCESS') {
                $status = true;
                $paymentStatusMessage = 'Payment Successfully Completed';

                $orderDataDB = DB::table('connections')
                    ->where('conn_hdfc_order_id', $orderId)
                    ->orWhere('from_hdfc_order_id', $orderId)
                    ->first();

                if ($orderDataDB) {
                    $orderDataDB = (array) $orderDataDB;
                    $connOrFrom = ($orderDataDB['conn_hdfc_order_id'] == $orderId) ? 'conn' : 'from';

                    if ($orderDataDB[$connOrFrom.'_payment_status'] == false) {
                        $updateData = [];
                        $updateData[$connOrFrom.'_hdfc_payment_id'] = $hdfcPaymentId;
                        
                        if($connOrFrom == "conn"){
                            $newTimeLine = ['status'=>'Applicant Payment Done', 'created_at'=>\Carbon\Carbon::now()];
                            $newStatus = ($orderDataDB['from_payment_status'] == true) ? 3 : 5;
                        }else{
                            $newTimeLine = ['status'=>'Recipent Payment Done', 'created_at'=>\Carbon\Carbon::now()];
                            $newStatus = ($orderDataDB['conn_payment_status'] == true) ? 3 : 4;
                        }
                        
                        $updateData['status'] = $newStatus;
                        $updateData[$connOrFrom.'_payment_status'] = true;
                        $updateData['updated_at'] = \Carbon\Carbon::now();
                        
                        $timeline = json_decode($orderDataDB['timeline'], true);
                        if (!is_array($timeline)) $timeline = [];
                        array_push($timeline, $newTimeLine);
                        $updateData['timeline'] = json_encode($timeline);
                        
                        DB::table('connections')->where('id', $orderDataDB['id'])->update($updateData);
                    }
                }
            }
        }

        // Return real-time response page as per Bank Testing requirements
        return view('paymentResponse', [
            'order_id' => $orderId,
            'amount' => $amount,
            'message' => $paymentStatusMessage,
            'success' => $status
        ]);
    }
    
    public function checkout($from_id,$msme_type){
        if(!Auth::check()){
            return redirect()->to('login');
        }
        
        $msme_type = base64_decode($msme_type);
        $connectionRequested = DB::table('connections')->select('connections.status','user_data.business_description','connections.from_encode','connections.created_at as connection_created_at','business_category.name as business_category_name','connections.from_id','connections.conn_id','connections.conn_encode')
                                                    ->leftjoin('users','users.id','connections.from_id')
                                                    ->leftjoin('user_data','user_data.email','users.email')
                                                    ->leftjoin('business_category','business_category.id','user_data.business_category')
                                                    ->where('connections.from_id',$from_id)
                                                    ->whereIn('connections.status', [1, 5])
                                                    ->get()->first();
        
        if (!$connectionRequested) {
            return redirect()->to('user-connections')->with('error', 'Invalid or expired connection request for checkout.');
        }

        // dd($connectionRequested) ; 
        $msme_data_price = DB::table('charges')->select('charges.price','charges.name')
                                        ->where('charges.name', 'like', '%'.$msme_type.'%')
                                        ->get()->first();
        return view('info.checkout')->with('connectionRequest',$connectionRequested)->with('msme_price' ,$msme_data_price);
            
    }
    
    public function checkCoupon(Request $request){
        $coupon_code = $request->coupon;
        $data = Coupons::select('coupons.discount_percentage')->where('coupons.coupon_code',$coupon_code)->where('coupons.status','Active')->first();
        if(isset($data)){
             return response()->json(['code'=> 1, 'result' => $data]);
        }else{
            return response()->json(['code'=>0, 'result' => '']);
        }
       
    }
    

}