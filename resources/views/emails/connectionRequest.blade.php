<x-mail::message>

Someone has shown interest in your profile.Please Click below button to view.

<x-mail::button :url="$maildata['link']">
View Connection
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
