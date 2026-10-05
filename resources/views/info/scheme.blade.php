@extends('layouts.app')
@section('body_section')
@include('layouts.header')
    
    <section class="bg-blue">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="page-inner-title">
                        <h1>Goverment Schemes</h1>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div class="top-filter">
        <div class="container-fluid">
            <div class="row align-items-center">
            <div class="col-lg-4 col-8">
    <select id="schemeMaster" class="main-btn2 m-0" name="schemeMaster">
        <option value="">Select Scheme Category</option>
        <?php foreach($schemeMaster as $key => $schemeM){ ?>
            @if($param == $key)
            <option value="{{$key}}" selected>{{ucwords($schemeM)}}</option>
            
            @else
                
            <option value="{{$key}}" >{{ucwords($schemeM)}}</option>
            @endif
        <?php } ?>
        
    </select>
    </div>
    
    <div class="col-lg-4 text-right mobo-hide">
                <p class="m-0 mobo-hide">{{count($schemes)}} Records available</p>
            </div>
    
    </div>
    </div>
    </div>
    
    @foreach($schemes as $key => $schemeValue)
    @if($key >= 1)
        <hr>
    @endif
    <section >
        <div class="container">
          <div class="row">
            <div class="col-lg-12">
              <div class="main-title">
                <h3>{{$schemeValue->title}}</h3>              
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
                                @foreach(json_decode($schemeValue->details,true) as $key => $value)
                                  <tr>
                                    <td>{{$key+1}}</td>
                                    <td>{{$value['name']}}</td>
                                    <td>{{$schemeValue->category_name}}</td>
                                    <td><a href="{{$value['link']}}" target="_blank" class="btn"><i class="fa-solid fa-arrow-right"></i></a></td>
                                  </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </section>
    @endforeach
                    
@endsection
@section('page_js')

<script>
        $(document).ready(function() {
            $('#schemeMaster').change(function() {
                var selectedValue = $(this).val();
                if (selectedValue !== '') {
                    var url = "<?php echo route('msmeScheme'); ?>";
                    // Assuming your search URL format is '/search?param=value'
                    var searchURL = url+'?param=' + selectedValue;
                    // You can modify the above line to match your actual URL structure
                    window.location.href = searchURL;
                }
            });
        });
</script>
@endsection