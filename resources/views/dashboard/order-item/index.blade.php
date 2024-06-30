@extends('layouts.app')

@section('title')
    Order Items
@endsection

@section('title-header')
    <div class='mb-1'>
        Order Items
    </div>
@endsection

@section('content')
    {{-- Alert Success --}}
    @if (session('success'))
        <div class="alert alert-success mb-4 d-flex" role="alert">
            {{ session('success') }}
        </div>
    @endif
    {{-- End Of Alert Success --}}

    {{-- Alert Danger --}}
    @if (session('error'))
        <div class="alert alert-danger mb-4" role="alert">
            {{ session('error') }}
        </div>
    @endif
    {{-- End Of Alert Danger --}}

    {{-- Table --}}
    <div class="table-responsive mt-4">
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">
                        <a style="text-decoration: none;"
                            href="{{ route('dashboard.order-items.index', ['sort' => 'id', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">#</a>
                    </th>
                    <th scope="col">
                        <a style="text-decoration: none;"
                            href="{{ route('dashboard.order-items.index', ['sort' => 'customer_name', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">
                            Customer Name
                        </a>
                    </th>
                    <th scope="col">
                        <a style="text-decoration: none;"
                            href="{{ route('dashboard.order-items.index', ['sort' => 'product_name', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">
                            Product Name
                        </a>
                    </th>
                    <th scope="col">
                        <a style="text-decoration: none;"
                            href="{{ route('dashboard.order-items.index', ['sort' => 'quantity', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">
                            Quantity
                        </a>
                    </th>
                    <th scope="col">
                        <a style="text-decoration: none;"
                            href="{{ route('dashboard.order-items.index', ['sort' => 'unit_price', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">
                            Unit Price
                        </a>
                    </th>
                    <th scope="col">
                        <a style="text-decoration: none;"
                            href="{{ route('dashboard.order-items.index', ['sort' => 'payment_method', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">
                            Payment Method
                        </a>
                    </th>
                    <th scope="col">
                        <a style="text-decoration: none;"
                            href="{{ route('dashboard.order-items.index', ['sort' => 'status', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">
                            Status
                        </a>
                    </th>
                    {{-- @if (auth()->user()->role_id === \App\Models\Role::ADMIN) --}}
                    <th scope="col">
                        <a style="text-decoration: none;"
                            href="{{ route('dashboard.order-items.index', ['sort' => 'status', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">
                            Action
                        </a>
                    </th>
                    {{-- @endif --}}
                </tr>
            </thead>
            <tbody>
                @foreach ($orderItems as $item)
                    <tr>
                        <th scope="row">{{ $loop->iteration }}</th>
                        <td>{{ $item->customer->customer_name }}</td>
                        <td>{{ $item->product->name }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>₱ {{ number_format($item->unit_price) }}</td>
                        <td>
                            @if ($item->status === 'paid' || $item->status === 'approved')
                                Paid via {{ strtoupper($item->payment_method) }}
                            @elseif ($item->status === 'cod')
                                Cash on Delivery
                            @elseif ($item->status === 'pending')
                                Pending Payment
                            @endif
                        </td>

                        <td>
                            @if ($item->status === 'pending')
                                <span class="badge bg-warning">Pending</span>
                            @elseif ($item->status === 'paid' || $item->status === 'approved')
                                <span class="badge bg-primary">approved</span>
                            @elseif ($item->status === 'rejected')
                                <span class="badge bg-danger">Rejected</span>
                            @endif
                        </td>
                        <td>
                            @if (auth()->user()->role_id === \App\Models\Role::ADMIN)
                                <a href="{{ route('dashboard.order-items.show', $item->id) }}"
                                    class="btn btn-secondary">View</a>
                                @if ($item->status === 'pending')
                                    <a href="{{ route('dashboard.order-items.payment', $item->id) }}"
                                        class="btn btn-primary">Pay Order</a>
                                    <form action="{{ route('dashboard.order-items.reject', $item->id) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-danger">Reject Order</button>
                                    </form>
                                @endif
                            @endif
                            @if (auth()->user()->role_id === \App\Models\Role::HUB)
                                @if ($item->status === 'pending')
                                    <a href="{{ route('dashboard.order-items.payment', $item->id) }}"
                                        class="btn btn-primary">Pay Order</a>
                                @endif
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {{-- End Of Table --}}

    {{-- Pagination --}}
    <div class="justify-content-right mt-3">
        {{ $orderItems->appends(['sort' => $sortField, 'order' => $sortOrder])->links() }}
    </div>
    {{-- End Of Pagination --}}
@endsection
