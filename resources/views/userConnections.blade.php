@extends('layouts.app')
@section('body_section')
@include('layouts.header')
   @if(session('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif
    <section> 
        <div class="container-fluid">
            <div class="row justify-content-center">
                @foreach($connectionAccept as $connectionRequest)
                <div class="col-lg-3">
                      <a href="#" class="prod-main-list">
                       
                        <!-- <div class="prod-img">
                          <img src="img/prod1.png" class="w-100" alt="">
                        </div> -->
                        <div class="prod-content">
                          <p class="m-0 date">{{Carbon\Carbon::parse($connectionRequest->connection_created_at)->diffForHumans()}}</p>
                          <div class="prod-name">
                            <h6>{{$connectionRequest->business_category_name}}</h6>
                          </div>
                          <div class="prod-desc">
                            <p>{!!$connectionRequest->business_description!!}.</p>
                          </div>
                          <div class="prod-req-btn">
                            <button type="button" onclick="window.location.href = '{{url('/decline-connection/'.$connectionRequest->conn_encode)}}';" class="btn req-btn decline" > Decline</button>
                            
                            <button type="button" onclick="window.location.href = '{{url('/accept-connection/'.$connectionRequest->conn_encode)}}';" class="btn req-btn accept"> Accept</button>
                          </div>
                        </div>
                        
                      </a>
                </div>
                @endforeach
            </div>
        </div>


<section class="bg-blue">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="page-inner-title">
                        <h1>MSME Connections</h1>
                    </div>
                </div>
            </div>
        </div>
      </section>
       <section  class="bg-gray"> 
        <div class="container-fluid">
          <div class="row justify-content-center">
              @foreach($connectionRequests as $connectionRequest)
            <div class="col-lg-3">
              <a href="#" class="prod-main-list">
               
                <!-- <div class="prod-img">
                  <img src="img/prod1.png" class="w-100" alt="">
                </div> -->
                <div class="prod-content">
                  <p class="m-0 date">{{Carbon\Carbon::parse($connectionRequest->connection_created_at)->diffForHumans()}}  </p>
                  <div class="prod-name">
                    <h6>{{$connectionRequest->business_category_name}} {{$connectionRequest->conn_id}}</h6>
                  </div>
                  <div class="prod-desc">
                    <p>{!!$connectionRequest->business_description!!}.</p>
                  </div>
                 
                    @php
                        $status = config('app.status')[$connectionRequest->status];
                    @endphp
                    @if($connectionRequest->status == "0")
                        <div class="prod-status">
                            <div class="dot dot-yellow"></div>
                            <p class="m-0 requested">Requested</p>
                        </div> 
                    @endif
                    
                    @if($connectionRequest->status == "3")
                        <div class="prod-status">
                            <div class="dot dot-green"></div>
                            <p class="m-0 requested">Connected</p>
                        </div> 
                    @endif
                    
                    @if($connectionRequest->status == "4")
                        <div class="prod-status">
                            <div class="dot dot-green"></div>
                            <p class="m-0 requested">Applicant payment pending</p>
                        </div> 
                    @endif
                    
                    @if($connectionRequest->status == "5")
                        <div class="prod-status">
                            <div class="dot dot-green"></div>
                            <p class="m-0 requested">Recipent payment pending</p>
                        </div> 
                    @endif
                    @if($connectionRequest->status == "1" || $connectionRequest->status == "5")
                        @if($connectionRequest->conn_id == Auth::user()->id)
                         <div class="prod-status">
                            <div class="dot dot-green"></div>
                            <!--<a href ='{{url("/accept-connection/{$connectionRequest->conn_encode}")}}>'<p class="m-0 Accepted">Payment Link 1</p></a>-->
                            <a href="{{route('checkout',[$connectionRequest->from_id,base64_encode($msme_type->msme_type)])}}" class="btn req-btn"><p class="m-0 Accepted">Checkout</p></a>
                        </div>
                        
                        @else
                            <div class="prod-status">
                            <div class="dot dot-green"></div>
                            <!--<a href ='{{url("/accept-connection/{$connectionRequest->from_encode}")}}>'<p class="m-0 Accepted">Payment Link</p></a>-->
                            <a href="{{route('checkout',[$connectionRequest->from_id,base64_encode($msme_type->msme_type)])}}" class="btn req-btn"><p class="m-0 Accepted">Checkout</p></a>
                        </div>
                        
                        @endif
                        
                         
                    @endif
                   
                    <!--<a href="" class="btn req-btn"><p class="m-0 Accepted">Checkout</p></a>-->
                    
                    
                    @if($connectionRequest->status == "2")
                        <div class="prod-status">
                            <div class="dot dot-red"></div>
                            <p class="m-0 decline">{{$status}}</p>
                        </div>
                    @endif
                
              </a>
            </div>
            </div>
            @endforeach
            
         
          </div>
        </div>
      </section>

@endsection