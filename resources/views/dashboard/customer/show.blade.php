@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Customer Details</div>

                    <div class="card-body">
                        <div class="form-group">
                            <label for="customer_name">Customer Name</label>
                            <p>{{ $customer->customer_name }}</p>
                        </div>
                        <div class="form-group">
                            <label for="customer_email">Email</label>
                            <p>{{ $customer->customer_email }}</p>
                        </div>
                        <div class="form-group">
                            <label for="customer_phone_number">Phone Number</label>
                            <p>{{ $customer->customer_phone_number }}</p>
                        </div>
                        <div class="form-group">
                            <label for="hub_name">Hub Name</label>
                            <p>{{ $customer->hub_name }}</p>
                        </div>
                        <div class="form-group">
                            <label for="address">Address</label>
                            <p>{{ $customer->address }}</p>
                        </div>
                        <div class="form-group">
                            <label for="date">Date</label>
                            <p>{{ $customer->date ? $customer->date->format('Y-m-d') : '' }}</p>
                        </div>
                        <div class="form-group">
                            <label for="created_at">Created At</label>
                            <p>{{ $customer->created_at }}</p>
                        </div>
                        <div class="form-group">
                            <label for="updated_at">Updated At</label>
                            <p>{{ $customer->updated_at }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
