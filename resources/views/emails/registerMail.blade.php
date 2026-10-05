<x-mail::message>
Dear {{$maildata['name']}},

Welcome to MSME Direct! We are thrilled to have you onboard, and we appreciate you sharing your details with us.

Our team is dedicated to ensuring a seamless experience for all our users, and we are committed to reviewing your information promptly. You can expect to receive a response from us within the next 24-48 hours.

In the meantime, feel free to explore our platform and familiarize yourself with the opportunities and features MSME Direct has to offer. If you have any immediate questions or concerns, don't hesitate to reach out to our support team at {{route('contactUs')}}.

Thank you once again for choosing MSME Direct. We look forward to assisting you on your journey to connect and collaborate with fellow MSMEs.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
