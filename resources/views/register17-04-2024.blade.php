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
                        <form action="">
                            <div class="form-box">
                                <div class="row ">
                                    <div class="col-12">
                                        <h6>Organization Details</h6>
                                    </div>
                                    <div class="col-lg-6 col-md-6">
                                        <input type="text" placeholder="Organization Name">
                                    </div>
                                    <div class="col-lg-6 col-md-6">
                                        <select name="" id="">
                                            <option value="">Select Type of Business</option>
                                            <option value="">Goods</option>
                                            <option value="">Services</option>
                                        </select>
                                    </div>
                                    <div class="col-lg-6 col-md-6">
                                        <select name="" id="">
                                            <option value="">Select Type of Organization</option>
                                            <option value="">Sole Proprietorship</option>
                                            <option value="">Start Up</option>
                                            <option value="">Partnership </option>
                                            <option value="">LLP</option>
                                            <option value="">Pvt. Ltd.</option>
                                            <option value="">Ltd.</option>
                                            <option value="">AOP</option>
                                            <option value="">HUF</option>
                                            <option value="">Trusts</option>
                                            <option value="">NGO</option>
                                        </select>
                                    </div>
                                   
                                    <div class="col-lg-6">
                                       
                                            <select  id="msme_type" name="msme_type">
                                                <option value="">Select MSME Type</option>
                                                <option value="1">Start ups</option>
                                                <option value="2">Micro: Investment less than Rs.1 Cr. and Turnover less than Rs. 5 crore</option>
                                                <option value="3">Small: Investment less than Rs.10 Cr. and Turnover less than Rs. 50 crore</option>
                                                <option value="4">Medium: Investment less than Rs.50 Cr. and Turnover less than Rs. 250 crore</option>
                                                <option value="5">Beyond SME: Investment less than Rs.250 Cr. and Turnover less than Rs. 500 crore </option>
                                            </select>
                                
                                    </div>
        
                                    <div class="col-lg-6">
                                        
                                        <select  id="buz_cat" name="business_category">
                                            <option value="">Select Business Category</option>
                                            <option value="Electrical_Goods">Electrical Goods</option>
                                            <option value="Equity_Financing">Equity Financing</option>
                                            <option value="Marketing">Marketing</option>
                                            <option value="HR_Services">HR Services</option>
                                            <option value="Financial_Services">Financial Services</option>
                                            <option value="Wealth_Management">Wealth Management</option>
                                        </select>
                                    
                                </div>
    
                                <div class="col-lg-6 buz_sub_cat">
                                    <!--only for goods  -->
                                    
                                        <select  id="buz_sub_cat" name="business_subcategory">
                                            <option value="">Select Business Sub-Category</option>
                                            <option value="Manufacturing_Trading">Manufacturing & Trading</option>
                                            <option value="Manufacturing">Manufacturing</option>
                                            <option value="Trading">Trading</option>
                                        </select>
                                    
                                </div>
                                    <div class="col-lg-12">
                                        
                                            <textarea  id="specialization" name="specialization" placeholder="Enter Any 2 specialization (Example: For Equity Financing - SME Listing) Maximum 40 characters allowed" value="" rows="1" cols="60" maxlength="40"></textarea>
                                   
                                    </div>
                                    
                                </div>
                            </div>
                            <div class="form-box">
                                <div class="row ">
                                    <div class="col-12">
                                        <h6>Contact Details</h6>
                                    </div>
                                    <div class="col-lg-6 col-md-6">
                                        <input type="number" placeholder="Contact Number">
                                    </div>
                                    <div class="col-lg-6 col-md-6">
                                        <input type="email" placeholder="Email Address">
                                    </div>
        
                                    <div class="col-lg-12">
                                        
                                            <textarea  id="address" name="address" placeholder="Enter Address" value="" rows="2"></textarea>
                                   
                                    </div>

                                    <div class="col-lg-4 col-md-4">
                                        <input type="number" placeholder="Pincode" id="pincode" name="pincode">
                                    </div>
                                    <div class="col-lg-4 col-md-4">
                                        <input type="text" placeholder="City" id="city" name="city">
                                    </div>
                                    <div class="col-lg-4 col-md-4">
                                        <input type="text" placeholder="State"  id="state" name="state">
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
                                                <input type="checkbox" name="" id="startup" checked>
                                                <label for="startup" class="check-label">Start ups</label>
                                            </div>
                                            <div class="cus-check-contain">
                                                <input type="checkbox" name="" id="micro">
                                                <label for="micro" class="check-label">Micro</label>
                                            </div>
                                            <div class="cus-check-contain">
                                                <input type="checkbox" name="" id="small">
                                                <label for="small" class="check-label">Small</label>
                                            </div>
                                            <div class="cus-check-contain">
                                                <input type="checkbox" name="" id="medium">
                                                <label for="medium" class="check-label">Medium</label>
                                            </div>
                                            <div class="cus-check-contain">
                                                <input type="checkbox" name="" id="sme">
                                                <label for="sme" class="check-label">Beyond SME</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6">
                                        <input type="text" placeholder="Enter Years of experience">
                                    </div>
                                    <div class="col-lg-6 col-md-6">
                                        <input type="text" placeholder="Enter GST number, PAN number or Udyam Aadhar number">
                                    </div>
                                    <div class="col-lg-6 col-md-6">
                                        <label for="" class="m-0">Upload Document of entered ID.</label>
                                        <input type="file" name="" id="">
                                    </div>
                                    <div class="col-lg-6 col-md-6">
                                        <input type="text" placeholder="Enter states/cities you can provide service to">
                                    </div>
                                    
                                </div>
                            </div>
                            <div class="form-box">
                                <div class="row align-items-end">
                                    <div class="col-12">
                                        <h6>Description</h6>
                                        <p>We are a (type of organization) and a (MSME Type) MSME firm in (Business Category) with a __ years of experience and specialization in (Specialization) operating from (city),(state) and we also provide service to (Enter states/cities you can cater to).</p>
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
        $('#pincodeInput').on('input', function () {
            var pincode = $(this).val();

            // Ensure pincode does not exceed 6 digits
            if (pincode.length > 6) {
                $(this).val(pincode.slice(0, 6));
            }

            // Make an AJAX request to your server-side script (e.g., pincode_lookup.php)
            $.ajax({
                type: 'POST',
                url: '{{ route("getPincodeData") }}', // Replace with your server-side script URL
                data: {pincode: pincode},
                success: function (response) {
                    // Parse the JSON response
                    var data = JSON.parse(response);

                    // Update the City and State input fields
                    $('#city').val(data.city);
                    $('#state').val(data.state);
                },
                error: function () {
                    $('#city').val('');
                    $('#state').val('');
                }
            });
        });
    });  
</script>
@endsection