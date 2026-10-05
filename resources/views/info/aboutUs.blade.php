@extends('layouts.app')
@section('body_section')
    @include('layouts.header')
     <div class="container">
          <div class="row align-items-center">
            <div class="col-lg-6">
                <img src="{{asset('custom/img/about-img.jpg')}}" class="w-100" alt="">
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
                  <p>To provide information on resources available for development of MSMEs ,thereby enabling them to take right decisions.
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
                    <a href="contact-us.html" class="main-btn2 w-100 m-0">Contact us</a>
                  </div>
                </div>
              </div>
          </div>
        </div>
      </div>
    </section>
@endsection