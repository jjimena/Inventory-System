@extends('layouts.app')

@section('title')
    Users
@endsection

@section('title-header')
    <div class='mb-1'>
        All Users
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

    <div class="d-flex justify-content-between mb-4">
        <div>
            <a href="{{ route('dashboard.users.create') }}" class="btn btn-primary">
                <i class="bi bi-person-plus-fill"></i> Create New User
            </a>
        </div>
    </div>

    {{-- Table --}}
    <div class="card shadow-lg">
        <div class="card-body">
            <div class="table-responsive mt-4">
                <table class="table table-striped table-hover">
                    <thead class="table-primary">
                        <tr>
                            <th>#</th>
                            <th>Id</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone Number</th>
                            <th>Address</th>
                            <th>Role</th>
                            <th class="d-flex justify-content-center gap-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <th scope="row">{{ $loop->iteration }}</th>
                                <td>{{ $user->id }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->phone_number }}</td>
                                <td>{{ $user->address }}</td>
                                <td>
                                    {{ $user->role_id == \App\Models\Role::ADMIN ? 'Admin' : ($user->role_id == \App\Models\Role::HUB ? 'Hub' : 'Staff') }}
                                </td>
                                <td class="d-flex justify-content-center gap-2">
                                    {{-- <a href="{{ route('dashboard.users.show', $user->id) }}"
                                        class="btn btn-secondary btn-sm">
                                        <i class="bi bi-eye-fill"></i> View
                                    </a> --}}
                                    <a href="{{ route('dashboard.users.edit', $user->id) }}" class="btn btn-primary btn-sm">
                                        <i class="bi bi-pencil-square"></i> Update
                                    </a>
                                    <form action="{{ route('dashboard.users.destroy', $user->id) }}" method="POST"
                                        class="d-inline"
                                        onsubmit="return confirm('Are you sure you want to delete this user?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="bi bi-trash-fill"></i> Delete
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
    <div class="d-flex justify-content-end mt-3">
        {{ $users->links() }}
    </div>
@endsection
