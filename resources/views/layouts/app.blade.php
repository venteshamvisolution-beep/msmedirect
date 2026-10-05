<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>MSME Direct</title>
         <link rel="icon" type="image/x-icon" href="/public/custom/img/favicon.ico">
                  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link rel="stylesheet" href="{{asset('custom/css/font-icons.min.css')}}">
        
        <link rel="stylesheet" href="{{asset('custom/css/style.css')}}">
        <link rel="stylesheet" href="{{asset('custom/css/responsive.css')}}">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        
        <link rel="stylesheet" href="{{asset('custom/css/swiper.css')}}">
        <link rel="stylesheet" href="{{asset('custom/css/bootstrap.css')}}">
        <style>
            .search-input button {
                position: absolute;
                right: 5%;
                top: 22%;
            }
        </style>
    </head>
    <body>
        @if(session('message'))
            <div id="flash-message-alert" class="alert alert-success alert-dismissible fade show" role="alert" style="position: fixed; top: 80px; right: 20px; z-index: 99999; max-width: 500px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); border-left: 5px solid #28a745; background-color: #d4edda; color: #155724; padding: 15px 20px; border-radius: 8px;">
                <div class="d-flex align-items-center justify-content-between">
                    <div style="font-size: 15px; line-height: 1.4;">
                        <strong><i class="fa fa-check-circle me-2"></i> Success:</strong> {{ session('message') }}
                    </div>
                    <button type="button" class="btn-close ms-3" onclick="document.getElementById('flash-message-alert').remove()" style="background: none; border: none; font-size: 20px; cursor: pointer; color: #155724; line-height: 1;">&times;</button>
                </div>
            </div>
            <script>
                setTimeout(function() {
                    var flashAlert = document.getElementById('flash-message-alert');
                    if (flashAlert) {
                        flashAlert.style.transition = 'opacity 1s ease';
                        flashAlert.style.opacity = '0';
                        setTimeout(function() { if(flashAlert) flashAlert.remove(); }, 1000);
                    }
                }, 60000);
            </script>
        @endif

        @yield('body_section')
        <footer>
          <div class="container-fluid">
            <div class="row">
              <div class="col-12">
                  <div class="footer-logo">
                    <a href="index.html"><img src="{{asset('custom/img/logo.png')}}" alt=""></a>
                  </div>            
              </div> 
            </div>
            <div class="row">
              <div class="col-lg-6">
                <ul class="bottom-foot-menu">
                  <li><a href="{{url('/term-condition')}}" class="">Terms & Conditions</a></li>
                  <li><a href="{{url('/privacy-policy')}}" class="">Privacy Policy</a></li>
                  <li><a href="{{url('/disclaimer')}}" class="">Disclaimer</a></li>
                  <li><a href="{{url('/refund-policy')}}" class="">Refund Policy</a></li>
                </ul>
              </div>
              <div class="col-lg-6">
                <div class="bottom-foot-text">
                  <p class="text-right">Copyright © 2023 MSME Direct. All rights reserved.</p>
                </div>
                
              </div>
              
            </div>
          </div>

        </footer>
        
        @php
            $stateList  = DB::table('pincode')->select('State')->distinct('State')->orderBy('state','ASC')->get()->toArray();
        @endphp
        <div class="head-search modal fade" id="search" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
              
                    <div class="modal-body">
                        <div class="container">
                  <div class="row">
                    <div class="col-12">
                    <div class="search-form">
                        <div class="search-input">
                          <input type="text" class="" placeholder="Search by Businesses, Services or Location" id="all_search_home_mob">
                          <button type="button" class="btn" id="all_home_btns_mob"><i class="feather icon-feather-search"></i></button>
                        </div>
                    </div>
                        
                        <!--<div class="text-center mt-3"> 
                          <select name="" id="all_home_location">
                            <option value="">Location</option>
                             @foreach($stateList as $key => $value)
                            
                            <option value="{{$value->State}}">{{$value->State}}</option>
                            
                            @endforeach
                           
                          </select> 
                        </div>-->
                        <!--<div class="text-center">
                             <button type="button" class="btn" id="all_home_btns"><i class="feather icon-feather-search"></i></button>
                        </div>-->
                                         
           
                    </div>
                  </div>
                </div>
                
              </div>
              
            </div>
          </div>
        </div>

          <script>
            window.onload = function() {
  document.getElementById('button1').onclick = function() {
    document.getElementById('modalOverlay').style.display = 'none'
  };
};
    </script>
    <script>
        function openNav() {
          document.getElementById("mySidenav").style.width = "360px";
        }
        
        function closeNav() {
          document.getElementById("mySidenav").style.width = "0";
        }
    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
    <script>
        $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
    </script>
    
    <script>
    document.getElementById('all_home_btn').addEventListener('click', function() {
        // e.preventDefault();
        var allsearchHomeValue = document.getElementById('all_search_home').value.trim();
        // var allhomeLocationValue = document.getElementById('all_home_location').value.trim();
        
        if(allsearchHomeValue == ''){
            alert('Please Enter or Select Location');
            return false;
        }
    
        // Base URL
        var baseUrl = "{{ url('/category/all') }}";
    
        // Constructing the URL
        var searchUrl = baseUrl + "?search_home=" + encodeURIComponent(allsearchHomeValue);
    
        console.log(searchUrl);
        window.location.assign(searchUrl);
    });
    
    document.getElementById('all_home_btns_mob').addEventListener('click', function() {
        // e.preventDefault();
        var allsearchHomeValue = document.getElementById('all_search_home_mob').value.trim();
        
        if(allsearchHomeValue == ''){
            alert('Please Enter or Select Location');
            return false;
        }
    
        // Base URL
        var baseUrl = "{{ url('/category/all') }}";
    
        // Constructing the URL
        var searchUrl = baseUrl + "?search_home=" + encodeURIComponent(allsearchHomeValue);
    
        console.log(searchUrl);
        window.location.assign(searchUrl);
    });
</script>

<script>
    $(document).on('click', '.proceedToPayment', function () {
        // alert ('dsg'); return false;
        var productservice = $(this).data('encode');
        var coupon_discount = $('#is_coupon_automatic').val();
        var searchUrl = "{{ url('/accept-connection') }}/"+productservice+"/"+coupon_discount;
        window.location.href = searchUrl;
    });
    
    $(document).on('click', '#couponBtn', function () {
        var coupon = $('#coupon').val();
        $.ajax({
            type: "POST",
            url: "<?=route('checkCoupon')?>",
            data: {coupon:coupon},
            dataType: 'json',
            success : function(response){
              console.log(response);
                if(response.code == '1'){
                    var amt = $('#buzAmount').text().substring(1);
                    var discount = response.result.discount_percentage;
                    var discount_amount = parseFloat(amt *(discount / 100));
                    
                    $('#coupon_discount').css('color', 'black');
                    $('#coupon_discount').html('- ₹ '+discount_amount);
                    $('#perce').html('( '+discount+' % )');
                    $('#is_coupon_automatic').val(discount_amount);
                    var tot = parseFloat(amt - discount_amount);
                    $('.total-amt').html('₹ '+tot);
                    
                }else if(response.code == '0'){
                    $('#coupon_discount').css({ 'color': 'red'});
                    $('#coupon_discount').html('Invalid Coupon');
                    
                }
            }
        });
        return false;
        
    });
</script>   

    @yield('page_js')
    


    </body>
    
</html>