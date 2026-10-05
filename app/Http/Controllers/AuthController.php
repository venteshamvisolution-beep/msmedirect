<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use App\Mail\RegisterMail;
use App\Mail\SendForgotPassowrdMail;
// use Mail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Session;
use URL;
use Redirect;
use App\Models\User;
// use Str;
use Auth;
use Hash;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;
use App\Mail\AdminNewRegisterMail;
use Crypt;

class AuthController extends Controller
{

    public function register(Request $request)
    {
        $businessCategory = DB::table('business_category')->get()->toArray();
        $subbusinessCategory = DB::table('business_sub_category')->get()->toArray();

        // 1. If 'id' parameter is passed in the URL (from email link), store it in session
        if ($request->has('id') && !empty($request->get('id'))) {
            $rawId = $request->get('id');
            $decodedId = is_numeric($rawId) ? $rawId : base64_decode($rawId);
            session(['msme_business_data_id' => $decodedId]);
        }

        // 2. Retrieve Business Data ID from session if present
        $businessDataId = session('msme_business_data_id');

        $businessData = null;
        if ($businessDataId) {
            $realId = is_numeric($businessDataId) ? $businessDataId : base64_decode($businessDataId);
            $businessData = DB::table('msme_business_local')->where('id', $realId)->first();
        }

        return view('auth.register')
            ->with("businessCategory", $businessCategory)
            ->with("subbusinessCategory", $subbusinessCategory)
            ->with("businessData", $businessData);
    }


    public function login(Request $request)
    {
        Session::put('url.intended', URL::previous());
        return view('auth.login');
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/login');
    }

    public function userProfile(Request $request)
    {
        $userProfile = DB::table('user_data')->where("email", Auth::user()->email)->get()->toArray();

        $userProfile = current($userProfile);

        $userProfile->business_category = DB::table('business_category')->whereId($userProfile->business_category)->value('name');

        $userProfile->business_sub_category = DB::table('business_sub_category')->whereId($userProfile->business_sub_category)->value('name');

        // echo "<pre>";p/rint_r($userProfile);exit; 
        return view("auth.userProfile")->with('userProfile', $userProfile);
    }


    public function editProfile(Request $request)
    {
        $userProfile = DB::table('user_data')->where("email", Auth::user()->email)->get()->toArray();

        $userProfile = current($userProfile);

        // $userProfile->business_category = DB::table('business_category')->whereId($userProfile->business_category)->value('name');

        // $userProfile->business_sub_category = DB::table('business_sub_category')->whereId($userProfile->business_sub_category)->value('name');

        $businessCategory = DB::table('business_category')->get()->toArray();
        $subbusinessCategory = DB::table('business_sub_category')->get()->toArray();

        // echo "<pre>";p/rint_r($userProfile);exit; 
        return view("auth.editProfile")->with('userProfile', $userProfile)
            ->with('businessCategory', $businessCategory)->with('subbusinessCategory', $subbusinessCategory)
        ;
    }

