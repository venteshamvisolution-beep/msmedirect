@extends('layouts.app')
@section('body_section')
@include('layouts.header')
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
  
    <!--Sticky Filter-->
    <div class="top-filter">
      <div class="container-fluid">
        <form action="">
          <div class="row align-items-center justify-content-center">
            <div class="col-lg-3 col-3">
                <select name="" id="">
                    <option value="">Select Status</option>
                  <option value="">Accepted</option>
                  <option value="">Requested</option>
                  <option value="">Declined</option>
                </select>
            </div>
            
          </div>
        </form>
      </div>
        
    </div>
  <!---->
  <section> 
        <div class="container-fluid">
          <div class="row justify-content-center">
            <div class="col-lg-3">
              <a href="#" class="prod-main-list">
               
                <!-- <div class="prod-img">
                  <img src="img/prod1.png" class="w-100" alt="">
                </div> -->
                <div class="prod-content">
                  <p class="m-0 date">3 days ago</p>
                  <div class="prod-name">
                    <h6>Need Plumber for fixing Bathroom Tap</h6>
                  </div>
                  <div class="prod-desc">
                    <p>We are a (type of organization) and a (MSME Type) MSME firm in (Business Category) with a __ years of experience and specialization in (Specialization) operating from (city),(state) and we also provide service to (Enter states/cities you can cater to).</p>
                  </div>
                  <div class="prod-req-btn">
                    <button type="button" class="btn req-btn decline"> Decline</button>
                    <button type="button" class="btn req-btn accept"> Accept</button>
                  </div>
                </div>
                
              </a>
            </div>
            <div class="col-lg-3">
              <a href="#" class="prod-main-list">
               
                <!-- <div class="prod-img">
                  <img src="img/prod1.png" class="w-100" alt="">
                </div> -->
                <div class="prod-content">
                  <p class="m-0 date">3 days ago</p>
                  <div class="prod-name">
                    <h6>Need Plumber for fixing Bathroom Tap</h6>
                  </div>
                  <div class="prod-desc">
                    <p>We are a (type of organization) and a (MSME Type) MSME firm in (Business Category) with a __ years of experience and specialization in (Specialization) operating from (city),(state) and we also provide service to (Enter states/cities you can cater to).</p>
                  </div>
                  <div class="prod-req-btn">
                    <button type="button" class="btn req-btn decline"> Decline</button>
                    <button type="button" class="btn req-btn accept"> Accept</button>
                  </div>
                </div>
                
              </a>
            </div>
            <div class="col-lg-3">
              <a href="#" class="prod-main-list">
               
                <!-- <div class="prod-img">
                  <img src="img/prod1.png" class="w-100" alt="">
                </div> -->
                <div class="prod-content">
                  <p class="m-0 date">3 days ago</p>
                  <div class="prod-name">
                    <h6>Need Plumber for fixing Bathroom Tap</h6>
                  </div>
                  <div class="prod-desc">
                    <p>We are a (type of organization) and a (MSME Type) MSME firm in (Business Category) with a __ years of experience and specialization in (Specialization) operating from (city),(state) and we also provide service to (Enter states/cities you can cater to).</p>
                  </div>
                  <div class="prod-req-btn">
                    <button type="button" class="btn req-btn decline"> Decline</button>
                    <button type="button" class="btn req-btn accept"> Accept</button>
                  </div>
                </div>
                
              </a>
            </div>
            
          </div>
        </div>
      </section>
    <section  class="bg-gray"> 
        <div class="container-fluid">
          <div class="row justify-content-center">
            <div class="col-lg-3">
              <a href="#" class="prod-main-list">
               
                <!-- <div class="prod-img">
                  <img src="img/prod1.png" class="w-100" alt="">
                </div> -->
                <div class="prod-content">
                  <p class="m-0 date">3 days ago</p>
                  <div class="prod-name">
                    <h6>Need Plumber for fixing Bathroom Tap</h6>
                  </div>
                  <div class="prod-desc">
                    <p>We are a (type of organization) and a (MSME Type) MSME firm in (Business Category) with a __ years of experience and specialization in (Specialization) operating from (city),(state) and we also provide service to (Enter states/cities you can cater to).</p>
                  </div>
                  <div class="prod-status">
                    <div class="dot dot-red"></div>
                    <p class="m-0 decline">Declined</p>
                  </div>
                </div>
                
              </a>
            </div>
            <div class="col-lg-3">
              <a href="#" class="prod-main-list">
               
                <!-- <div class="prod-img">
                  <img src="img/prod2.png" class="w-100" alt="">
                </div> -->
                <div class="prod-content">
                   <p class="m-0 date">3 days ago</p> 
                  <div class="prod-name">
                    <h6>Lorem ipsum dolor sit amet</h6>
                  </div>
                  <div class="prod-desc">
                    <p>We are a (type of organization) and a (MSME Type) MSME firm in (Business Category) with a __ years of experience and specialization in (Specialization) operating from (city),(state) and we also provide service to (Enter states/cities you can cater to).</p>
                  </div>

                  <div class="prod-status">
                    <div class="dot dot-green"></div>
                    <p class="m-0 Accepted">Accepted</p>
                </div>      

                </div>
              </a>
            </div>
            <div class="col-lg-3">
              <a href="#" class="prod-main-list">
               
                <!-- <div class="prod-img">
                  <img src="img/prod2.png" class="w-100" alt="">
                </div> -->
                <div class="prod-content">
                   <p class="m-0 date">3 days ago</p> 
                  <div class="prod-name">
                    <h6>Lorem ipsum dolor sit amet</h6>
                  </div>
                  <div class="prod-desc">
                    <p>We are a (type of organization) and a (MSME Type) MSME firm in (Business Category) with a __ years of experience and specialization in (Specialization) operating from (city),(state) and we also provide service to (Enter states/cities you can cater to).</p>
                  </div>

                  <div class="prod-status">
                    <div class="dot dot-yellow"></div>
                    <p class="m-0 requested">Requested</p>
                </div>      

                </div>
              </a>
            </div>
         
          </div>
        </div>
      </section>


     
   
@endsection