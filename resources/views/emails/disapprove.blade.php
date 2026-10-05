<x-mail::message>
We regret to inform you that your registration request on {{ config('app.name') }} has been rejected by our backend team. Below is the remark for rejection:

@foreach($maildata as $key =>$mail)
	{{ucwords(str_replace('_', ' ', $key))}} : {{$mail}} 
@endforeach

We appreciate your interest in {{ config('app.name') }}. If you have any further questions or concerns, please feel free to contact us.
<br>Thanks,
{{ config('app.name') }}
</x-mail::message>