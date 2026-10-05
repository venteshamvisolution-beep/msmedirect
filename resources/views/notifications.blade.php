@extends('layouts.app')
@section('body_section')
@include('layouts.header')
@if(session('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif
<section>
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="main-title">
                        <h4>Notification</h4>
                    </div>
                </div>
                <div class="col-12">
                    <div class="table-responsive mt-4">
                        <table class="table ">
                            <tbody>
                            @foreach($notifications as $notification)
                                <tr class="unres-not">
                                    @if($notification->link != '')
                                    <td><a href="{{$notification->link}}">{{$notification->description}}</a></td>
                                    @else
                                    <td>{{$notification->description}}</td>
                                    @endif
                                    <td><em>{{Carbon\Carbon::parse($notification->created_at)->diffForHumans()}}</em></td>
                                </tr>
                            @endforeach
                               

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection