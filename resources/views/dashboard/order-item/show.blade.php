@extends('layouts.app')

@section('title')
    Customer Orders
@endsection

@section('title-header')
    <div class='mb-1'>
        Purchased items of {{ $customer->customer_name }}
    </div>
@endsection

@section('content')
    {{-- Back Button --}}
    <div class="mb-3">
        <a href="{{ url()->previous() }}" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    {{-- Customer Information --}}
    <div class="card shadow-lg mb-4">
        <div class="card-body">
            <h5 class="card-title text-center mb-3">Customer Details</h5>
            <div class="table-responsive">
                <table class="table text-center">
                    <tbody>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Address</th>
                        </tr>
                        <tr>
                            <td>{{ $customer->customer_name }}</td>
                            <td>{{ $customer->customer_email }}</td>
                            <td>{{ $customer->customer_phone_number }}</td>
                            <td>{{ $customer->address }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Orders Table --}}
    <div class="card shadow-lg">
        <div class="card-body">
            <h5 class="card-title text-center mb-3">Purchase History</h5>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-primary">
                        <tr>
                            <th>#</th>
                            <th>Product Name</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orderItems as $order)
                            <tr>
                                <td>{{ ($orderItems->currentPage() - 1) * $orderItems->perPage() + $loop->iteration }}</td>
                                <td>{{ $order->product->name }}</td>
                                <td>{{ $order->quantity }}</td>
                                <td>₱ {{ number_format($order->unit_price, 2) }}</td>
                                <td>
                                    @if ($order->status !== 'cancel')
                                        ₱ {{ number_format($order->quantity * $order->unit_price, 2) }}
                                    @else
                                        ₱ 0.00
                                    @endif
                                </td>
                                <td>
                                    @if ($order->status === 'paid' || $order->status === 'cod')
                                        <span class="badge bg-success">Approved</span>
                                    @elseif ($order->status === 'cancel')
                                        <span class="badge bg-danger">Canceled</span>
                                    @endif
                                </td>
                                <td>{{ $order->created_at->format('Y-m-d') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No orders found for this customer.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if ($orderItems->count())
                        <tfoot>
                            <tr class="table-primary">
                                <th colspan="4" class="text-end">Total:</th>
                                <th>₱ {{ number_format($totalSpent, 2) }}</th>
                                <th colspan="2"></th>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>

    {{-- Pagination --}}
    <div class="d-flex justify-content-end mt-3">
        {{ $orderItems->links() }}
    </div>
@endsection
