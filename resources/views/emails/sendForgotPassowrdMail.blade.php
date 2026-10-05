<x-mail::message>
# Forgot Password

You can reset password from bellow link.

<x-mail::button :url="$maildata">
Forgot Password
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