    public function updateRegister(Request $request)
    {

        $rules = [
            'organization_name' => 'required|string|max:255',
            'business_type' => 'required|integer',
            'type_organization' => 'required|string|max:255',
            'msme_type' => 'required|in:Startup Business,Micro Business,Small Business,Medium Business,Beyond MSME Business',
            'business_category' => 'required|integer|exists:business_category,id',
            'business_sub_category' => [
                Rule::requiredIf(function () use ($request) {
                    return $request->business_type == 0;
                }),
                'integer',
                'exists:business_sub_category,id',
            ],
            'specialization' => 'required|string|max:255',
            'contact_no' => [
                'required',
                'string',
                'max:15',
                'regex:/^[0-9]{10,15}$/'
            ],
            'email' => [
                'sometimes',
                'required',
                'email',
                'max:255',
                Rule::unique('users')->ignore($request->user()->id),
                Rule::unique('user_data')->ignore($request->get('addEdit')),
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/'
            ],
            'pincode' => [
                'required',
                'string',
                'max:10',
                'regex:/^[1-9][0-9]{5}$/'
            ],
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'business_size' => 'required|array',
            'business_size.*' => 'in:startup,micro,small,medium,beyond-sme',
            'experience' => 'required|integer',
            'document_no' => 'required|string|max:255',
            'state_city' => 'required|string|max:255',
            'document_file' => 'sometimes|array',
            'document_file.*' => 'sometimes|file|mimes:png,jpg,jpeg,pdf|max:2048'
        ];

        $request->validate($rules, [
            'business_size.*.in' => 'The selected business size option is invalid.',
            'msme_type.in' => 'The selected MSME type is invalid.',
        ]);

        $businessType = $request->get('business_type');
        $typeOrganizationName = $request->get('type_organization');
        $msmeType = $request->get('msme_type');
        $businessCategory = $request->get('business_category');
        $businessSubCategory = $request->get('business_sub_category');
        $experience = $request->get('experience');
        $specialization = $request->get('specialization');
        $city = $request->get('city');
        $state = $request->get('state');
        $stateCity = $request->get('state_city');

        $businessCategory = DB::table('business_category')->whereId($businessCategory)->value('name');

        $businessSubCategory = DB::table('business_sub_category')->whereId($businessSubCategory)->value('name');


        if ($businessType == 0) {
            $txt = 'We are a <b>' . $typeOrganizationName . '</b> and a <b>' . $msmeType . '</b> MSME firm in <b>' . $businessSubCategory . '</b><b> ' . $businessCategory . '</b> with a <b>' . $experience . '</b> years of experience and specialization in <b>' . $specialization . '</b> operating from <b>' . $city . '</b>, <b>' . $state . '</b> and we also provide service to <b>' . $stateCity . '.</b>';
        } else {


            $txt = 'We are a <b>' . $typeOrganizationName . '</b> and a <b>' . $msmeType . '</b> MSME firm in <b>' . $businessCategory . '</b> with a <b>' . $experience . '</b> years of experience and specialization in <b>' . $specialization . '</b> operating from <b>' . $city . '</b>, <b>' . $state . '</b> and we also provide service to <b>' . $stateCity . '.</b>';
        }



        $requestData = $request->all();
        $requestData['business_description'] = $txt;
        $documentFile = [];
        if ($request->hasFile('document_file')) {
            foreach ($request->file('document_file') as $index => $file) {
                $originalFilename = $file->getClientOriginalName(); // Get the 
                $encryptedFilename = Str::random(10) . '_' . $originalFilename;
                $path = $file->storeAs('userdata', $encryptedFilename);
                array_push($documentFile, $path);
            }
        }
        $requestData['business_size'] = json_encode($requestData['business_size']);

        $oldData = DB::table('user_data')->where('email', $request->get('email'))->get()->toArray();

        $oldData = (array) current($oldData);

        unset($oldData['created_at']);
        unset($oldData['updated_at']);
        unset($oldData['remark']);
        unset($oldData['approve']);
        unset($oldData['status']);
        unset($oldData['id']);
        unset($requestData['addEdit']);
        unset($requestData['_token']);





        if (count($documentFile) >= 1) {
            $requestData['document_file'] = json_encode($documentFile);
            $newRequestData = $requestData;
            $result = array_diff_assoc($newRequestData, $oldData);
            $result = array_merge($oldData, $result);
            $result['created_at'] = Carbon::now();
            $result['updated_at'] = Carbon::now();
            $result['approve'] = 0;


            $countUser = DB::table('new_user_data')->where('email', Auth::user()->email);



            if ($countUser->count() >= 1) {
                $countUser->update($result);
            } else {
                DB::table('new_user_data')->insert($result);
            }

            DB::table('user_data')->where('email', $request->get('email'))->update(['status' => 'pending']);
        } else {
            $documentFile = $oldData['document_file'];
            unset($oldData['document_file']);
            $newRequestData = $requestData;
            $result = array_diff_assoc($newRequestData, $oldData);
            $result = array_merge($oldData, $result);
            $result['document_file'] = $documentFile;
            $result['created_at'] = Carbon::now();
            $result['updated_at'] = Carbon::now();
            $result['approve'] = 0;



            $countUser = DB::table('new_user_data')->where('email', Auth::user()->email);



            if ($countUser->count() >= 1) {
                $countUser->update($result);
            } else {
                DB::table('new_user_data')->insert($result);
            }

            DB::table('user_data')->where('email', $request->get('email'))->update(['status' => 'pending']);
        }








        session()->flash('message', 'Records Saved');

        return redirect()->back();
    }


