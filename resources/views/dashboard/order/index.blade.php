@extends('layouts.app')

@section('title')
    Customers
@endsection

@section('title-header')
    <div class="mb-3">
        <h1 class="h3 fw-bold">Customers</h1>
    </div>
@endsection

@section('content')
    <div class="d-flex justify-content-between mb-4">
        <div>
            <a href="{{ route('dashboard.customers.create') }}" class="btn btn-primary btn-lg rounded-pill">
                <i class="bi bi-person-plus-fill"></i> Create New Customer
            </a>
        </div>
    </div>

    {{-- Alert Success --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-4 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2 fs-4"></i>
            <span class="fs-5">{{ session('success') }}</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Table --}}
    <div class="card shadow-lg border-0">
        <div class="card-body">
            <div class="table-responsive mt-4">
                <table class="table table-striped table-hover align-middle border">
                    <thead class="table-primary text-center">
                        <tr>
                            <th>#</th>
                            <th>Customer Name</th>
                            <th>Customer Email</th>
                            <th>Phone Number</th>
                            <th>Address</th>
                            <th>Customer Type</th>
                            <th class="text-end">Total Products Ordered</th>
                            <th class="text-end">Total Purchased</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($customers as $customer)
                            <tr class="text-center">
                                <th scope="row">{{ $loop->iteration }}</th>
                                <td>{{ $customer['customer_name'] }}</td>
                                <td>{{ $customer['customer_email'] }}</td>
                                <td>{{ $customer['customer_phone_number'] }}</td>
                                <td>{{ $customer['address'] }}</td>
                                <td>{{ $customer['customer_type'] }}</td>
                                <td class="text-end">{{ $customer['total_quantity'] }}</td>
                                <td class="text-end">₱ {{ number_format($customer['total_price'], 2) }}</td>
                                <td class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('dashboard.customers.edit', $customer['id']) }}"
                                        class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form action="{{ route('dashboard.customers.destroy', $customer['id']) }}"
                                        method="POST" class="d-inline"
                                        onsubmit="return confirm('Are you sure you want to delete this customer?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Pagination --}}
    <div class="d-flex justify-content-center mt-4">
        {{ $customers->links() }}
    </div>
@endsection
