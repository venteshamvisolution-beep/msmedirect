 <header>
    <nav>
      <div class="container-fluid">
        <div class="row align-items-center justify-content-between">
          <div class="col-4 col-lg-2">
              <a href="{{url('/')}}" class="btn main-logo   p-0">
                <img src="{{asset('custom/img/logo.png')}}" alt="">
              </a>
          </div>
          <div class="col-lg-8 mobo-t-hide">
            <ul class="head-main-menu main-menu justify-content-center">
              <li class="position-relative ">
                <a href="{{url('about-us')}}">About MSME</a>
                
              </li>
              <li class="position-relative">
                <a href="{{url('charges')}}">Charges</a>
              </li>
            
              <li class="dropdown position-relative">
                <a href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">MSME Info <i class="feather icon-feather-chevron-down"></i></a>
                <ul class="dropdown-menu">
                  <li><a class="dropdown-item" href="{{url('msme-scheme')}}">MSME Scheme</a></li>
                  <li><a class="dropdown-item" href="{{url('news')}}">MSME News</a></li>
                  <!-- <li><a class="dropdown-item" href="#">Something else here</a></li> -->
                </ul>
              </li>
              <li class="dropdown position-relative">
                <a href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Categories<i class="feather icon-feather-chevron-down"></i></a>
                <ul class="dropdown-menu">
                  <li><a class="dropdown-item" href="{{url('listing/product')}}">MSME Products</a></li>
                  <li><a class="dropdown-item" href="{{url('listing/services')}}">MSME Services</a></li>
                  <!-- <li><a class="dropdown-item" href="#">Something else here</a></li> -->
                </ul>
              </li>
              <li class="">
                <a href="{{url('/contact-us')}}">Contact Us</a>
              </li>
              
            </ul>
          </div>
          <!-- <div class="col-3 mobo-t-hide">
             <form action="" class="search-form ">
                <div class="search-input">
                  <input type="text" class="" placeholder="Search Businesses and Services">
                  <i class="feather icon-feather-search"></i>
                </div>                  
             </form>
          </div> -->
          <div class="col-8 col-sm-8 col-md-8 col-lg-2">
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
                  <li class="position-relative ">
                    <button type="button" class="btn p-0" data-bs-toggle="modal" data-bs-target="#search"><i class="feather icon-feather-search"></i></button>
                    
                  </li>
                  <li class="position-relative">
                    <a href="{{url('/notifications')}}" class="ico-menu"><i class="feather icon-feather-bell"></i></a>
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
      <a href="{{url('about-msme')}}">About MSME</a>
      <a href="{{url('charges')}}">Charges</a>
      
      <div class="dropdown position-relative">
        <a href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">MSME Info <i class="feather icon-feather-chevron-down"></i></a>
        <ul class="dropdown-menu">
         
           <li><a class="dropdown-item" href="{{url('msme-scheme')}}">MSME Scheme</a></li>
                  <li><a class="dropdown-item" href="{{url('news')}}">MSME News</a></li>
          <!-- <li><a class="dropdown-item" href="#">Something else here</a></li> -->
        </ul>
      </div>
       
      <a href="{{url('contact-us')}}">Contact</a>
    </div>
  </header>