<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 14px;
            line-height: 1.6;
            color: #333333;
            background-color: #ffffff;
            margin: 0;
            padding: 20px;
        }
        h3 {
            font-size: 14px;
            font-weight: bold;
            color: #111111;
            margin-top: 18px;
            margin-bottom: 8px;
            text-transform: uppercase;
        }
        p {
            margin-top: 0;
            margin-bottom: 12px;
        }
        ul {
            margin-top: 0;
            margin-bottom: 12px;
            padding-left: 20px;
        }
        li {
            margin-bottom: 6px;
        }
        a {
            color: #0056b3;
            text-decoration: underline;
        }
        .btn-register {
            display: inline-block;
            background-color: #0056b3;
            color: #ffffff !important;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
            margin: 10px 0;
        }
    </style>
</head>
<body>

<p><strong>Dear {{ $businessData->company ?: $businessData->person_name ?: 'MSME Member' }},</strong></p>

<p>We came across your company's online business profile and would like to invite you to register with MSME Direct.</p>

<h3>WHAT IS MSME DIRECT?</h3>
<p>MSME Direct is a business platform designed to help MSMEs <strong>gain visibility, discover business opportunities, and connect with relevant businesses, service providers, manufacturers, and traders.</strong></p>

<h3>WHY SHOULD YOUR BUSINESS JOIN MSME DIRECT?</h3>
<ul>
    <li><strong>Get Visibility</strong> – Showcase your business to relevant businesses and potential opportunities.</li>
    <li><strong>Build Connections</strong> – Discover potential customers, suppliers and business partners.</li>
    <li><strong>Explore Wider Markets</strong> – Reach opportunities beyond your existing network and geography.</li>
    <li><strong>Grow Your Presence</strong> – Be part of a growing MSME business network.</li>
    <li><strong>FREE REGISTRATION</strong> – No registration fee or annual subscription for your basic listing.</li>
</ul>

<p>A nominal fee will be charged only when you choose to connect with a relevant business opportunity.</p>

<h3>HOW TO PROCEED</h3>
<p>We are pleased to register your esteemed organization based on the business information available on your website and in the public domain, enabling you to benefit from the advantages outlined above. The registration confirmation will be shared via email in due course.</p>

<p>However, if you <strong>do not wish to be listed or have any objection</strong>, write to us <strong>within 7 days of receiving this email</strong>, and we will remove your registration/listing with immediate effect.</p>

@php
    $encodedId = base64_encode((string) $businessData->id);
    $registerUrl = url('/register?id=' . $encodedId);
@endphp

<p>
    <a href="{{ $registerUrl }}" class="btn-register" target="_blank">Register Now</a>
</p>

<p>Or copy and paste this link into your browser:<br>
<a href="{{ $registerUrl }}" target="_blank">{{ $registerUrl }}</a></p>

<h3>SEE HOW MSME DIRECT WORKS</h3>
<p>Watch our short introductory video:</p>
<p>
    <a href="https://drive.google.com/file/d/1BaFS85TRQD2HS2SLZ68Buw3cJXzmTdwZ/view?usp=sharing" class="btn-register" target="_blank">Watch Now</a>
</p>

<hr style="border: none; border-top: 1px solid #e0e0e0; margin: 20px 0;">

<p><strong>For any questions or further information, please contact:</strong><br>
Phone: 9152036911<br>
Email: <a href="mailto:msmedirectsales@gmail.com">msmedirectsales@gmail.com</a><br>
Website: <a href="https://msmedirect.com/" target="_blank">https://msmedirect.com/</a></p>

<p><strong>Give your business the opportunity to spread its wings.</strong><br>
<strong>Register • Connect • Grow • Expand</strong></p>

</body>
</html>
