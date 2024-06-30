@extends('layouts.app')

@section('title')
    Create New User
@endsection

@section('title-header')
    <div class='mb-1'>
        Create New User
    </div>
@endsection

@php
    use App\Models\Role;
@endphp


@section('content')
    <div class="col-md-6 ">
        <a href="{{ route('dashboard.users.index') }}" class="btn btn-primary">Back</a>
    </div>

    <div class="mt-2 d-flex justify-content-left">
        <div class="col-md-6 justify-content-left">
            <form class="d-flex flex-column gap-2" method="POST" action="{{ route('dashboard.users.store') }}">
                @csrf

                {{-- Name --}}
                <div class="form-floating">
                    <input id="name" type="text" class="form-control @error('name') is-invalid @enderror"
                        name="name" required autocomplete="name" autofocus placeholder="Name"
                        value="{{ old('name') }}">
                    <label for="name">Name</label>

                    @error('name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                {{-- End Of Name --}}

                {{-- Select Role --}}
                <div class="form-floating">
                    <select id="role_id" name="role_id" class="form-select @error('role_id') is-invalid @enderror">
                        <option value="">Select Role</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}">{{ ucfirst($role->name) }}</option>
                        @endforeach
                    </select>
                    <label for="role_id">Role</label>

                    @error('role_id')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                {{-- End Of Select Role --}}

                {{-- Hub Name --}}
                <div class="form-floating">
                    <input id="hub_name" type="text" class="form-control @error('hub_name') is-invalid @enderror"
                        name="hub_name" autocomplete="hub_name" placeholder="Hub Name" value="{{ old('hub_name') }}">
                    <label for="hub_name">Hub Name</label>

                    @error('hub_name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                {{-- End Of Hub Name --}}

                {{-- Phone Number --}}
                <div class="form-floating">
                    <input id="phone_number" type="tel" class="form-control @error('phone_number') is-invalid @enderror"
                        name="phone_number" required autocomplete="phone" placeholder="Phone Number"
                        value="{{ old('phone_number') }}">
                    <label for="phone_number">Phone Number</label>

                    @error('phone_number')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                {{-- End Of Phone Number --}}


                {{-- Address --}}
                <div class="form-floating">
                    <input id="address" type="text" class="form-control @error('address') is-invalid @enderror"
                        name="address" required autocomplete="address" placeholder="Address" value="{{ old('address') }}">
                    <label for="address">Address</label>

                    @error('address')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                {{-- End Of Address --}}

                {{-- Email --}}
                <div class="form-floating">
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                        name="email" required autocomplete="email" placeholder="Email" value="{{ old('email') }}" />
                    <label for="email">Email</label>

                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                {{-- End Of Email --}}


                {{-- Password --}}
                <div class="form-floating">
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                        name="password" required autocomplete="password" placeholder="Password" />
                    <label for="password">Password</label>

                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                {{-- End Of Password --}}

                {{-- Confirm Password --}}
                <div class="form-floating">
                    <input id="password_confirmation" type="password"
                        class="form-control @error('password') is-invalid @enderror" name="password_confirmation" required
                        autocomplete="password_confirmation" placeholder="Confirm Password" />
                    <label for="password_confirmation">Confirm Password</label>

                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                {{-- End Of Confirm Password --}}

                {{-- Button Submit --}}
                <button class="btn btn-primary w-100 py-2" type="submit">Submit</button>
                @if (session()->has('success'))
                    <div class="alert alert-success" role="alert">
                        {{ session('success') }}
                    </div>
                @endif
                {{-- End Of Button Submit --}}
            </form>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            $('#role_id').change(function() {
                var roleId = $(this).val();
                if (roleId !== 3) {
                    $('#hub_name').prop('disabled', false);
                } else {
                    $('#hub_name').prop('disabled', true);
                }
            });

            // Initialize state based on the current selected role
            $('#role_id').change(); // Trigger change event to set initial state
        });
    </script>
@endsection
