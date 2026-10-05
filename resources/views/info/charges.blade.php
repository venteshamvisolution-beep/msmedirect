@extends('layouts.app')
@section('body_section')
@include('layouts.header')
  <section class="bg-blue">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="page-inner-title">
                        <h1>Connection Fees</h1>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section>
      <div class="container">
        <!--<div class="row mb-5">-->
        <!--  <div class="col-lg-12">-->
        <!--    <div class="col-lg-12">-->
        <!--      <div class="main-title mb-4">-->
        <!--        <h3>If both accept, the charges payable would be as below.-->
        <!--        </h3>              -->
        <!--      </div>-->
        <!--    </div>-->
        <!--      <div class="main-title">-->
        <!--          <h2>MSME Connector</h2>-->
        <!--          <p>MSME which want to connect to another MSME to avail goods or services.</p>-->
        <!--      </div>-->
        <!--  </div>-->
        <!--  <div class="col-12">-->
        <!--    <table class="table  main-charges-tbl">-->
                    
        <!--      <tbody>-->
        <!--        <tr>-->
        <!--          <td class="text-center"><img src="{{asset('custom/img/msme-app.png')}}" alt=""></td>-->
        <!--          <td>MSME Applicant</td>-->
        <!--          <td><span class="rate">₹500/-</span></td>-->
        <!--        </tr>-->
                
              
        <!--      </tbody>-->
        <!--    </table>-->
        <!--  </div>-->
        <!--</div>-->
        

        <div class="row">
          <div class="col-lg-12">
            
              <div class="main-title">
                  <!--<h2>MSME Connectee</h2>-->
                  <p>If both accept, the charges payable would be as below.</p>
              </div>
          </div>
          <div class="col-12">
            <div class="table-responsive">
              <table class="table main-charges-tbl">
                
                <tbody>
                @php
                    $charges = DB::table('charges')->get()->toArray();
                @endphp
                  @foreach($charges as $charge)
                  <tr>
                    <td><img src=
                    '{{asset("/storage/{$charge->image}")}}' alt=""></td>
                    <td>{{$charge->name}}</td>
                    <td><span class="rate">₹{{$charge->price}}/-</span></td>
                  </tr>
                  @endforeach
                 
                
                </tbody>
              </table>
            </div>
            
          </div>
        </div>
      </div>
    </section>
  
@endsection