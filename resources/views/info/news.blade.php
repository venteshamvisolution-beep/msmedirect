@extends('layouts.app')
@section('body_section')
@include('layouts.header')
@php
    $news = DB::table('news')->get()->toArray();
@endphp
    <section class="bg-blue">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="page-inner-title">
                        <h1>MSME News</h1>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <section>
        <div class="container">
            <div class="row">
                @foreach($news as $key => $value)
                <div class="col-lg-4">
                    <a href="{{$value->link}}" target="_blank" class="btn p-0 blog-sec">
                        <div class="blog-title">
                             <p class="m-0">{{\Carbon\Carbon::parse($value->date)->format('d M Y')}}</p>
                             <h5>{{$value->name}}</h5>                  
                        </div>
                        <div class="blog-img">
                            @if($value->media != '')
                            <img src="{{asset('storage/'.$value->media)}}" alt="">
                            @else
                            <img src="{{asset('custom/img/logo.png')}}" alt="">
                            @endif
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </section>
  
@endsection