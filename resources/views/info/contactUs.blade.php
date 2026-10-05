@extends('layouts.app')
@section('body_section')
    @include('layouts.header')
    <section class="">
        <div class="container">
            <div class="row justify-content-center align-items-center">
                <div class="col-lg-6">
                  <div class="hm-about-content abt-cont">
                          
                      <div class="main-title">
                        <h4>
                          <span class="text-cyan">Contact</span> 
                          Us
                      </h4>               
    
                      </div>
                     <div class="contact-sec mb-4">
                        <div class="cont-main">
                          <div class="cont-col">
                            <i class="fa-solid fa-location-dot"></i>
                            <p>19, Gopal Bhavan, <br>
                            199, Princess Street <br>
                            Mumbai - 400002</p>
                          </div>
                          <div class="cont-col">
                            <i class="fa-solid fa-phone"></i>
                            <p><a href="tel:9152036911" class="">+91 91520 36911</a></p>
                        </div>
                        <div class="cont-col">
                          <i class="fa-solid fa-envelope"></i>
                          <p><a href="mailtomsmedirect@gmail.com:" class="">msmedirect@gmail.com</a></p>
                      </div>
                        </div>
                      </div>
                      
                  </div>
                </div>
                <div class="col-lg-6">
                  <h4 class="mb-3">Feedback Form</h4>
                      <form action="{{route('saveContactUs')}}" class="feedback-form" method="POST">
                          @csrf
                    @if(session('message'))
                        <div class="alert alert-success">
                            {{ session('message') }}
                        </div>
                    @endif
                        
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="row"> 
                      <div class="col-12">
                        <input type="text" class="" placeholder="Enter Name" id="name" name="name" value="{{old('name')}}">
    
                      </div>
                      <div class="col-12">
                        <input type="tel" class="" placeholder="Enter Mobile Number" name="mobile_number" value="{{old('mobile_number')}}">
                        
                      </div>
                      <div class="col-12">
                        <input type="email" class="" placeholder="Enter Email Address" name="email" value="{{old('email')}}">                    
                      </div>
                      <div class="col-12">
                        <textarea name="message" id="message"  rows="3" placeholder="Message">{{old('message')}}</textarea>                
                      </div>
                      <div class="col-12">
                        <button class=" main-btn2 bg-cyan w-100 m-0" type="submit"> Send Feedback</button>
                    </div>
                    </div>
                  </form>
                </div>
            </div>
        </div>
    </section>


@endsection