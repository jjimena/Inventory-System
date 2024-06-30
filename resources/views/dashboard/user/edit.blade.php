@extends('layouts.app')

@section('title')
    Update User
@endsection

@section('title-header')
    <div class='mb-1'>
        Update User
    </div>
@endsection

@section('content')
    <div class="col-md-6 justify-content-left">
        <a href="{{ url()->previous() }}" class="btn btn-success">Back</a>
    </div>
    <div class="mt-2 d-flex justify-content-left">
        <div class="col-md-6"> <!-- Adjust the column width as needed -->
            <form class="d-flex flex-column gap-2" method="POST" action="{{ route('dashboard.users.update', $user->id) }}">
                @csrf
                @method('PUT')

                {{-- Name --}}
                <div class="form-floating">
                    <input id="name" type="text" class="form-control @error('name') is-invalid @enderror"
                        name="name" required autocomplete="name" autofocus placeholder="Name"
                        value="{{ old('name', $user->name) }}">
                    <label for="name">Name</label>

                    @error('name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                {{-- End Of Name --}}

                {{-- Email --}}
                <div class="form-floating">
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                        name="email" required autocomplete="email" placeholder="Email"
                        value="{{ old('email', $user->email) }}" />
                    <label for="email">Email</label>

                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                {{-- End Of Email --}}

                {{-- Phone Number --}}
                <div class="form-floating">
                    <input id="phone_number" type="tel" class="form-control @error('phone_number') is-invalid @enderror"
                        name="phone_number" required autocomplete="phone_number" placeholder="Phone Number"
                        value="{{ old('phone_number', $user->phone_number) }}">
                    <label for="phone_number">Phone Number</label>

                    @error('phone_number')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                {{-- End Of Phone Number --}}

                {{-- Hub Name --}}
                <div class="form-floating">
                    <input id="hub_name" type="text" class="form-control @error('hub_name') is-invalid @enderror"
                        name="hub_name" autocomplete="hub_name" placeholder="Hub Name"
                        value="{{ old('hub_name', $user->hub_name) }}">
                    <label for="hub_name">Hub Name</label>

                    @error('hub_name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                {{-- End Of Hub Name --}}

                {{-- Address --}}
                <div class="form-floating">
                    <input id="address" type="text" class="form-control @error('address') is-invalid @enderror"
                        name="address" required autocomplete="address" placeholder="Address"
                        value="{{ old('address', $user->address) }}">
                    <label for="address">Address</label>

                    @error('address')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                {{-- End Of Address --}}


                {{-- Select Role --}}
                <div class="form-floating">
                    <select id="role_id" name="role_id" class="form-select @error('role_id') is-invalid @enderror">
                        <option value="">Select Role</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}" {{ $user->role_id == $role->id ? 'selected' : '' }}>
                                {{ ucfirst($role->name) }}</option>
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


                {{-- Password --}}
                <div class="form-floating">
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                        name="password" autocomplete="password" placeholder="Password" />
                    <label for="password">New Password</label>

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
                        class="form-control @error('password') is-invalid @enderror" name="password_confirmation"
                        autocomplete="password_confirmation" placeholder="Confirm Password" />
                    <label for="password_confirmation">Confirm New Password</label>

                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                {{-- End Of Confirm Password --}}

                {{-- Button Submit --}}
                <button class="btn btn-primary w-100 py-2" type="submit">Save</button>
                {{-- End Of Button Submit --}}
            </form>
        </div>
    </div>
@endsection
