@extends('layouts.app')
@section('body_section')
@include('layouts.header')

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white text-center py-4 border-bottom-0">
                    @if($success)
                        <h2 class="text-success"><i class="fa fa-check-circle"></i> Payment Successful</h2>
                    @else
                        <h2 class="text-danger"><i class="fa fa-times-circle"></i> Payment Failed</h2>
                    @endif
                </div>
                <div class="card-body px-5 pb-5">
                    <p class="text-center text-muted mb-4">{{ $message }}</p>
                    
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th scope="row" class="w-50">Order Number</th>
                                <td>{{ $order_id }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Amount</th>
                                <td>₹ {{ number_format($amount, 2) }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Status</th>
                                <td>
                                    @if($success)
                                        <span class="badge bg-success">Success</span>
                                    @else
                                        <span class="badge bg-danger">Failed / Pending</span>
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="text-center mt-4">
                        <a href="{{ url('/user-connections') }}" class="btn btn-primary px-4 py-2">Go to Connections</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
