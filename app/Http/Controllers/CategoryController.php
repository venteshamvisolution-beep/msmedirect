<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use Mail;
use Session;
use Crypt;
use Auth;
use Str;
class CategoryController extends Controller
{
    
    public function categoryListingPage(Request $request,$businessType){
        $connections = [];
        $canViewPage = true;
        if (!in_array($businessType, ["product", "services", "all"])) {
            return abort(403);
        }
        
        $businessId = ($businessType == "product") ? 0 : 1;
        $userData = DB::table('user_data')->select('*','user_data.id as user_data_id')->where('user_data.approve',true)
                                            ->leftjoin('business_category',
                                                    'business_category.id',
                                                'user_data.business_category');
        
        if($businessType == "all"){
            $businessType = "Product & Services";
            $businessCategory = DB::table('business_category')->get()->toArray();
            
        }else{
        
            if(in_array($businessType, ["product", "services"])){
                $businessCategory = DB::table('business_category')->where('type',$businessId)->get()->toArray();
                $userData = $userData->where('user_data.business_type',$businessId);
            }
            
            
            
            if(Auth::check()){
                $connections = DB::table('connections')->select('conn_user_data','from_user_data','status','conn_id','from_id')
                ->where('conn_id',Auth::user()->id)
                ->orWhere('from_id',Auth::user()->id)
                ->get()->toArray();
                
                
                $businessSize = DB::table('user_data')->where('email',Auth::user()->email)->value('business_size');
                $businessSize = json_decode($businessSize,true); 
                $userData = $userData->whereNot('user_data.email',Auth::user()->email);
                $userData = $userData->where(function ($query) use ($businessSize) {
                    foreach ($businessSize as $value) {
                        $query->orWhereJsonContains('business_size', $value);
                    }
                });
            }
        }
        $checkedValues = [];
        if($request->input('checkedValues') != ''){
            $checkedValues = explode(",", $request->input('checkedValues'));
            $userData =  $userData->whereIn('business_category',$checkedValues);
        }
        
        $searchHome = $request->input('search_home');
        $location = $request->input('home_location');
        
        if($searchHome != ''){
            $userData = $userData
              ->where('business_category.name', 'like', '%' . $searchHome . '%')
              ->orWhere('state', 'like', '%' . $searchHome . '%')
              ->orWhere('city', 'like', '%' . $searchHome . '%')
              ->orWhere('state_city', 'like', '%' . $searchHome . '%')
              ->orWhere('pincode', 'like', '%' . $searchHome . '%');
              $businessType = ucwords($searchHome);
        }
         
        if($location != ''){
            $userData = $userData->where('state', $location);
        }
        
        $userData = $userData->get()->toArray();
        
        return view('listing')->with('searchHome',$searchHome)->with('userDatas',$userData)->with('businessCategory',$businessCategory)->with('businessType',$businessType)->with('connections',$connections)->with('checkedValues',$checkedValues);
        
    }
}