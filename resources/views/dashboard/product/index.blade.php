@extends('layouts.app')

@section('title')
    Products
@endsection

@section('title-header')
    <div class='mb-1'>
        Products
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

    {{-- Admin Only Create New Product and View Products Buttons --}}
    @if (auth()->user()->role_id === App\Models\Role::ADMIN || auth()->user()->role_id === App\Models\Role::STAFF)
        <div class="justify-content-between mb-4">
            <a href="{{ route('dashboard.products.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Add Product
            </a>
            <a href="{{ route('dashboard.products.show', $firstProduct->id) }}" class="btn btn-secondary">
                <i class="bi bi-eye-fill"></i> View Product History
            </a>
        </div>
    @endif


    {{-- Filter --}}
    <form action="{{ route('dashboard.products.index') }}" method="GET" class="mb-4">
        <div class="row">
            <div class="col-md-6">
                <label for="category" class="form-label">Choose Category</label>
                <select class="form-select" id="category" name="category" onchange="this.form.submit()">
                    @foreach ($categories as $category)
                        <option value="{{ $category['name'] }}" @if (request()->input('category', 'All') == $category['name']) selected @endif>
                            {{ $category['name'] }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </form>
    {{-- End Of Filter --}}

    {{-- Product Table --}}
    <div class="card shadow-lg">
        <div class="card-body">
            <h5 class="card-title text-center mb-4">Product List</h5>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-primary">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Product Name</th>
                            <th scope="col" class="text-start">Price</th>
                            <th scope="col" class="text-start">Quantity In Stock</th>
                            @if (auth()->user()->role_id === App\Models\Role::ADMIN)
                                <th scope="col" class="d-flex justify-content-center gap-2">Actions</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $product)
                            <tr>
                                <th scope="row">
                                    {{ $loop->iteration + ($products->currentPage() - 1) * $products->perPage() }}</th>
                                <td>{{ $product->name }}</td>
                                <td class="text-start">₱ {{ number_format($product->price, 2) }}</td>
                                <td class="text-start">{{ $product->quantity_in_stock }}</td>
                                @if (auth()->user()->role_id === App\Models\Role::ADMIN)
                                    <td class="d-flex justify-content-center gap-2">
                                        <a href="{{ route('dashboard.products.edit', $product->id) }}"
                                            class="btn btn-primary btn-sm">
                                            <i class="bi bi-pencil-square"></i> Manage
                                        </a>

                                        <form action="{{ route('dashboard.products.destroy', $product->id) }}"
                                            method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="bi bi-trash-fill"></i> Delete
                                            </button>
                                        </form>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Pagination --}}
    <div class="d-flex justify-content-end mt-3">
        {{ $products->links() }}
    </div>
@endsection
