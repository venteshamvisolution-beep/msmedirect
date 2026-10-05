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
                        @if(session('message'))
                            <div class="alert alert-danger mt-3">
                                {{ session('message') }}
                            </div>
                        @endif
                        @if(session('error'))
                            <div class="alert alert-danger mt-3">
                                {{ session('error') }}
                            </div>
                        @endif
                            <div class="row ">
                               <h5 style="text-align:center">Payment Summary</h5>
                               <div class="co-col in-coup">
                                   
                            <div class="checkout-det">
                                    <div class="co-col">
                                    <p class="m-0">{{$msme_price->name}}</p>
                                    <p class="m-0" id="buzAmount">₹ {{$msme_price->price}}</p>
                            </div>
                            
                            <!--<div class="co-col border-top">-->
                            <!--    <p class="m-0">Sub Total</p>-->
                            <!--    <p class="m-0"></p>-->
                            <!--    <p class="m-0">₹2000</p>-->
                            <!--</div>-->
                            
                            <div class="co-col in-coup">
                               <div class="app-coup position-relative">
                                     <input type="text" class="" placeholder="Apply Coupon" name="coupon" id="coupon">
                                    <button type="button" class="btn p-0 couponBtn" id="couponBtn"><i class="feather icon-feather-gift"></i></button>
                               </div>
                            </div>


                            <div class="co-col">
                                <p class="m-0">Coupon Discount <span id="perce"></span></p>
                                
                                <input type="hidden" id="is_coupon_automatic" value="0">
                                <p class="m-0" id="coupon_discount"></p>
                            </div>

                            <div class="co-col border-top">
                                <p class="m-0">Total</p>
                                <p class="m-0 total-amt">₹ {{$msme_price->price}}</p>
                            </div>
                            
                            <div class="co-col">
                                
                                @if($connectionRequest->status == "1" || $connectionRequest->status == "5")
                                    @if($connectionRequest->conn_id == Auth::user()->id)
                                    <button type="buttton" class="btn cart-btn w-100 proceedToPayment" id="proceedToPayment"  data-encode="{{$connectionRequest->conn_encode}}">Proceed to Payment </button>
                                    <!--<a href ='{{url("/accept-connection/{$connectionRequest->conn_encode}/")}}' class="btn cart-btn w-100" id="proceedToPayment"><p class="m-0 Accepted">Proceed to Payment</p></a>-->
                                    @else
                                    <button type="buttton" class="btn cart-btn w-100 proceedToPayment" id="proceedToPayment"  data-encode="{{$connectionRequest->from_encode}}">Proceed to Payment </button>
                                    <!--<a href ='{{url("/accept-connection/{$connectionRequest->from_encode}")}}' class="btn cart-btn w-100" id="proceedToPayment"><p class="m-0 Accepted">Proceed to Payment</p></a>-->
                                    @endif
                                @endif    
                            </div>
                        </div>
                            
                            </div>
                                
                                
                            </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