    public function saveRegister(Request $request)
    {
        $requestData = $request->all();



        /*
        $request->validate([
            'organization_name' => 'required|string|max:255',
            'business_type' => 'required|integer',
            'type_organization' => 'required|string|max:255',
            'msme_type' => 'required|in:Startup Business,Micro Business,Small Business,Medium Business,Beyond MSME Business',
            'business_category' => 'required',
            'business_sub_category' => [
                Rule::requiredIf(function () use ($request) {
                    return $request->business_type == 0;
                }),
                'nullable', // Makes the field optional when business_type is not 0
                'integer',
                'exists:business_sub_category,id',

            ],
            'specialization' => 'required|string|max:255',
            'contact_no' => [
                'required',
                'string',
                'max:10',
                'regex:/^[0-9]{10}$/' // Ensures it's a numeric string between 10 to 15 digits
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
                'unique:user_data,email',
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/'
            ],
            'pincode' => [
                'required',
                'string',
                'max:10',
                'regex:/^[1-9][0-9]{5}$/'
            ],
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'business_size' => 'required|array',
            'business_size.*' => 'in:startup,micro,small,medium,beyond-sme',
            'experience' => 'required|integer',
            'document_no' => 'required|string|max:255',
            'state_city' => 'required|string|max:255',
            'document_file' => 'required|array',
            'document_file.*' => 'required|file|mimes:png,jpg,jpeg,pdf|max:2048'
        ], [
            'business_size.*.in' => 'The selected business size option is invalid.',
            'msme_type.in' => 'The selected MSME type is invalid.',
        ]);
        */

        $requestData = $request->all();

        $businessType = $requestData['business_type'] ?? null;
        $typeOrganizationName = $requestData['type_organization'] ?? '';
        $msmeType = $requestData['msme_type'] ?? '';
        $businessCategoryInput = $requestData['business_category'] ?? null;
        $businessSubCategoryInput = $requestData['business_sub_category'] ?? null;
        $experience = $requestData['experience'] ?? '';
        $specialization = $requestData['specialization'] ?? '';
        $city = $requestData['city'] ?? '';
        $state = $requestData['state'] ?? '';
        $stateCity = $requestData['state_city'] ?? '';

        $businessSubCategory = DB::table('business_sub_category')->whereId($businessSubCategoryInput)->value('name');

        if (isset($requestData['business_category']) && $requestData['business_category'] == "1") {
            $businessCategory = 'Others -' . ($requestData['others'] ?? '');
        } else {
            $businessCategory = DB::table('business_category')->whereId($businessCategoryInput)->value('name');
        }

        if ($businessType === "0" || $businessType === 0) {
            $txt = 'We are a <b>' . $typeOrganizationName . '</b> and a <b>' . $msmeType . '</b> MSME firm in <b>' . $businessSubCategory . '</b><b> ' . $businessCategory . '</b> with a <b>' . $experience . '</b> years of experience and specialization in <b>' . $specialization . '</b> operating from <b>' . $city . '</b>, <b>' . $state . '</b> and we provide service to <b>' . $stateCity . '.</b>. I want to connect to <b>' . ($requestData['want_to'] ?? '') . '</b>';
        } else {
            $txt = 'We are a <b>' . $typeOrganizationName . '</b> and a <b>' . $msmeType . '</b> MSME firm in <b>' . $businessCategory . '</b> with a <b>' . $experience . '</b> years of experience and specialization in <b>' . $specialization . '</b> operating from <b>' . $city . '</b>, <b>' . $state . '</b> and we provide service to <b>' . $stateCity . '.</b>. I want to connect to <b>' . ($requestData['want_to'] ?? '') . '</b>';
        }

        $requestData['business_description'] = $txt;

        // Fallbacks for non-nullable DB columns when form inputs are empty
        $requestData['organization_name'] = $requestData['organization_name'] ?? '';
        $requestData['business_type'] = (isset($requestData['business_type']) && $requestData['business_type'] !== '') ? (int)$requestData['business_type'] : 0;
        $requestData['type_organization'] = $requestData['type_organization'] ?? '';
        $requestData['msme_type'] = $requestData['msme_type'] ?? '';
        $requestData['business_category'] = (isset($requestData['business_category']) && $requestData['business_category'] !== '') ? (int)$requestData['business_category'] : 0;
        $requestData['business_sub_category'] = (isset($requestData['business_sub_category']) && $requestData['business_sub_category'] !== '') ? (int)$requestData['business_sub_category'] : null;
        $requestData['specialization'] = $requestData['specialization'] ?? '';
        $requestData['contact_no'] = $requestData['contact_no'] ?? '';
        $requestData['email'] = !empty($requestData['email']) ? $requestData['email'] : null;
        $requestData['pincode'] = (isset($requestData['pincode']) && $requestData['pincode'] !== '') ? (int)$requestData['pincode'] : 0;
        $requestData['city'] = $requestData['city'] ?? '';
        $requestData['state'] = $requestData['state'] ?? '';
        $requestData['experience'] = (isset($requestData['experience']) && $requestData['experience'] !== '') ? (int)$requestData['experience'] : 0;
        $requestData['document_no'] = $requestData['document_no'] ?? '';
        $requestData['state_city'] = $requestData['state_city'] ?? '';
        $requestData['want_to'] = $requestData['want_to'] ?? '';
        $requestData['address'] = $requestData['address'] ?? '';
        $requestData['others'] = $requestData['others'] ?? '';

        $businessDataId = $request->get('msme_business_data_id') ?: session('msme_business_data_id');
        if (!empty($businessDataId)) {
            $realId = is_numeric($businessDataId) ? $businessDataId : base64_decode($businessDataId);
            $requestData['msme_business_data_id'] = $realId;
            $requestData['is_bulk_user'] = 1;
        } else {
            $requestData['msme_business_data_id'] = null;
            $requestData['is_bulk_user'] = 0;
        }

        $documentFile = [];
        if ($request->hasFile('document_file')) {
            foreach ($request->file('document_file') as $index => $file) {
                $originalFilename = $file->getClientOriginalName();
                $encryptedFilename = Str::random(10) . '_' . $originalFilename;
                $path = $file->storeAs('userdata', $encryptedFilename);
                array_push($documentFile, $path);
            }
        }
        $requestData['business_size'] = json_encode($requestData['business_size'] ?? []);
        $requestData['document_file'] = json_encode($documentFile);
        $requestData['created_at'] = \Carbon\Carbon::now();
        $requestData['updated_at'] = \Carbon\Carbon::now();
        unset($requestData['_token']);

        $userId = DB::table('user_data')->insertGetId($requestData);

        $recipient = User::find(1);
        $url = url('/admin/user-datas/' . $userId . '/edit');
        Notification::make()
            ->title('New')
            ->success()
            ->body('New Data in User Data')
            ->actions([
                Action::make('view')
                    ->button()
                    ->url($url, shouldOpenInNewTab: true)
            ])
            ->sendToDatabase($recipient);
        $encId = Crypt::encrypt($userId);
        DB::table('user_data')->whereId($userId)->update(['enc_id' => $encId]);

        $userName = [];
        $userName['name'] = $requestData['organization_name'] ?? '';
        if (!empty($requestData['email'] ?? null)) {
            // Mail::to($requestData['email'])->send(new RegisterMail($userName));
            Mail::to('ventesh.amvisolution@gmail.com')->send(new RegisterMail($userName));
        }


        $adminMailData = [
            'organization_name' => $requestData['organization_name'] ?? '',
            'email'             => $requestData['email'] ?? '',
            'contact_no'        => $requestData['contact_no'] ?? '',
            'city'              => $requestData['city'] ?? '',
            'state'             => $requestData['state'] ?? '',
            'msme_type'         => $requestData['msme_type'] ?? '',
            'business_category' => $businessCategory,
            'experience'        => $requestData['experience'] ?? '',
            'specialization'    => $requestData['specialization'] ?? '',
            'admin_url'         => url('/admin/user-datas/' . $userId . '/edit'),
        ];

        // Send to admin
        if (config('mail.admin_email')) {
            Mail::to(config('mail.admin_email'))
                ->send(new AdminNewRegisterMail($adminMailData));
        }

        // Clear Business Data session after successful registration
        session()->forget('msme_business_data_id');

        session()->flash('message', 'Thank you for submitting, you will get a confirmation on the application within 24-48 hours via email');

        return redirect('/');
    }

