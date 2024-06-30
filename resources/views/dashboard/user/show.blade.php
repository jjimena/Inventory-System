@extends('layouts.app')

@section('title')
    User Detail
@endsection

@section('title-header')
    <div class='mb-1'>
        User Detail
    </div>
@endsection

@section('content')
    <div class="col-md-6">
        <a href="{{ route('dashboard.users.index') }}" class="btn btn-primary">Back</a>
    </div>

    <div class="mt-2 d-flex justify-content-left">
        <div class="col-md-6"> <!-- Adjust the column width as needed -->
            <div class="d-flex flex-column gap-2">
                {{-- Name --}}
                <div class="form-floating">
                    <input id="name" type="text" class="form-control" name="name" autocomplete="name"
                        placeholder="Name" value="{{ $user->name }}" disabled />
                    <label for="name">Name</label>
                </div>
                {{-- End Of Name --}}

                {{-- Email --}}
                <div class="form-floating">
                    <input id="email" type="email" class="form-control" name="email" placeholder="Email"
                        value="{{ $user->email }}" disabled />
                    <label for="email">Email</label>
                </div>
                {{-- End Of Email --}}

                {{-- Role --}}
                <div class="form-floating">
                    <input id="role" type="text" class="form-control" name="role" placeholder="role"
                        value="{{ $user->role_id === App\Models\Role::ADMIN ? 'Admin' : 'User' }}" disabled />
                    <label for="role">Role</label>
                </div>
                {{-- End Of Role --}}

                {{-- Created At --}}
                <div class="form-floating">
                    <input id="created-at" type="text" class="form-control" name="created-at" required
                        autocomplete="created-at" placeholder="Created At" value="{{ $user->created_at->diffForHumans() }}"
                        disabled />
                    <label for="created-at">Created At</label>
                </div>
                {{-- End Of Created At --}}

                {{-- Updated At --}}
                <div class="form-floating">
                    <input id="updated-at" type="text" class="form-control" name="updated-at" required
                        autocomplete="updated-at" placeholder="Updated At" value="{{ $user->updated_at->diffForHumans() }}"
                        disabled />
                    <label for="updated-at">Updated At</label>
                </div>
                {{-- End Of Updated At --}}
            </div>
        </div>
    </div>
@endsection
