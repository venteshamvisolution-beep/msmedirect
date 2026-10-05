@extends('layouts.app')
@section('body_section')
@include('layouts.header')
@php
    $schemes = DB::table('schemes')->get()->toArray();
    $news = DB::table('news')->get()->toArray();
@endphp

<style>
   .banner-section{
       padding: 11px 0;
   }
</style>
<!--<section class="p-0">-->
<!--      <div class="container-fluid">-->
<!--        <div class="row">-->
<!--          <div class="col-lg-12 p-0">-->
<!--              <div class="home-banner-img position-relative">-->
<!--                  <img src="{{asset('custom/img/banner-home.png')}}" class="w-100" alt="">-->
<!--                  <div class="hm-banner-content">-->
<!--                      <div class="container-fluid">-->
<!--                        <div class="row align-items-center">-->
<!--                          <div class="col-lg-6">-->
<!--                            <div class="hm-search">-->
                               
                               
                                
                                
<!--                            </div>-->
<!--                        </div>-->
<!--                        <div class="col-lg-6">-->
<!--                          <div class="banner-sec-img">-->
<!--                            <img src="{{asset('custom/img/banner-img.png')}}" class="w-100 mobo-hide" alt="">-->
<!--                          </div>                          -->
<!--                        </div>-->
<!--                        </div>-->
                       
