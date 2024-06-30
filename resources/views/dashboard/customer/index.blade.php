@extends('layouts.app')

@section('title')
    Customer Info
@endsection

@section('title-header')
    <div class='mb-1'>
        Customer Info
    </div>
@endsection

@section('content')
    {{-- Alert Success --}}
    @if (session('success'))
        <div class="alert alert-success mb-4" role="alert">
            {{ session('success') }}
        </div>
    @endif
    {{-- End Of Alert Success --}}

    <div class="container">
        <h1>Customers List</h1>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone Number</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($customers as $customer)
                    <tr>
                        <td>{{ $customer->id }}</td>
                        <td>{{ $customer->customer_name }}</td>
                        <td>{{ $customer->customer_email }}</td>
                        <td>{{ $customer->customer_phone_number }}</td>
                        <td>
                            <a href="{{ route('dashboard.customers.edit', $customer->id) }}" class="btn btn-primary">Edit</a>
                            <form action="{{ route('dashboard.customers.destroy', $customer->id) }}" method="POST"
                                style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{-- <a href="{{ route('dashboard.customer.create') }}" class="btn btn-success">Add New Customer</a> --}}
    </div>
@endsection