    public function saveLogin(Request $request)
    {
        $requestData = $request->only(['email', 'password']);
        $credentials = $requestData;

        /*
    |--------------------------------------------------------------------------
    | Step 1: Check if user exists & is approved
    |--------------------------------------------------------------------------
    */
        $approvedUser = DB::table('user_data')
            ->where('email', $requestData['email'])
            ->where('approve', true)
            ->exists();

        if (!$approvedUser) {
            return redirect()->back()
                ->with('message', 'Error! Please try again later');
        }

        /*
        |--------------------------------------------------------------------------
        | Step 2: Attempt login
        |--------------------------------------------------------------------------
        */
        if (!Auth::attempt($credentials)) {
            return redirect()->back()
                ->with('message', 'Invalid email or password');
        }

        /*
        |--------------------------------------------------------------------------
        | Step 3: MSME connection compatibility check (ONLY if pending)
        |--------------------------------------------------------------------------
        */
        if (Session::has('pendingConnectionMsmeId')) {

            $pendingMsmeId = Session::get('pendingConnectionMsmeId');

            // Logged-in user MSME
            $loggedInMsme = DB::table('user_data')
                ->where('email', $requestData['email'])
                ->first();

            // Target MSME user tried to connect with
            $targetMsme = DB::table('user_data')
                ->where('id', $pendingMsmeId)
                ->first();

            if ($loggedInMsme && $targetMsme) {

                // Prevent self-connection
                if ($loggedInMsme->email === $targetMsme->email) {

                    Session::flash(
                        'connectionErrorMessage',
                        "Sorry, you can't request yourself"
                    );
                } else {

                    $loggedInSizes = json_decode($loggedInMsme->business_size, true) ?? [];
                    $targetSizes   = json_decode($targetMsme->business_size, true) ?? [];

                    // ✅ CORRECT compatibility check
                    $commonSizes = array_intersect($loggedInSizes, $targetSizes);

                    if (count($commonSizes) === 0) {
                        Session::flash(
                            'connectionErrorMessage',
                            'Sorry, this MSME does not deal with ' . implode(', ', $loggedInSizes)
                        );
                    }
                }
            }

            // 🔴 IMPORTANT: clear pending ID always
            Session::forget('pendingConnectionMsmeId');
        }

        /*
        |--------------------------------------------------------------------------
        | Step 4: Redirect after login
        |--------------------------------------------------------------------------
        */
        // Clear Business Data session after successful login
        session()->forget('msme_business_data_id');

        return redirect()->intended('/')
            ->withSuccess('Signed in');
    }


