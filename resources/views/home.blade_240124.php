@extends('layouts.app')
@section('body_section')
@include('layouts.header')
@php
    $schemes = DB::table('schemes')->get()->toArray();
    $news = DB::table('news')->get()->toArray();
@endphp
<section class="p-0">
      <div class="container-fluid">
        <div class="row">
          <div class="col-lg-12 p-0">
              <div class="home-banner-img position-relative">
                  <img src="{{asset('custom/img/banner-home.png')}}" class="w-100" alt="">
                  <div class="hm-banner-content">
                      <div class="container">
                        <div class="row align-items-center">
                          <div class="col-lg-5">
                            <div class="hm-search">
                                <h1>Connecting MSME Businesses to MSME Service Providers
                                </h1>
                                <div class="hm-form">
                                  <form action="">
                                    <div class="row">
                                      <div class="col-12">
                                        <div class="hm-in position-relative">
                                          <input type="text" placeholder="Search Location" name="search_home" id="search_home">
                                          <button type="button" class="btn" id="search_home_btn"><i class="feather icon-feather-search"></i></button>
                                        </div>
                                      </div>
                                      <div class="col-12">
                                        <select name="home_location" id="home_location">
                                              @php
            $stateList  = DB::table('pincode')->select('State')->distinct('State')->orderBy('state','ASC')->get()->toArray();
        @endphp
        <option value="">Select A Location</option>
                                          @foreach($stateList as $key => $value)
                            
                            <option value="{{$value->State}}">{{$value->State}}</option>
                            
                            @endforeach
                                        </select>
                                      </div>
                                    </div>
                                  </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 offset-lg-1">
                          <div class="banner-sec-img">
                            <img src="{{asset('custom/img/banner-img.png')}}" class="w-100 mobo-hide" alt="">
                          </div>                          
                        </div>
                        </div>
                       
                      </div>
                  </div>
              </div>
          </div>
        </div>
      </div>
    </section>
    
    
    <section class="bg-gray">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                  <img src="{{asset('custom/img/about-img.jpg')}}" class="w-100" alt="">
                </div>
                  <div class="col-lg-6">
                      <div class="hm-about-content">
                          <div class="main-title">
                            <h4>About <br><span class="text-cyan">MSME</span> <span class="text-green">Direct</span></h4>               
            
                          </div>
                          <p> Are you an MSME on the growth path?
                            Are you facing challenges in Funding, marketing your Products, Legal services, Technical Upgradation, Accounting & Taxation, Availing of Govt Incentives, HR support, Brand Communication, International trade and many other services.
                            <p><b>Then MSME Direct is your ANSWER.</b></p>
                            <p>Are you a manufacturer or a trader?</p>
                            <p>DO you want to connect to a buyer or a supplier?</p>
                            <p><b>Then MSME Direct is your ANSWER.</b></p>
                      </div>
                  </div>
            </div>
          </div>
    </section>
    
     <section>
        
         
      <div class="container">
        <div class="row align-items-center">
            <div class="col-12">
              <div class="main-title text-center">
                <h4>Who can Register</h4>  
              </div>
            </div>
        </div>
        <div class="row align-items-center">
          
          <div class="comp-type-sec">
            <div class="comp-sec">
              <div class="comp-img">
                <img src="{{asset('custom/img/reg1.png')}}" alt="">
              </div>
              <div class="com-desc">
                <h4>Startup </h4>
                <p>Startups emerge as entrepreneurs create products or services to meet perceived demand.</p>
              </div>
            </div>
            <div class="comp-sec">
              <div class="comp-img">
                <img src="{{asset('custom/img/reg2.png')}}" alt="">
              </div>
              <div class="com-desc">
                <h4>Micro </h4>
                <p>Investment less than Rs.1 crore and Annual Turnover less than Rs. 5 crores</p>
              </div>
            </div>
            <div class="comp-sec">
              <div class="comp-img">
                <img src="{{asset('custom/img/reg3.png')}}" alt="">
              </div>
              <div class="com-desc">
                <h4>Small </h4>
                <p>Investment less than Rs.10 crore and Annual Turnover less than Rs. 50 crores</p>
              </div>
            </div>
            <div class="comp-sec">
              <div class="comp-img">
                <img src="{{asset('custom/img/reg4.png')}}" alt="">
              </div>
              <div class="com-desc">
                <h4>Medium </h4>
                <p>Investment less than Rs.50 crore and Annual Turnover less than Rs. 250 crores</p>
              </div>
            </div>
            <div class="comp-sec">
              <div class="comp-img">
                <img src="{{asset('custom/img/reg5.png')}}" alt="">
              </div>
              <div class="com-desc">
                <h4>Beyond MSME</h4>
                <p>Investment less than Rs. 250 crores and Annual Turnover less than Rs. 500 crores</p>
              </div>
            </div>
          </div>
           
        </div>
      </div>
   
     </section>   
     @php
        $news = DB::table('news')->where('show_home',true)->get()->toArray();
        $scheme = DB::table('schemes')->where('show_home',true)->get()->toArray();
     @endphp
     
     
     @if(count($news) >= 1)
      <section class="bg-gray">
          <div class="container">
            <div class="row mb-4">
              <div class="col-lg-12">
                <div class="main-title">
                  <h4>Msme News</h4>
                 
                </div>
              </div>
            </div>
        <div class="row">
            @foreach($news as $new)
            <div class="col-lg-4">
              <a href="{{$new->link}}" class="btn p-0 blog-sec">
                <div class="blog-title">
                  <p class="m-0">{{\Carbon\Carbon::parse($new->date)->format('d M Y')}}</p>
                  <h5>{{$new->name}}</h5>                  
                </div>
                <div class="blog-img">
                  @if($new->media != '')
                            <img src="{{asset('storage/'.$new->media)}}" alt="">
                            @else
                            <img src="{{asset('custom/img/logo.png')}}" alt="">
                            @endif
                </div>
              </a>
            </div>
            @endforeach
        </div>
        <div class="row mt-3">
          <div class="text-center">
            <a href="{{route('news')}}" class="main-btn2">View More</a>
          </div>
        </div>
      </div>
    </section>
    @endif
    
    
    
    @if(count($scheme) >= 1)
     <section>   
     @foreach($scheme as $value)
     <div class="container">
    
        <div class="row">
          <div class="col-lg-12">
            <div class="main-title">
              <h3>{{$value->title}}</h3>
               {!! trim(stripslashes($value->home_text),'"') !!}
            </div>
          </div>
          <div class="row mt-4">
            <div class="col-12">
              <div class="table-responsive">
                <table class="table main-v-tbl">
                  <thead>
                    <tr>
                      <th>Sr.No</th>
                      <th>Scheme</th>
                      <th>Function</th>
                      <th>Link</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      @foreach(json_decode($value->details,true) as $key => $value)
                                  <tr>
                                    <td>{{$key+1}}</td>
                                    <td>{{$value['name']}}</td>
                                    <td>{{$value['function']}}</td>
                                    <td><a href="{{$value['link']}}" class="btn"><i class="feather icon-feather-arrow-right"></i></a></td>
                                     
                                  </tr>
                                @endforeach
                    </tr>
                 </tbody>
                </table>
              </div>
              <div class="text-center">
                <a href="{{url('/msme-scheme')}}" class="main-btn2">View More</a>
              </div>
            </div>
          </div>
        </div>
      </div>
      @endforeach
    </section>
    @endif
     <section class="bg-gray">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-12 col-lg-10 col-md-10">
              <div class="contact-sec">
                <div class="row align-items-center">
                  <div class="col-lg-8 col-md-8">
                    <h4 class="">Need Help! Feel Free to Connect with Us</h4>
                  </div>
                  <div class="col-lg-4 text-center">
                    <a href="{{url('/contact-us')}}" class="main-btn2 w-100 m-0">Contact us</a>
                  </div>
                </div>
              </div>
          </div>
        </div>
      </div>
    </section>
     

@endsection

@section('page_js')
<script>
document.getElementById('search_home_btn').addEventListener('click', function() {
    // e.preventDefault();
    var searchHomeValue = document.getElementById('search_home').value.trim();
    var homeLocationValue = document.getElementById('home_location').value.trim();
    
    if(searchHomeValue == '' && homeLocationValue == ""){
        alert('Please Enter or Select Location');
        return false;
    }

    // Base URL
    var baseUrl = "https://fiveonline.live/MsmeDirect/listing/all";

    // Constructing the URL
    var searchUrl = baseUrl + "?search_home=" + encodeURIComponent(searchHomeValue) + "&home_location=" + encodeURIComponent(homeLocationValue);

    console.log(searchUrl);
    window.location.assign(searchUrl);
});
</script>
@endsection