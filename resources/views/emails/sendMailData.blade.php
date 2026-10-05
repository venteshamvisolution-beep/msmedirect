<x-mail::message>
Dear {{$maildata['user_name']}}, <br>
We are delighted to inform you that a successful connection has been made on MSME Direct.<br> You have been matched with {{$maildata['msme_name']}} based on your shared interests and requirements.<br>
Here are the details of your newly connected partner:<br>
MSME Name: {{$maildata['msme_name']}}<br>
Contact Number: {{$maildata['contact_no']}}<br>
Email Address: {{$maildata['email']}}<br>
Pincode: {{$maildata['pincode']}}<br>
City: {{$maildata['city']}}<br>
State: {{$maildata['state']}}<br>
Description: {!!$maildata['business_description']!!}<br>
We believe this connection holds great potential for collaboration and mutual benefit.<br> We encourage you to reach out to your new connection to explore synergies and discuss business opportunities.<br>
Should you have any questions or encounter any issues, feel free to contact our support team at Contact URl.<br>
Thank you for being a part of MSME Direct, and we wish you a prosperous and successful collaboration!<br>




Thanks,<br>
{{ config('app.name') }}
</x-mail::message>