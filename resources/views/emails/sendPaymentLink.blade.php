<x-mail::message>
Dear {{$maildata['name']}},


<a href="{{$maildata['link']}}">Please click on this payment link button to make the payment.</a>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
