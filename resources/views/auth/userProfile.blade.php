@extends('layouts.app')
@section('body_section')
@include('layouts.header')
 <section class="bg-blue">
      <div class="container">
          <div class="row align-items-center">
              <div class="col-lg-6">
                  <div class="page-inner-title">
                      <h1>User Profile</h1>
                  </div>
              </div>
          </div>
      </div>
  </section>

  

  <!--Sticky Filter-->
    <div class="top-filter">
      <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
              <h3 class="m-0">{{$userProfile->organization_name}}</h3>
            </div>
            <div class="col-lg-6 text-right mobo-hide">
                <a href="edit-register.html" class=" main-btn2 bg-cyan m-0" > Edit</a>
            </div>
        </div>       
      </div>        
    </div>
  <!---->
   <section>
    <div class="container">
      <div class="row">
        <div class="col-lg-6">
            <div class="form-box">
              <h5>Organization Details</h5>
              <div class="rge-data-flex">
                <div class="reg-data">
                  <p>Type of Business </p>
                  <p class="reg-ans">{{config('app.business_type')[$userProfile->business_type]}}</p>
                </div>
                <div class="reg-data">
                  <p>MSME Type</p>
                  <p class="reg-ans">{{$userProfile->msme_type}}</p>
                </div>
                <div class="reg-data">
                  <p>Business Category</p>
                  <p class="reg-ans">{{$userProfile->business_category}}</p>
                </div>
                <div class="reg-data">
                  <p>Business Sub-Category</p>
                  <p class="reg-ans">{{$userProfile->business_sub_category}}</p>
                </div>
                <div class="reg-data">
                  <p>Specialization</p>
                  <p class="reg-ans">{{$userProfile->specialization}}</p>
                </div>
              </div>
             
            </div>
        </div>
        <div class="col-lg-6">
          <div class="form-box">
            <h5>Contact Details</h5>
            <div class="rge-data-flex">
              <div class="reg-data">
                <p>Contact Number</p>
                <p class="reg-ans">+91 {{$userProfile->contact_no}}</p>
              </div>
              <div class="reg-data">
                <p>Email</p>
                <p class="reg-ans">{{$userProfile->email}}</p>
              </div>
              <div class="reg-data w-100">
                <p>Enter Address</p>
                <p class="reg-ans">{{$userProfile->address}}</p>
              </div>
              <div class="reg-data">
                <p>Business Sub-Category</p>
                <p class="reg-ans">{{$userProfile->business_sub_category}}</p>
              </div>
              <div class="reg-data">
                <p>Specialization</p>
                <p class="reg-ans">{{$userProfile->specialization}}</p>
              </div>
            </div>
           
          </div>
        </div>
        <div class="col-lg-12">
          <div class="form-box">
            <h5>Other Details</h5>
            <div class="rge-data-flex">
              <div class="reg-data w-100">
                <p>Which business category would you like to connect to / provide service to?</p>
                <p class="reg-ans">{{implode(",",json_decode($userProfile->business_size,true))}}</p>
              </div>
              <div class="reg-data">
                <p>Years of Experience</p>
                <p class="reg-ans">{{$userProfile->experience}}</p>
              </div>
              <div class="reg-data ">
                <p>GST No</p>
                <p class="reg-ans">{{$userProfile->document_no}}</p>
              </div>
              <div class="reg-data">
                <p>Upload Document of entered ID.</p>
                @foreach(json_decode($userProfile->document_file,true) as $key => $value)
                <a href='{{asset("storage/{$value}")}}' target="_blank"><p class="reg-ans">{{str_replace('userdata/','',$value)}}</p></a>
                @endforeach
              </div>
              <div class="reg-data">
                <p>State or City which you can provide Service to</p>
                <p class="reg-ans">{{$userProfile->state_city}}</p>
              </div>
            </div>
           
          </div>
        </div>
        <div class="col-lg-12">
          <div class="form-box">
            <div class="row align-items-end">
                <div class="col-12">
                    <h6>Description</h6>
                    <p>We are a {{$userProfile->organization_name}} and a {{$userProfile->msme_type}} MSME firm in {{$userProfile->msme_type}} with a {{$userProfile->experience}} years of experience and specialization in {{$userProfile->specialization}} operating from {{$userProfile->city}},{{$userProfile->state}}) and we also provide service to {{$userProfile->state_city}}.</p>
                </div>
                
            </div>
        </div>
        </div>
      </div>
    </div>
   </section>
@endsection