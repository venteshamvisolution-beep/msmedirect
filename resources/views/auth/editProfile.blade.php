@extends('layouts.app')
@section('body_section')
<section class="login-section reg-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <div class="login-sec registration-sec">
                        <div class="login-logo">
                            <a href="index.html"><img src="{{asset('custom/img/logo.png')}}" alt=""></a>
                        </div>
                        <form action="{{route('updateRegister')}}" method="POST"enctype="multipart/form-data">
                            @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
@if(session('message'))
    <div class="alert alert-success">
        {{ session('message') }}
    </div>
@endif

                            @csrf
                            <input type="hidden" name="addEdit" value="{{$userProfile->id}}">
                            <div class="form-box">
                                <div class="row ">
                                    <div class="col-12">
                                        <h6>Organization Details</h6>
                                    </div>
                                    <div class="col-lg-6 col-md-6">
                                        <input type="text" placeholder="Organization Name" id="organization_name" name="organization_name" value="{{ $userProfile->organization_name }}" required>

                                    </div>
                                    <div class="col-lg-6 col-md-6">
                                        <select name="business_type" id="business_type" required>
                                            <option value="" {{ $userProfile->business_type == '' ? 'selected' : '' }}>Select Type of Business</option>
                                            <option value="0" {{ $userProfile->business_type == '0' ? 'selected' : '' }}>Product</option>
                                            <option value="1" {{ $userProfile->business_type == '1' ? 'selected' : '' }}>Services</option>
                                        </select>
                                    </div>
                                    <div class="col-lg-6 col-md-6">
                                        <select name="type_organization" id="type_organization" required>
    <option value="" {{ $userProfile->type_organization == '' ? 'selected' : '' }}>Select Type of Organization</option>
    <option value="Sole Proprietorship" {{ $userProfile->type_organization == 'Sole Proprietorship' ? 'selected' : '' }}>Sole Proprietorship</option>
    <option value="Start Up" {{ $userProfile->type_organization == 'Start Up' ? 'selected' : '' }}>Start Up</option>
    <option value="Partnership" {{ $userProfile->type_organization == 'Partnership' ? 'selected' : '' }}>Partnership</option>
    <option value="LLP" {{ $userProfile->type_organization == 'LLP' ? 'selected' : '' }}>LLP</option>
    <option value="Pvt. Ltd." {{ $userProfile->type_organization == 'Pvt. Ltd.' ? 'selected' : '' }}>Pvt. Ltd.</option>
    <option value="Ltd." {{ $userProfile->type_organization == 'Ltd.' ? 'selected' : '' }}>Ltd.</option>
    <option value="Cooperative" {{ $userProfile->type_organization == 'Cooperative' ? 'selected' : '' }}>Cooperative</option>
    <option value="AOP" {{ $userProfile->type_organization == 'AOP' ? 'selected' : '' }}>AOP</option>
    <option value="HUF" {{ $userProfile->type_organization == 'HUF' ? 'selected' : '' }}>HUF</option>
    <option value="Trusts" {{ $userProfile->type_organization == 'Trusts' ? 'selected' : '' }}>Trusts</option>
    <option value="NGO" {{ $userProfile->type_organization == 'NGO' ? 'selected' : '' }}>NGO</option>
</select>

                                    </div>
                                   
                                    <div class="col-lg-6">
                                       
                                            <select id="msme_type" name="msme_type" required>
    <option value="" {{ $userProfile->msme_type == '' ? 'selected' : '' }}>Select MSME Type</option>
    <option value="Start ups" {{ $userProfile->msme_type == 'Start ups' ? 'selected' : '' }}>Start ups</option>
    <option value="Micro" {{ $userProfile->msme_type == 'Micro' ? 'selected' : '' }}>Micro: Investment less than Rs.1 Cr. and Turnover less than Rs. 5 crore</option>
    <option value="Small" {{ $userProfile->msme_type == 'Small' ? 'selected' : '' }}>Small: Investment less than Rs.10 Cr. and Turnover less than Rs. 50 crore</option>
    <option value="Medium" {{ $userProfile->msme_type == 'Medium' ? 'selected' : '' }}>Medium: Investment less than Rs.50 Cr. and Turnover less than Rs. 250 crore</option>
    <option value="Beyond SME" {{ $userProfile->msme_type == 'Beyond SME' ? 'selected' : '' }}>Beyond SME: Investment less than Rs.250 Cr. and Turnover less than Rs. 500 crore</option>
</select>

                                
                                    </div>
        
                                    <div class="col-lg-6">
                                        
                                        <select id="business_category" name="business_category" required>
    <option value="">Select Business Category</option>
    @foreach($businessCategory as $businessCat)
        <option data-type="{{ $businessCat->type }}" value="{{ $businessCat->id }}" 
            {{ $userProfile->business_category == $businessCat->id ? 'selected' : '' }}>
            {{ $businessCat->name }}
        </option>
    @endforeach
</select>

                                    
                                    
                                </div>
    
                                <div class="col-lg-6 buz_sub_cat">
                                    <!--only for goods  -->
                                    
                                       <select id="business_sub_category" name="business_sub_category" required>
    <option value="">Select Business Sub-Category</option>
    @foreach($subbusinessCategory as $subbusinessCat)
        <option value="{{ $subbusinessCat->id }}" 
            {{ $userProfile->business_sub_category == $subbusinessCat->id ? 'selected' : '' }}>
            {{ $subbusinessCat->name }}
        </option>
    @endforeach
</select>

                                    
                                </div>
                                    <div class="col-lg-12">
                                        
                                           <textarea id="specialization" name="specialization" placeholder="Enter specialization (Example: For Equity Financing - SME Listing)" rows="2">{{ $userProfile->specialization }}</textarea>

                                   
                                    </div>
                                    
                                </div>
                            </div>
                           <div class="form-box">
    <div class="row ">
        <div class="col-12">
            <h6>Contact Details</h6>
        </div>
        <div class="col-lg-6 col-md-6">
            <input type="number" placeholder="Contact Number" id="contact_no" name="contact_no" value="{{ $userProfile->contact_no }}" required>
        </div>
        <div class="col-lg-6 col-md-6">
            <input type="email" placeholder="Email Address" id="email" name="email" value="{{ $userProfile->email }}" required>
        </div>

        <div class="col-lg-12">
            <textarea id="address" name="address" required placeholder="Enter Address" rows="2">{{ $userProfile->address }}</textarea>
        </div>

        <div class="col-lg-4 col-md-4">
            <input type="number" placeholder="Pincode" id="pincode" name="pincode" value="{{ $userProfile->pincode }}" required>
        </div>
        <div class="col-lg-4 col-md-4">
            <input type="text" placeholder="City" id="city" name="city" value="{{ $userProfile->city }}" readonly>
        </div>
        <div class="col-lg-4 col-md-4">
            <input type="text" placeholder="State" id="state" name="state" value="{{ $userProfile->state }}" readonly >
        </div>
    </div>
</div>

<div class="form-box">
    <div class="row align-items-end">
        <div class="col-12">
            <h6>Other Details</h6>
        </div>
        <div class="col-12">
            <p for="">Which business category would you like to connect to / provide service to?</p>
            <div class="checkboc-sec mb-5">
                <div class="cus-check-contain">
                    <input type="checkbox" id="chk-startup" name="business_size[]" value="startup" {{ in_array('startup', json_decode($userProfile->business_size,true)) ? 'checked' : '' }}>
                    <label for="chk-startup" class="check-label">Startups</label>
                </div>
                <div class="cus-check-contain">
                    <input type="checkbox" id="chk-micro" name="business_size[]" value="micro" {{ in_array('micro', json_decode($userProfile->business_size,true)) ? 'checked' : '' }}>
                    <label for="chk-micro" class="check-label">Micro</label>
                </div>
                <div class="cus-check-contain">
                    <input type="checkbox" id="chk-small" name="business_size[]" value="small" {{ in_array('small', json_decode($userProfile->business_size,true)) ? 'checked' : '' }}>
                    <label for="chk-small" class="check-label">Small</label>
                </div>
                <div class="cus-check-contain">
                    <input type="checkbox" id="chk-medium" name="business_size[]" value="medium" {{ in_array('medium', json_decode($userProfile->business_size,true)) ? 'checked' : '' }}>
                    <label for="chk-medium" class="check-label">Medium</label>
                </div>
                <div class="cus-check-contain">
                    <input type="checkbox" id="chk-sme" name="business_size[]" value="beyond-sme" {{ in_array('beyond-sme', json_decode($userProfile->business_size,true)) ? 'checked' : '' }}>
                    <label for="chk-sme" class="check-label">Beyond SME</label>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-md-6">
            <input type="text" placeholder="Enter Years of experience" id="experience" name='experience' value="{{ $userProfile->experience }}" required>
        </div>
        <div class="col-lg-6 col-md-6">
            <input type="text" placeholder="Enter GST number, PAN number or Udyam Aadhar number" name="document_no" id="document_no" value="{{ $userProfile->document_no }}" required>
        </div>
        <div class="col-lg-6 col-md-6">
            <label for="" class="m-0">Upload Document of entered ID.</label>
            <input type="file" name="document_file[]" id="document_file"  mutiple> 
             @foreach(json_decode($userProfile->document_file,true) as $key => $value)
                <a href='{{asset("storage/{$value}")}}' target="_blank"><p class="reg-ans">{{str_replace('userdata/','',$value)}}</p></a>
                @endforeach
        </div>
        <div class="col-lg-6 col-md-6">
            <input type="text" placeholder="Enter states/cities you can provide service to" name="state_city" id="state_city" value="{{ $userProfile->state_city }}" required>
        </div>
    </div>
