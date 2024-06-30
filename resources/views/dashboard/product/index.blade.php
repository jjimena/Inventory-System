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
        <div class="alert alert-success mb-4" role="alert">
            {{ session('success') }}
        </div>
    @endif
    {{-- End Of Alert Success --}}

    @if (auth()->user()->role_id === App\Models\Role::ADMIN || auth()->user()->role_id === App\Models\Role::STAFF)
        <a href="{{ route('dashboard.products.create') }}" class="btn btn-primary mb-4">Create New Product</a>
    @endif
    {{-- Filter --}}
    <form action="{{ route('dashboard.products.index') }}" method="GET" class="mb-4">
        <div class="row">
            <div class="col-md-6">
                <label for="category" class="form-label">Choose Category</label>
                <select class="form-select" id="category" name="category" onchange="this.form.submit()">
                    @foreach ($categories as $category)
                        @if (empty(request()->input('category')))
                            <option value="{{ $category['name'] }}" selected>{{ $category['name'] }}</option>
                        @elseif (request()->input('category') == $category['name'])
                            <option value="{{ $category['name'] }}" selected>{{ $category['name'] }}</option>
                        @else
                            <option value="{{ $category['name'] }}">{{ $category['name'] }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
        </div>
    </form>
    {{-- End Of Filter --}}

    {{-- Table --}}
    <div class="table-responsive mt-4">
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Name</th>
                    <th scope="col">Price</th>
                    <th scope="col">Quantity In Stock</th>
                    @if (auth()->user()->role_id === App\Models\Role::ADMIN)
                        <th scope="col">Actions</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $product)
                    <tr>
                        <th scope="row">{{ $loop->iteration }}</th>
                        <td>{{ $product->name }}</td>
                        <td class="price-column">₱ {{ number_format($product->price, 2) }}</td>
                        <td>{{ $product->quantity_in_stock }}</td>
                        @if (auth()->user()->role_id === App\Models\Role::ADMIN)
                            <td class="d-flex justify-items-center gap-2">
                                <a href="{{ route('dashboard.products.show', $product->id) }}"
                                    class="btn btn-secondary btn-sm">View</a>

                                <a href="{{ route('dashboard.products.edit', $product->id) }}"
                                    class="btn btn-primary btn-sm">Manage</a>

                                <form action="{{ route('dashboard.products.destroy', $product->id) }}" method="POST"
                                    class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                            </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {{-- End Of Table --}}


    <div>
        {{ $products->links() }}
    </div>
@endsection
