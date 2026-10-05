@extends('layouts.app')
@section('body_section')
@include('layouts.header')

<button id="rzp-button1" style="display:none;">Pay</button>

@endsection
@section('page_js')
<script type="text/javascript">
    var paymentLink = '<?php echo isset($payment_link) ? $payment_link : ""; ?>';
    if(paymentLink !== '') {
        // Redirect to HDFC SmartGateway Hypercheckout page
        window.location.href = paymentLink;
    } else {
        alert("Payment link could not be generated. Please try again later.");
        window.location.href = '/user-connections';
    }
</script>
@endsection