 <header>
    <nav>
      <div class="container-fluid">
        <div class="row align-items-center justify-content-between">
          <div class="col-4 col-lg-2">
              <a href="{{url('/')}}" class="btn main-logo   p-0">
                <img src="{{asset('custom/img/logo.png')}}" alt="">
              </a>
          </div>
          <div class="col-lg-5 mobo-t-hide">
            <ul class="head-main-menu main-menu justify-content-center">
              <li class="position-relative ">
                <a href="{{url('category/product')}}">Products</a>
              </li>
              <li class="position-relative">
                <a href="{{url('category/services')}}">Services</a>
              </li>
              <!-- <li class="position-relative">-->
              <!--  <a href="{{url('/charges')}}">Connection Fee</a>-->
              <!--</li>-->
              
              <li class="dropdown position-relative">
                <a href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Connection <i class="fa-solid fa-angle-down"></i></a>
                <ul class="dropdown-menu">
                  <li><a class="dropdown-item" href="{{url('charges')}}">Connection fees</a></li>
                  <li><a class="dropdown-item" href="{{url('user-connections')}}">Your connections</a></li>
                  <!-- <li><a class="dropdown-item" href="#">Something else here</a></li> -->
                </ul>
              </li>
            
              <li class="dropdown position-relative">
                <a href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">MSME Corner <i class="fa-solid fa-angle-down"></i></a>
                <ul class="dropdown-menu">
                  <li><a class="dropdown-item" href="{{url('news')}}">MSME News</a></li>
                  <li><a class="dropdown-item" href="{{url('msme-scheme')}}">MSME Schemes</a></li>
                  <!-- <li><a class="dropdown-item" href="#">Something else here</a></li> -->
                </ul>
              </li>
              <li class="dropdown position-relative">
                <a href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">About Us<i class="fa-solid fa-angle-down"></i></a>
                <ul class="dropdown-menu">
                  <li><a class="dropdown-item" href="{{url('about-us')}}">About MSME DIrect</a></li>
                  <li><a class="dropdown-item" href="{{url('contact-us')}}">Contact Us</a></li>
                  <!-- <li><a class="dropdown-item" href="#">Something else here</a></li> -->
                </ul>
              </li>
              <!--<li class="">-->
              <!--  <a href="{{url('/contact-us')}}">Contact Us</a>-->
              <!--</li>-->
              
            </ul>
          </div>
          <div class="col-3 mobo-t-hide">
             <div  class="search-form ">
                <div class="search-input">
                  <input type="text" class="" id="all_search_home" placeholder="Search by Businesses, Services or Location">
                  <button type="button" class="btn"  id="all_home_btn"><i class="fa-solid fa-magnifying-glass"></i></button>
                </div>                  
             </div>
          </div>
          <div class="col-8 col-sm-8 col-md-8 col-lg-1">
            <div class="head-menu-flex justify-content-end">
              <!-- <div class="location-dropdown mobo-t-hide">
                <select name="" id="">
                  <option value="">Location</option>
                  <option value="">Mumbai</option>
                  <option value="">Bengaluru</option>
                  <option value="">Delhi</option>
                </select> 
              </div> -->
                <ul class="head-main-menu ">
                  <li class="position-relative des-t-hide">
                    <button type="button" class="btn  p-0" data-bs-toggle="modal" data-bs-target="#search"><i class="feather icon-feather-search"></i></button>
                    
                  </li>
                  <li class="position-relative">
                    <a href="{{url('/notifications')}}" class="ico-menu"><i class="fa-solid fa-bell"></i></a>
                    <span class="noti-dot"></span>
                  </li>
                
                  <li class="">
                        @if (Auth::check())
                            <a href="{{ route('logout') }}" class="btn p-0">LOGOUT</a>
                        @else
                            <a href="{{ route('login') }}" class="btn p-0">LOGIN</a>
                        @endif
                  </li>
                  <li class="des-t-hide">
                    <button type="button" onclick="openNav()" class="btn ham-menu p-0">
                      <span></span>
                      <span></span>
                      <span></span>
                    </button>
                  </li>
                </ul>
                
            </div>
            
          </div>
        </div>
      </div>
    </nav>  
    <div id="mySidenav" class="sidenav">
      <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
            <a href="{{url('category/product')}}">Products</a>
     <a href="{{url('category/services')}}">Services</a>
      
      <div class="dropdown position-relative">
        <a href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Connection <i class="fa-solid fa-angle-down"></i></a>
        <ul class="dropdown-menu">
         
           <li><a class="dropdown-item" href="{{url('charges')}}">Connection fees</a></li>
                  <li><a class="dropdown-item" href="{{url('user-connections')}}">Your connections</a></li>
          <!-- <li><a class="dropdown-item" href="#">Something else here</a></li> -->
        </ul>
      </div>
      
      <div class="dropdown position-relative">
        <a href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">MSME Corner <i class="fa-solid fa-angle-down"></i></a>
        <ul class="dropdown-menu">
         
           <li><a class="dropdown-item" href="{{url('msme-scheme')}}">MSME Scheme</a></li>
                  <li><a class="dropdown-item" href="{{url('news')}}">MSME News</a></li>
          <!-- <li><a class="dropdown-item" href="#">Something else here</a></li> -->
        </ul>
      </div>
      
      <div class="dropdown position-relative">
        <a href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">About us <i class="fa-solid fa-angle-down"></i></a>
        <ul class="dropdown-menu">
         
           <li><a class="dropdown-item" href="{{url('about-us')}}">About MSME DIrect</a></li>
                  <li><a class="dropdown-item" href="{{url('contact-us')}}">Contact Us</a></li>
          <!-- <li><a class="dropdown-item" href="#">Something else here</a></li> -->
        </ul>
      </div>
      

</div>
  </header>