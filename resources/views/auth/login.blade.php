@extends('layouts.app')
@section('body_section')
<section class="login-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-4">
                    <div class="login-sec">
                        <div class="login-logo">
                             <a href="{{url('/')}}"><img src="{{asset('custom/img/logo.png')}}" alt=""></a>
                        </div>
                         <form action="{{route('saveLogin')}}" method="POST"enctype="multipart/form-data">
                            @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
@if(session('message'))
    <div class="alert alert-success">
        {{ session('message') }}
    </div>
@endif

                            @csrf
      
                            <div class="row ">
                                <div class="col-12">
                                    <input type="email" id="email" name="email" placeholder="Email / Mobile Number">
                                </div>
                                <div class="col-12">
                                    <input type="password" id="password" name="password" placeholder="Enter Password">
                                    <a href="{{url('/forgot-password')}}" class="btn p-0">Forgot Password?</a>
                                </div>
                                <div class="col-12">
                                    <button class=" main-btn2 w-100" type="submit"> LOGIN</button>
                                </div>
                                <div class="col-12 text-center mt-3 mb-3">
                                    <span>OR</span>
                                </div>
                                <div class="col-12 text-center">
                                    <a href="{{url('register')}}" class=" main-btn2 bg-cyan w-100 m-0"> REGISTER NOW</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection