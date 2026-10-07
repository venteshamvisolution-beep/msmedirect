@extends('layouts.app')
@section('body_section')
<section class="login-section reg-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <div class="login-sec registration-sec">
                        <div class="login-logo">
                            <a href="/"><img src="{{asset('custom/img/logo.png')}}" alt=""></a>
                        </div>
                        <div style="padding-top:20px">
                            
                            <h5 style="text-align:center">Registration is Free</h5>
<h5 style="text-align:center">Fee to be charged only once both parties are connected</h5></p>
                            
                        </div>
                        
                        <form action="{{route('saveRegister')}}" method="POST"enctype="multipart/form-data">
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
                            <input type="hidden" name="msme_business_data_id" value="{{ old('msme_business_data_id', $businessData->id ?? session('msme_business_data_id', '')) }}">
                            <div class="form-box">
                                <div class="row ">
                                    <div class="col-12">
                                        <h6>Organization Details</h6>
                                    </div>
                                    <div class="col-lg-6 col-md-6">
                                        <input type="text" placeholder="Organization Name" id="organization_name" name="organization_name" value="{{ old('organization_name', $businessData->company ?? '') }}">

                                    </div>
                                    <div class="col-lg-6 col-md-6">
                                        <select name="business_type" id="business_type">
                                            <option value="" {{ old('business_type') == '' ? 'selected' : '' }}>Select Type of Business</option>
                                            <option value="0" {{ old('business_type') == '0' ? 'selected' : '' }}>Product</option>
                                            <option value="1" {{ old('business_type') == '1' ? 'selected' : '' }}>Services</option>
                                        </select>
                                    </div>
                                    <div class="col-lg-6 col-md-6">
                                        <select name="type_organization" id="type_organization">
                                            <option value="" {{ old('type_organization') == '' ? 'selected' : '' }}>Select Type of Organization</option>
                                            <option value="Sole Proprietorship" {{ old('type_organization') == 'Sole Proprietorship' ? 'selected' : '' }}>Sole Proprietorship</option>
                                            <option value="Start Up" {{ old('type_organization') == 'Start Up' ? 'selected' : '' }}>Start Up</option>
                                            <option value="Partnership" {{ old('type_organization') == 'Partnership' ? 'selected' : '' }}>Partnership</option>
                                            <option value="LLP" {{ old('type_organization') == 'LLP' ? 'selected' : '' }}>LLP</option>
                                            <option value="Pvt. Ltd." {{ old('type_organization') == 'Pvt. Ltd.' ? 'selected' : '' }}>Pvt. Ltd.</option>
                                            <option value="Ltd." {{ old('type_organization') == 'Ltd.' ? 'selected' : '' }}>Ltd.</option>
                                            <option value="Cooperative" {{ old('type_organization') == 'Cooperative' ? 'selected' : '' }}>Cooperative</option>
                                            <option value="AOP" {{ old('type_organization') == 'AOP' ? 'selected' : '' }}>AOP</option>
                                            <option value="HUF" {{ old('type_organization') == 'HUF' ? 'selected' : '' }}>HUF</option>
                                            <option value="Trusts" {{ old('type_organization') == 'Trusts' ? 'selected' : '' }}>Trusts</option>
                                            <option value="NGO" {{ old('type_organization') == 'NGO' ? 'selected' : '' }}>NGO</option>
                                        </select>

                                    </div>
                                   
                                    <div class="col-lg-6">
                                       
                                            <select id="msme_type" name="msme_type">
                                                <option value="" {{ old('msme_type') == '' ? 'selected' : '' }}>Select MSME Type</option>
                                                <option value="Startup Business" {{ old('msme_type') == 'Startup Business' ? 'selected' : '' }}>Start ups with less than 3 years of existence.</option>
                                                <option value="Micro Business" {{ old('msme_type') == 'Micro Business' ? 'selected' : '' }}>Micro: Investment less than Rs.1 Cr. and Turnover less than Rs. 5 crore</option>
                                                <option value="Small Business" {{ old('msme_type') == 'Small Business' ? 'selected' : '' }}>Small: Investment less than Rs.10 Cr. and Turnover less than Rs. 50 crore</option>
                                                <option value="Medium Business" {{ old('msme_type') == 'Medium Business' ? 'selected' : '' }}>Medium: Investment less than Rs.50 Cr. and Turnover less than Rs. 250 crore</option>
                                                <option value="Beyond MSME Business" {{ old('msme_type') == 'Beyond MSME Business' ? 'selected' : '' }}>Beyond SME: Investment less than Rs.250 Cr. and Turnover less than Rs. 500 crore</option>
                                            </select>

                                
                                    </div>
        
                                    <div class="col-lg-6">
                                        
                                        <select id="business_category" name="business_category">
    <option value="">Select Business Category</option>
   
    @foreach($businessCategory as $businessCat)
        <option data-type="{{ $businessCat->type }}" value="{{ $businessCat->id }}" 
            {{ old('business_category') == $businessCat->id ? 'selected' : '' }}>
            {{ $businessCat->name }}
            
        </option>
    @endforeach
</select>

                                    
                                    
                                </div>
             <div class="col-lg-6 col-md-6">
                <input type="text" placeholder="Others" id="others" name="others" value="{{ old('others') }}">
            </div>
    @if(old('business_type') == '1' || old('business_type') == '')
        @php
            $class = 'display:none';
        @endphp
    @else
        @php
            $class = '';
        @endphp
    
    @endif
                                <div class="col-lg-6 buz_sub_cat">
                                    <!--only for goods  -->
                                    
                                       <select id="business_sub_category" name="business_sub_category"  style="{{$class}}">
                                            <option value="">Select Business Sub-Category</option>
                                            @foreach($subbusinessCategory as $subbusinessCat)
                                                <option value="{{ $subbusinessCat->id }}" 
                                                    {{ old('business_sub_category') == $subbusinessCat->id ? 'selected' : '' }}>
                                                    {{ $subbusinessCat->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    
                                    
                                </div>
                               
                                    <div class="col-lg-12">
                                        
                                           <textarea id="specialization" name="specialization" placeholder="Enter Any 2 specialization (Example: For Equity Financing - SME Listing) Maximum 40 characters allowed" rows="1" cols="40" maxlength="40">{{ old('specialization', isset($businessData) ? substr($businessData->details ?? '', 0, 40) : '') }}</textarea>

                                   
                                    </div>
                                    
                                </div>
                            </div>
                           <div class="form-box">
    <div class="row ">
        <div class="col-12">
            <h6>Contact Details</h6>
        </div>
        <div class="col-lg-4 col-md-6">
            <input type="number" placeholder="Contact Number" id="contact_no" name="contact_no" value="{{ old('contact_no', isset($businessData) ? preg_replace('/[^0-9]/', '', $businessData->mobile_no ?: $businessData->office_no) : '') }}">
        </div>
        <div class="col-lg-4 col-md-6">
            <input type="email" placeholder="Email Address" id="email" name="email" value="{{ old('email', $businessData->email ?? '') }}">
        </div>
        <div class="col-lg-4 col-md-6">
            <input type="text" placeholder="Website" id="website" name="website" value="{{ old('website', isset($businessData->website) ? $businessData->website : '') }}">
        </div>

        <div class="col-lg-12">
            <textarea id="address" name="address" placeholder="Enter Address" rows="2">{{ old('address', $businessData->add ?? '') }}</textarea>
        </div>

        <div class="col-lg-4 col-md-4">
            <input type="number" placeholder="Pincode" id="pincode" name="pincode" value="{{ old('pincode', isset($businessData) ? substr(preg_replace('/[^0-9]/', '', $businessData->pincode), 0, 6) : '') }}">
        </div>
        <div class="col-lg-4 col-md-4">
            <input type="text" placeholder="City" id="city" name="city" value="{{ old('city', $businessData->city ?? '') }}" >
        </div>
        <div class="col-lg-4 col-md-4">
            <input type="text" placeholder="State" id="state" name="state" value="{{ old('state', $businessData->state ?? '') }}"  >
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
                    <input type="checkbox" id="chk-startup" name="business_size[]" value="startup" {{ in_array('startup', old('business_size', [])) ? 'checked' : '' }}>
                    <label for="chk-startup" class="check-label">Startups</label>
                </div>
                <div class="cus-check-contain">
                    <input type="checkbox" id="chk-micro" name="business_size[]" value="micro" {{ in_array('micro', old('business_size', [])) ? 'checked' : '' }}>
                    <label for="chk-micro" class="check-label">Micro</label>
                </div>
                <div class="cus-check-contain">
                    <input type="checkbox" id="chk-small" name="business_size[]" value="small" {{ in_array('small', old('business_size', [])) ? 'checked' : '' }}>
                    <label for="chk-small" class="check-label">Small</label>
                </div>
                <div class="cus-check-contain">
                    <input type="checkbox" id="chk-medium" name="business_size[]" value="medium" {{ in_array('medium', old('business_size', [])) ? 'checked' : '' }}>
                    <label for="chk-medium" class="check-label">Medium</label>
                </div>
                <div class="cus-check-contain">
                    <input type="checkbox" id="chk-sme" name="business_size[]" value="beyond-sme" {{ in_array('beyond-sme', old('business_size', [])) ? 'checked' : '' }}>
                    <label for="chk-sme" class="check-label">Beyond SME</label>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-md-6">
            <input type="text" placeholder="Enter Years of experience" id="experience" name='experience' value="{{ old('experience') }}">
        </div>
        <div class="col-lg-6 col-md-6">
            <input type="text" placeholder="Enter GST number, PAN number or Udyam Aadhar number" name="document_no" id="document_no" value="{{ old('document_no') }}">
        </div>
        <div class="col-lg-6 col-md-6">
            <label for="" class="m-0">Upload Document of entered ID.</label>
            <input type="file" name="document_file[]" id="document_file" multiple> 
        </div>
        <div class="col-lg-6 col-md-6">
            <input type="hidden" id="business_description" name="business_description">
            <input type="text" placeholder="Enter states/cities you can provide service to" name="state_city" id="state_city" value="{{ old('state_city') }}">
        </div>
        <div class="col-lg-6 col-md-6">
            <label for="" class="m-0">I want to connect to ................. (Maximum 25 letters allowed)</label>
            <input type="text" placeholder="Enter Taxation, Tshirt Manufacturers, etc" name="want_to" id="want_to" value="" maxlength="25">
        </div>
    </div>
</div>

                            <div class="form-box">
                                <div class="row align-items-end">
                                    <div class="col-12">
                                        <h6>Description</h6>
                                        @if(old('business_description') == '')
                                        <p id="description">We are a (type of organization) and a (MSME Type) MSME firm in (Business Category) with a __ years of experience and specialization in (Specialization) operating from (city),(state) and we also provide service to (Enter states/cities you can cater to). I want to connect to</p>
                                        @else
                                        <p id="description">{{old('business_description')}}</p>
                                        @endif
                                    </div>
                                    
                                </div>
                            </div>
                            
                            <div class="col-12">
                                
                               <p>
                                   
                                   By clicking Register Now, you acknowledge that you have read and agree to our <a href="/term-condition" style="font-size: 16px; color:#74c8a2;">Terms and Conditions</a> and <a href="/privacy-policy" style="font-size: 16px;color:#74c8a2;">Privacy policy</a>.
                                   
                               </p> 
                                
                            </div>
                            
                            <div class="row justify-content-center">
                                <div class="col-4">
                                    <button class="main-btn2 w-100" type="submit" onclick="validateForm()"> REGISTER NOW</button>
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

 function validateForm() {
        /*
        // Get the selected MSME type
        var msmeType = document.getElementById('msme_type').value;
        
        // Get the years of experience
        var experience = parseInt(document.getElementById('experience').value);

        // Check if the MSME type is "Startups" and experience is above 3 years
        if (msmeType === 'Startup Business' && experience > 3) {
            // Show alert
            alert("Startups with more than 3 years of experience are not eligible for registration.");
        } else {
            // If validation passes, submit the form
            document.getElementById('registrationForm').submit();
        }
        */
    }


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
        
        
        $(document).on('keyup', '#specialization,#state_city,#organization_name,#want_to', function(){
            descriptionText();
        
        });
        
        $(document).on('change', '#type_organization,#msme_type,#business_category,#business_sub_category', function(){
            descriptionText();
        
        });
        
        
        $(document).on('change', '#business_category',function(){
            if($(this).val() == '1'){
                $('#others').show();
            }else{
                $('#others').hide();
            }
        
        });
        
        function descriptionText(){
            if($('#business_category').val() == '1'){
                if($('#business_type').val() == 0){
                    var txt = 'We are a '+$('#type_organization').val().bold()+' and a '+$('#msme_type option:selected').val().bold()+' MSME firm in '+$('#business_sub_category option:selected').text().bold()+' '+$('#others').val().bold()+' with a '+$('#experience').val().bold()+' years of experience and specialization in '+$('#specialization').val().bold()+' operating from '+$('#city').val().bold()+', '+$('#state').val().bold()+' and we provide service to '+$('#state_city').val().bold()+'. I want to connect to '+$('#want_to').val().bold()+'.';
                }else{
                    var txt = 'We are a '+$('#type_organization').val().bold()+' and a '+$('#msme_type option:selected').val().bold()+' MSME firm in '+$('#others').val().bold()+' with a '+$('#experience').val().bold()+' years of experience and specialization in '+$('#specialization').val().bold()+' operating from '+$('#city').val().bold()+', '+$('#state').val().bold()+' and we provide service to '+$('#state_city').val().bold()+'. I want to connect to '+$('#want_to').val().bold()+'.';
                }
            }else{
                if($('#business_type').val() == 0){
                    var txt = 'We are a '+$('#type_organization').val().bold()+' and a '+$('#msme_type option:selected').val().bold()+' MSME firm in '+$('#business_sub_category option:selected').text().bold()+' '+$('#business_category option:selected').text().bold()+' with a '+$('#experience').val().bold()+' years of experience and specialization in '+$('#specialization').val().bold()+' operating from '+$('#city').val().bold()+', '+$('#state').val().bold()+' and we provide service to '+$('#state_city').val().bold()+'. I want to connect to '+$('#want_to').val().bold()+'.';
                }else{
                    var txt = 'We are a '+$('#type_organization').val().bold()+' and a '+$('#msme_type option:selected').val().bold()+' MSME firm in '+$('#business_category option:selected').text().bold()+' with a '+$('#experience').val().bold()+' years of experience and specialization in '+$('#specialization').val().bold()+' operating from '+$('#city').val().bold()+', '+$('#state').val().bold()+' and we provide service to '+$('#state_city').val().bold()+'. I want to connect to '+$('#want_to').val().bold()+'.';
                }
            }        
            $('#description').html(txt);
            $('#business_description').html(txt);
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