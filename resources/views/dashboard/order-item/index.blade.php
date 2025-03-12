@extends('layouts.app')

@section('title')
    Order Items
@endsection

@section('title-header')
    <div class='mb-1'>
        Purchase History
    </div>
@endsection

@section('content')
    {{-- Alerts --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
            <i class="bi bi-exclamation-circle-fill me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Order Items Table --}}
    <div class="card shadow-lg">
        <div class="card-body">
            <h5 class="card-title text-center mb-4">Order Items</h5>

            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-primary">
                        <tr>
                            <th scope="col">
                                <a
                                    href="{{ route('dashboard.order-items.index', ['sort' => 'id', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">
                                    # </a>
                            </th>
                            <th scope="col">
                                <a
                                    href="{{ route('dashboard.order-items.index', ['sort' => 'customer_name', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">
                                    <i class="bi bi-person-fill"></i> Customer Name
                                </a>
                            </th>
                            <th scope="col">
                                <a
                                    href="{{ route('dashboard.order-items.index', ['sort' => 'product_name', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">
                                    <i class="bi bi-box"></i> Product Name
                                </a>
                            </th>
                            <th scope="col">
                                <a
                                    href="{{ route('dashboard.order-items.index', ['sort' => 'status', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">
                                    <i class="bi bi-info-circle"></i> Customer Type
                                </a>
                            </th>
                            <th scope="col">
                                <a
                                    href="{{ route('dashboard.order-items.index', ['sort' => 'quantity', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">
                                    <i class="bi bi-sort-numeric-up"></i> Quantity
                                </a>
                            </th>
                            <th scope="col">
                                <a
                                    href="{{ route('dashboard.order-items.index', ['sort' => 'unit_price', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">
                                    <i class="bi bi-currency-dollar"></i> Unit Price
                                </a>
                            </th>
                            <th scope="col">
                                <a
                                    href="{{ route('dashboard.order-items.index', ['sort' => 'reference_number', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">
                                    <i class="bi bi-file-earmark-text"></i> Reference Number
                                </a>
                            </th>
                            <th scope="col">
                                <a
                                    href="{{ route('dashboard.order-items.index', ['sort' => 'payment_method', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">
                                    <i class="bi bi-wallet2"></i> Payment Method
                                </a>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orderItems as $item)
                            <tr>
                                <th scope="row">
                                    {{ ($orderItems->currentPage() - 1) * $orderItems->perPage() + $loop->iteration }}</th>
                                <td>{{ $item->customer->customer_name }}</td>
                                <td>{{ $item->product->name }}</td>
                                <td>{{ $item->customer->customer_type }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>₱ {{ number_format($item->unit_price, 2) }}</td>
                                <td>{{ $item->reference_number ?? 'N/A' }}</td>
                                <td>
                                    @if ($item->status === 'paid' || $item->status === 'approved')
                                        Paid via {{ strtoupper($item->payment_method) }}
                                    @elseif ($item->status === 'cod')
                                        Cash
                                    @elseif ($item->status === 'cancel')
                                        Rejected Item
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>
        </div>
    </div>

    {{-- Pagination --}}
    <div class="d-flex justify-content-end mt-3">
        {{ $orderItems->appends(['sort' => $sortField, 'order' => $sortOrder])->links() }}
    </div>
@endsection