    public function forgotPassword(Request $request)
    {
        return view('auth.forgotPassword');
    }

    public function submitforgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users',
        ]);

        $token = Str::random(64);
        $maildata = route('forgotPasswordToken', $token);
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();
        $countPassword = DB::table('password_reset_tokens')->where('email', $request->email)->count();
        if ($countPassword >= 1) {
            $countPassword = DB::table('password_reset_tokens')->where('email', $request->email)->where('created_at', '<=', now()->subHour(1))->count();
            if ($countPassword >= 1) {
                DB::table('password_reset_tokens')->where('email', $request->email)->where('created_at', '<=', now()->subHour(1))->delete();
            } else {
                return back()->with('message', 'We have already sent a password reset link to your registered Email ID!');
            }
        }



        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => Carbon::now()
        ]);


        Mail::to($request->get('email'))->send(new SendForgotPassowrdMail($maildata));

        return back()->with('message', 'We have sent a password reset link to your registered Email ID!');
    }

    public function forgotPasswordToken(Request $request, $token)
    {
        return view('auth.forgotPasswordToken')->with('token', $token);
    }

    public function submitforgotPasswordToken(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users',
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required'
        ]);

        $updatePassword = DB::table('password_reset_tokens')
            ->where([
                'email' => $request->email,
                'token' => $request->token
            ])
            ->first();

        if (!$updatePassword) {
            return back()->with('message', 'Invalid token!');
        }

        $user = User::where('email', $request->email)
            ->update(['password' => Hash::make($request->password)]);

        DB::table('password_reset_tokens')->where(['email' => $request->email])->delete();

        return redirect('/login')->with('message', 'Your password has been updated!');
    }
}