<!--                      </div>-->
<!--                  </div>-->
<!--              </div>-->
<!--          </div>-->
<!--        </div>-->
<!--      </div>-->
<!--    </section>-->
    
    
    <section class="banner-section">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="banner-new">
                        <h1>ARE YOU AN <span class="text-green">MSME</span> ON A <span class="text-cyan">GROWTH</span> PATH ?</h1>
                        <h2>Do you want to connect to a service provider?</h2>
                        <h2>Do you want to connect to a Manufacturer or a trader?</h2>
                        <h1>THEN MSME DIRECT IS YOUR ANSWER</h1>
                        <div class="btn-sec">
                            <h2 class="mt-3 mb-0">Registration is Free</h2>
                            <h2 class="">Fee to be charged only once both parties are connected.</h2>
                            <a href="{{url('register')}}" class="main-btn2">Register Now</a>
                        </div>
                    </div>
                </div>
               
         
            
             <div class="col-lg-6">
                    <div class="main-title text-center mb-4">
                        
                        <h3>Who Can Register?</h3>
                    </div>
                    
                    <div class="row justify-content-center">
                        <div class="col-lg-4 mb-4">
                            <div class="comp-sec w-100">
                              <div class="comp-img">
                                <img src="{{asset('custom/img/reg1.png')}}" alt="">
                              </div>
                              <div class="com-desc">
                                <h4>Startup </h4>
                                <!--<p>Startups emerge as entrepreneurs create products or services to meet perceived demand.</p>-->
                              </div>
                            </div>
                        </div>
                        <div class="col-lg-4 mb-4">
                            <div class="comp-sec w-100">
                              <div class="comp-img">
                                <img src="{{asset('custom/img/reg2.png')}}" alt="">
                              </div>
                              <div class="com-desc">
                                <h4>Micro </h4>
                                <!--<p>Startups emerge as entrepreneurs create products or services to meet perceived demand.</p>-->
                              </div>
                            </div>
                        </div>
                        <div class="col-lg-4 mb-4">
                            <div class="comp-sec w-100">
                              <div class="comp-img">
                                <img src="{{asset('custom/img/reg3.png')}}" alt="">
                              </div>
                              <div class="com-desc">
                                <h4>Small </h4>
                                <!--<p>Startups emerge as entrepreneurs create products or services to meet perceived demand.</p>-->
                              </div>
                            </div>
                        </div>
                        <div class="col-lg-4 mb-4">
                            <div class="comp-sec w-100">
                              <div class="comp-img">
                                <img src="{{asset('custom/img/reg4.png')}}" alt="">
                              </div>
                              <div class="com-desc">
                                <h4>Medium </h4>
                                <!--<p>Startups emerge as entrepreneurs create products or services to meet perceived demand.</p>-->
                              </div>
                            </div>
                        </div>
                        <div class="col-lg-4 mb-4">
                            <div class="comp-sec w-100">
                              <div class="comp-img">
                                <img src="{{asset('custom/img/reg5.png')}}" alt="">
                              </div>
                              <div class="com-desc">
                                <h4>Beyond MSME</h4>
                               
                              </div>
                            </div>
                        </div>
                    </div>
                  
             </div>
             
             <h2><i><center>#ConnectingMSMEs</center></i></h2>
             
        </div>
         </div>
 
    </section>
    
    <section class="bg-light">
        <div class="container-fluid">
            <div class="row align-items-center mb-4">
            <div class="col-12">
              <div class="main-title text-center">
                <h4>Featured Products</h4>  
              </div>
            </div>
        </div>
                @php
                    if(Auth::user()){
                    $products = DB::table('user_data')->leftjoin('business_category','business_category.id','user_data.business_category')->where('business_type',0)->where('user_data.email',Auth::user()->email)->get()->toArray();
                    
                    }else{
                    $products = DB::table('user_data')->leftjoin('business_category','business_category.id','user_data.business_category')->where('business_type',0)->get()->toArray();
                    
                    }
                @endphp
            <div class="row">
                @foreach($products as $product)
                  <div class="col-lg-3">
            <a href='{{url("/request-connection/{$product->id}
            ")}}' class="prod-main-list">
             
              <!-- <div class="prod-img">
                <img src="img/prod1.png" class="w-100" alt="">
              </div> -->
              <div class="prod-content">
                <div class="prod-name">
                  <h6>{{$product->name}}</h6>
                </div>
                <div class="prod-desc">
                  <p>{{$product->business_description}}</p>
                </div>
              </div>
                                           <div class="prod-btn">
                <button type="button" class="main-btn2 w-100 m-0">Connect Now</button>
              </div>
            </a>
          </div>
          @endforeach
                 
            </div>
            <div class="row">
                <div class="col-lg-12 text-center">
            <a href="{{url('listing/product')}}" class="main-btn2">View More</a>
          </div>
            </div>
        </div>
    </section>
           @php
            if(Auth::user()){
                $products = DB::table('user_data')->leftjoin('business_category','business_category.id','user_data.business_category')->where('user_data.email',Auth::user()->email)->where('business_type',1)->get()->toArray();

            }else{
                $products = DB::table('user_data')->leftjoin('business_category','business_category.id','user_data.business_category')->where('business_type',1)->get()->toArray();
            }
            
           
                @endphp
    @if(count($products) >= 1)
    <section>
        <div class="container-fluid">
            <div class="row align-items-center mb-4">
            <div class="col-12">
              <div class="main-title text-center">
                <h4>Featured Services</h4>  
              </div>
            </div>
        </div>
        
        
            <div class="row">
                @foreach($products as $product)
                  <div class="col-lg-3">
                  <a href='{{url("/request-connection/{$product->id}
            ")}}' class="prod-main-list">
             
              <!-- <div class="prod-img">
                <img src="img/prod1.png" class="w-100" alt="">
              </div> -->
              <div class="prod-content">
                <div class="prod-name">
                  <h6>{{$product->name}}</h6>
                </div>
                <div class="prod-desc">
                  <p>{{$product->business_description}}</p>
                </div>
              </div>
                                           <div class="prod-btn">
                <button type="button" class="main-btn2 w-100 m-0">Connect Now</button>
              </div>
            </a>
          </div>
          @endforeach
                 
            </div>
            <div class="row">
                <div class="col-lg-12 text-center">
                    <a href="{{url('listing/services')}}" class="main-btn2">View More</a>
                </div>
            </div>
        </div>
    </section>
    @endif
 
    
    <!--<section class="bg-gray">-->
    <!--    <div class="container">-->
    <!--        <div class="row align-items-center">-->
    <!--            <div class="col-lg-6">-->
    <!--              <img src="{{asset('custom/img/about-img.jpg')}}" class="w-100" alt="">-->
    <!--            </div>-->
    <!--              <div class="col-lg-6">-->
    <!--                  <div class="hm-about-content">-->
    <!--                      <div class="main-title">-->
    <!--                        <h4>About <br><span class="text-cyan">MSME</span> <span class="text-green">Direct</span></h4>               -->
            
    <!--                      </div>-->
    <!--                      <p> Are you an MSME on the growth path?-->
    <!--                        Are you facing challenges in Funding, marketing your Products, Legal services, Technical Upgradation, Accounting & Taxation, Availing of Govt Incentives, HR support, Brand Communication, International trade and many other services.-->
    <!--                        <p><b>Then MSME Direct is your ANSWER.</b></p>-->
    <!--                        <p>Are you a manufacturer or a trader?</p>-->
    <!--                        <p>DO you want to connect to a buyer or a supplier?</p>-->
    <!--                        <p><b>Then MSME Direct is your ANSWER.</b></p>-->
    <!--                  </div>-->
    <!--              </div>-->
    <!--        </div>-->
    <!--      </div>-->
    <!--</section>-->
    
     <section class="bg-light">
        
         
      <div class="container">
        <div class="row align-items-center">
            <div class="col-12">
              <div class="main-title text-center">
                <h4>Connection Fee</h4>  
              </div>
            </div>
        </div>
        <div class="row align-items-center">
              @php
                    $charges = DB::table('charges')->get()->toArray();
                @endphp
          <div class="comp-type-sec">
            <div class="comp-sec">
              <div class="comp-img">
                <img src="{{asset('custom/img/reg1.png')}}" alt="">
              </div>
              <div class="com-desc">
                <h4>Startup </h4>
                <p>Enterprise</p>
                <h5 class="text-green"><strong>₹{{$charges[0]->price}}/-</strong></h5>
              </div>
            </div>
            <div class="comp-sec">
              <div class="comp-img">
                <img src="{{asset('custom/img/reg2.png')}}" alt="">
              </div>
              <div class="com-desc">
                 <h4>Micro </h4>
                <p>Enterprise</p>
                 <h5 class="text-green"><strong>₹{{$charges[1]->price}}/-</strong></h5>
              </div>
            </div>
            <div class="comp-sec">
              <div class="comp-img">
                <img src="{{asset('custom/img/reg3.png')}}" alt="">
              </div>
              <div class="com-desc">
                <h4>Small </h4>
                <p>Enterprise</p>
                <h5 class="text-green"><strong>₹{{$charges[2]->price}}/-</strong></h5>
              </div>
            </div>
            <div class="comp-sec">
              <div class="comp-img">
                <img src="{{asset('custom/img/reg4.png')}}" alt="">
              </div>
              <div class="com-desc">
                <h4>Medium </h4>
                 <p>Enterprise</p>
                <h5 class="text-green"><strong>₹{{$charges[3]->price}}/-</strong></h5>
              </div>
            </div>
            <div class="comp-sec">
              <div class="comp-img">
                <img src="{{asset('custom/img/reg5.png')}}" alt="">
              </div>
              <div class="com-desc">
                <h4>Beyond MSME</h4>
                <p>Enterprise</p>
                <h5 class="text-green"><strong>₹{{$charges[4]->price}}/-</strong></h5>
              </div>
            </div>
          </div>
           
             <div class="row">
                <div class="col-lg-12 text-center">
                    <a href="{{url('register')}}" class="main-btn2">Register now</a>
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
      <section >
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
    
    <section class="bg-light">
        <div class="container">
            <div class="row">
                <div class="col-12 mb-4">
                   <div class="main-title ">
                        <h4>MSME Schemes</h4>  
                   </div>
                </div>
               
            </div>
            @php    
            $schemes = DB::table('schemes')->where('show_home',true)->get()->toArray();
            
            
            @endphp
            
            <div class="row ">
                @foreach($schemes as $scheme)
                <div class="col-lg-4">
                    <a href="{{url('/msme-scheme')}}" class="sch-sec">
                        <div class="sch-img position-relative">
                             <img src="{{asset('storage/'.$scheme->image)}}" alt=""class="w-100">
                        </div>
                        <div class="sch-head mt-4 text-center">
                            <h5>{{$scheme->title}}</h5>
                        </div>
                    </a>
                </div>
                @endforeach
            
                
            </div>
             <div class="row">
                <div class="col-lg-12 text-center">
                    <a href="{{url('/msme-scheme')}}" class="main-btn2">View More</a>
                </div>
            </div>
        </div>
    </section>
    
    <section>
        <div class="container">
          <div class="row align-items-center">
            <div class="col-lg-6">
                <img src="https://msmedirect.com/public/custom/img/about-img.jpg" class="w-100" alt="">
            </div>
            <div class="col-lg-6 ">
                <div class="hm-about-content abt-cont">
                    
                    <div class="main-title">
                      <h4>
                        About <br><span class="text-cyan">MSME</span> 
                        <span class="text-green">Direct</span>
                    </h4>                 
   
                    </div>
                    <p>To cater to the needs of the MSMEs who are passionate for growth.</p>
                    <p>Today in this world of excessive information , we are perplexed by the innumerable options available to us. MSME DIRECT will play the role of connecting the your businesses to the right service providers .</p>
                   <p> For the entrepreneurs , we realize that marketing , branding and fund raising are some of the huge challenges faced by them. .We aim not only to expedite processes for entrepreneurs but to also provide them with the right solutions at a reasonable cost.</p>
                   <p>MSMEs in India have a great potential and MSME DIRECT will show the way.</p> 
                    
                </div>
            </div>
          </div>
          <div class="row mt-5 justify-content-center">
            <div class="col-lg-4">
              <div class="abt-vm-sec">
                <div class="abt-vm-ico">
                  <i class="feather icon-feather-eye"></i>
                </div>
                <div class="abt-ico-cont">
                  <h6>Our Vision</h6>
                  <p>To facilitate social and economic growth by increasing opportunities for MSMEs </p>
                </div>
              </div>
            </div>
            <div class="col-lg-4">
              <div class="abt-vm-sec">
                <div class="abt-vm-ico">
                  <i class="feather icon-feather-target"></i>
                </div>
                <div class="abt-ico-cont">
                  <h6>Our Mission</h6>
                  <p>
                      To provide information on resources available for development of MSMEs ,thereby enabling them to take right decisions.
                  </p>
                </div>
              </div>
            </div>
            
          </div>
        </div>
    </section>
    
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
// document.getElementById('search_home_btn').addEventListener('click', function() {
//     // e.preventDefault();
//     var searchHomeValue = document.getElementById('search_home').value.trim();
//     var homeLocationValue = document.getElementById('home_location').value.trim();
    
//     if(searchHomeValue == '' && homeLocationValue == ""){
//         alert('Please Enter or Select Location');
//         return false;
//     }

//     // Base URL
//     var baseUrl = "https://fiveonline.live/MsmeDirect/listing/all";

//     // Constructing the URL
//     var searchUrl = baseUrl + "?search_home=" + encodeURIComponent(searchHomeValue) + "&home_location=" + encodeURIComponent(homeLocationValue);

//     console.log(searchUrl);
//     window.location.assign(searchUrl);
// });
</script>
@endsection