</div>

                            <div class="form-box">
                                <div class="row align-items-end">
                                    <div class="col-12">
                                        <h6>Description</h6>
                                        <p id="description">We are a {{$userProfile->organization_name}} and a {{$userProfile->msme_type}} MSME firm in {{$userProfile->msme_type}} with a {{$userProfile->experience}} years of experience and specialization in {{$userProfile->specialization}} operating from {{$userProfile->city}},{{$userProfile->state}}) and we also provide service to {{$userProfile->state_city}}.</p>
                                    </div>
                                    
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-4">
                                    <button class=" main-btn2 w-100" type="submit"> REGISTER NOW</button>
                                </div>
                                
                            </div>
                            
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('page_js')
<script>

    $(document).ready(function () {
        $(document).on('change', '#business_type', function(){
            var business_type = $(this).val();
            if(business_type === '1'){ // Services
                $("#business_sub_category").hide();
                $("#type_organization option[value=HUF]").hide();
                $("#type_organization option[value=Trusts]").hide();
                $("#type_organization option[value=NGO]").hide();
                $("#business_category option[data-type=0]").hide();
                $("#business_category option[data-type=1]").show();
            }else{                  // Product
                $("#business_sub_category").show();
                $("#type_organization option[value=HUF]").show();
                $("#type_organization option[value=Trusts]").show();
                $("#type_organization option[value=NGO]").show();
                $("#business_category option[data-type=1]").hide();
                $("#business_category option[data-type=0]").show();
            }
            descriptionText();
        });
        
        
        $(document).on('keyup', '#specialization,#state_city,#organization_name', function(){
            descriptionText();
        
        });
        
        $(document).on('change', '#type_organization,#msme_type,#business_category,#business_sub_category', function(){
            descriptionText();
        
        });
        
        
        function descriptionText(){
            if($('#business_type').val() == 0){
                var txt = 'We are a '+$('#organization_name').val()+' and a '+$('#msme_type option:selected').val()+' MSME firm in '+$('#business_sub_category option:selected').text()+' '+$('#business_category option:selected').text()+' with a '+$('#experience').val()+' years of experience and specialization in '+$('#specialization').val()+' operating from '+$('#city').val()+', '+$('#state').val()+' and we also provide service to '+$('#state_city').val()+'.';
            }else{
                var txt = 'We are a '+$('#organization_name').val()+' and a '+$('#msme_type option:selected').val()+' MSME firm in '+$('#business_category option:selected').text()+' with a '+$('#experience').val()+' years of experience and specialization in '+$('#specialization').val()+' operating from '+$('#city').val()+', '+$('#state').val()+' and we also provide service to '+$('#state_city').val()+'.';
            }
            $('#description').text(txt);
        }
      
      
      
        $('#pincode').on('input', function () {
            var pincode = $(this).val();
            if (pincode.length > 6) {
                $(this).val(pincode.slice(0, 6));
            }
            if (pincode.length === 6) {
                $.ajax({
                    type: 'POST',
                    url: '{{ route("getPincodeData") }}', // Replace with your server-side script URL
                    data: {pincode: pincode},
                    success: function (response) {
                        $('#city').val(response.city);
                        $('#state').val(response.state);
                        
                        
                        descriptionText();
                        
                        
                    },
                    error: function () {
                        $('#city').val('');
                        $('#state').val('');
                    }
                });
            }
        });
    });  
</script>
@endsection