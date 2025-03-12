@extends('layouts.app')

@section('title')
    Categories
@endsection

@section('title-header')
    <div class='mb-1'>
        Product Categories
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
    {{-- End Of Alert Success --}}

    <a href="{{ route('dashboard.categories.create') }}" class="btn btn-primary mb-4">
        <i class="bi bi-plus-circle"></i> Create New Category
    </a>
    {{-- <a href="{{ route('dashboard.categories.create') }}" class="btn btn-secondary mb-4">
        <i class="bi bi-plus-circle"></i> Create New Product
    </a> --}}

    {{-- Table --}}
    <div class="card shadow-lg">
        <div class="card-body">
            <h5 class="card-title text-center mb-4">Category List</h5>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-primary">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Name</th>
                            <th scope="col">Products</th>
                            <th scope="col" class="d-flex justify-content-center gap-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($categories as $category)
                            <tr>
                                <th scope="row">{{ $loop->iteration }}</th>
                                <td>{{ $category->name }}</td>
                                <td>{{ $category->products->count() }}</td>
                                <td class="d-flex justify-content-center gap-2">
                                    <form action="{{ route('dashboard.categories.destroy', $category->id) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="bi bi-trash-fill"></i> Delete
                                        </button>
                                    </form>
                                    <a href="{{ route('dashboard.categories.edit', $category->id) }}"
                                        class="btn btn-primary btn-sm">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </a>
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
        {{ $categories->links() }}
    </div>
@endsection
