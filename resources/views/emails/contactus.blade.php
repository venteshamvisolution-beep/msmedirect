<x-mail::message>

@foreach($maildata as $key =>$mail)
	<br> {{ucwords(str_replace('_', ' ', $key))}} : {{$mail}} 
@endforeach

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>