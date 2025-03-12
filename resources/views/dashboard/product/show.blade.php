@extends('layouts.app')

@section('title')
    Product Queue
@endsection

@section('title-header')
    <div class='mb-1'>
        Product Queue
    </div>
@endsection

@section('content')
    {{-- Alert Success --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Product Queue Table --}}
    <div class="card shadow-lg">
        <div class="card-body">
            <h5 class="card-title text-center mb-4">Product History</h5>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-primary">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Name</th>
                            <th scope="col">Description</th>
                            <th scope="col">Price</th>
                            <th scope="col">Quantity In Stock</th>
                            <th scope="col">Original Stocks</th>
                            <th scope="col">Category</th>
                            <th scope="col">Added By</th>
                            <th scope="col">Created At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($products as $product)
                            <tr>
                                <th scope="row">
                                    {{ $loop->iteration + ($products->currentPage() - 1) * $products->perPage() }}</th>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->description }}</td>
                                <td>₱ {{ number_format($product->price, 2) }}</td>
                                <td>{{ $product->quantity_in_stock }}</td>
                                <td>{{ $product->original_stocks }}</td>
                                <td>{{ $product->category->name }}</td>
                                <td>{{ $product->user->name }}</td>
                                <td>{{ $product->created_at->format('F j, Y, g:i a') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">No products added yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Pagination Links --}}
    <div class="d-flex justify-content-end mt-3">
        {{ $products->links() }} {{-- Add pagination links here --}}
    </div>

    <div class="mt-4">
        <a href="{{ route('dashboard.products.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to Products
        </a>
    </div>
@endsection
