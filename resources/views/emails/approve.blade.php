<x-mail::message>


Dear {{$maildata['name']}},

I hope this email finds you well. We are excited to have you on board as a valued member of MSME Direct. To provide you with seamless access to your account, we are sharing your initial login credentials.

Your Account Information:

Username/Email: {{$maildata['email']}} </br>
Password: {{$maildata['password']}} 
Important Instructions:

Please log in using the provided credentials at {{route('login')}}.
If you encounter any issues or have questions, please contact our support team.
Note: This email is confidential, and the information provided should be treated with the utmost care. Do not share your login credentials with anyone.

Thank you for choosing MSME Direct. We look forward to serving you.






</x-mail::message>