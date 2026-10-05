<x-mail::message>
# Introduction

Dear {{$maildata['name']}},

{{$maildata['desc']}}



Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
