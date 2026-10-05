@extends('layouts.app')
@section('body_section')
@include('layouts.header')
 <section class="cover-img position-relative" style="background-image: url({{asset('custom/img/services-bannner.jpg')}});">
      <div class="container">
          <div class="row align-items-center">
              <div class="col-lg-6">
                  <div class="page-inner-title">
                      <h1>MSME Listing Page - {{ucwords($businessType)}}</h1>
                  </div>
              </div>
          </div>
      </div>
    </section>
    @if(session('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif

    @if(session('requestConnectionMessage'))
        <div class="alert alert-success">
           {{ session('requestConnectionMessage') }}
        </div>
    
    @endif
    @php
        Session::forget('requestConnectionMessage');
    @endphp
    
    

  <!--Sticky Filter-->
    <div class="top-filter">
      <div class="container-fluid">
        <form action="">
          <div class="row align-items-center">
            
            <div class="col-lg-4 col-8">
              <button type="button" class="btn main-btn2 m-0" data-bs-toggle="modal" data-bs-target="#exampleModal">Select Categories</button>
            </div>
            <div class="col-lg-4 text-right mobo-hide">
                <p class="m-0 mobo-hide">{{count($userDatas)}} Records available</p>
            </div>
          </div>
        </form>
      </div>
        
    </div>
  <!---->
 <section> 
      <div class="container-fluid">
        <div class="row">
        @foreach($userDatas as $userData)
          <div class="col-lg-3">
            <a href='{{url("/request-connection/{$userData->enc_id}")}}' class="prod-main-list">
             
              <!-- <div class="prod-img">
                <img src="img/prod1.png" class="w-100" alt="">
              </div> -->
              <div class="prod-content">
                <div class="prod-name">
                  <h6>{{$userData->name}}</h6>
                </div>
                <div class="prod-desc">
                  <p>{{$userData->business_description}}</p>
                </div>
              </div>
              @php
                $changeButtonText = 'Connect Now';
                
              @endphp
               @if (!Auth::guest())
              @foreach($connections as $connection)
                
                @php
                    $status = true;
                    
                @endphp
              
                @if(($connection->conn_user_data == $userData->user_data_id))
                    @php
                    
                        $status = false;
                    @endphp
                @endif
                
                @if($connection->from_user_data == $userData->user_data_id)
                    @php
                    
                        $status = false;
                    @endphp
                @endif
                
                @if($status)
                    @php
                       continue; 
                    @endphp
                @endif
              
              
                @if($connection->status == 0)
                    @if($connection->from_id == Auth::user()->id)
                        @php
                            $changeButtonText = 'Connection Requested';
                            break;
                        @endphp
                    @endif
                    
                    @if($connection->conn_id == Auth::user()->id)
                        @php
                            $changeButtonText = 'Connection Requested';
                            break;
                        @endphp
                    @endif
                    
                @endif
                
                @if($connection->status == 1)
                    @if($connection->from_id == Auth::user()->id)
                        @php
                            $changeButtonText = 'Connection Accepted';
                            break;
                        @endphp
                    @endif
                    
                    @if($connection->conn_id == Auth::user()->id)
                        @php
                            $changeButtonText = 'Connection Accepted';
                            break;
                        @endphp
                    @endif
                    
                @endif
                
                @if($connection->status == 2)
                    @if($connection->from_id == Auth::user()->id)
                        @php
                            $changeButtonText = 'Connection Declined';
                            break;
                        @endphp
                    @endif
                    
                    @if($connection->conn_id == Auth::user()->id)
                        @php
                            $changeButtonText = 'Connection Declined';
                            break;
                        @endphp
                    @endif
                    
                @endif
                
                @if($connection->status == 3)
                    @if($connection->from_id == Auth::user()->id)
                        @php
                            $changeButtonText = 'Connected';
                            break;
                        @endphp
                    @endif
                    
                    @if($connection->conn_id == Auth::user()->id)
                        @php
                            $changeButtonText = 'Connected';
                            break;
                        @endphp
                    @endif
                    
                @endif
              
              @endforeach
              @endif
              <div class="prod-btn">
                <button type="button" class="main-btn2 w-100 m-0">{{$changeButtonText}}</button>
              </div>
            </a>
          </div>
          @endforeach
         
        </div>
      </div>
    </section>
    
    <div class="modal filter-cat-modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title fs-5" id="exampleModalLabel">Select Categories</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
              <div class="checkbox-sec row">
                  @foreach($businessCategory as $key => $businessCat)
                  <div class="check-col col-lg-3 col-md-3 col-12">
                    <div class="main-check">
                        @if(in_array($businessCat->id,$checkedValues))
                            @php
                                $class = 'checked';
                                
                            @endphp
                        @else
                            @php
                                $class = '';
                            @endphp
                        @endif
                      <input type="checkbox" name="categoryCheck" {{$class}} class="categoryCheckBox" id="check{{$key}}" value="{{$businessCat->id}}" >
                      <label for="check{{$key}}" class="label-check">{{$businessCat->name}}</label>
                    </div>
                  </div>
                  @endforeach
                  
              </div>
          </div>
          <div class="modal-footer">
    
            <button type="button" class="main-btn2" id="checkButton">Save Categories</button>
          </div>
        </div>
      </div>
    </div>
@endsection

@section('page_js')
<script>
    // Function to get an array of values for checked checkboxes
    function getCheckedCheckboxValues() {
        var checkboxes = $('.categoryCheckBox');
        var checkedValues = [];

        checkboxes.each(function () {
            if ($(this).prop('checked')) {
                checkedValues.push($(this).val());
            }
        });

        return checkedValues;
    }

    // Function to update the URL with checked values
    function updateUrlWithCheckedValues(checkedValues) {
        var baseUrl = window.location.href;

        // Remove the existing checkedValues parameter
        baseUrl = baseUrl.replace(/[\?&]checkedValues=[^&]+/, '');
        
        // Add the new checkedValues parameter
        var searchUrl = baseUrl + (baseUrl.includes('?') ? '&' : '?') + 'checkedValues=' + checkedValues.join(',');
        
        window.location.href = searchUrl;
    }

    // Button click event
    $('#checkButton').on('click', function () {
        var checkedValuesArray = getCheckedCheckboxValues();
        console.log('Values of checked checkboxes: ', checkedValuesArray);
        
        // Update the URL with checked values
        updateUrlWithCheckedValues(checkedValuesArray);
    });
</script>
@endsection