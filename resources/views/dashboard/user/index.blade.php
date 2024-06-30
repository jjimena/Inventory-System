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
        <div class="alert alert-success mb-4" role="alert">
            {{ session('success') }}
        </div>
    @endif
    {{-- End Of Alert Success --}}

    <a href="{{ route('dashboard.users.create') }}" class="btn btn-primary">Create New User</a>

    {{-- Table --}}
    <div class="table-responsive mt-4">
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Id</th>
                    <th scope="col">Name</th>
                    <th scope="col">Email</th>
                    <th scope="col">Phone Number</th>
                    <th scope="col">Address</th>
                    <th scope="col">Role</th>
                    <th scope="col">Action</th>
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
                        </td>
                        <td>{{ $user->role_id == \App\Models\Role::ADMIN ? 'Admin' : ($user->role_id == \App\Models\Role::HUB ? 'Hub' : 'Staff') }}
                        </td>
                        <td class="d-flex justify-items-center gap-2">
                            <a href="{{ route('dashboard.users.show', $user->id) }}" class="btn btn-secondary">View</a>
                            <form action="{{ route('dashboard.users.destroy', $user->id) }}" method="POST">
                                @csrf
                                @method('DELETE')

                                <button type="submit" class="btn btn-danger">Delete</button>
                            </form>
                            <a href="{{ route('dashboard.users.edit', $user->id) }}" class="btn btn-primary">Update</a>
                        </td>
                        {{-- <td class="d-flex justify-items-center gap-2">
                            <!-- Other actions buttons -->
                            @if ($user->role_id == 3)
                                <form action="{{ route('dashboard.customers.create', $user->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')

                                    <button type="submit" class="btn btn-success">Add as Customer</button>
                                </form>
                            @endif
                        </td> --}}
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-3">
            {{ $users->links() }}
        </div>
    </div>
    {{-- End Of Table --}}
@endsection
