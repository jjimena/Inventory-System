@extends('layouts.app')

@section('title')
    Create New Order
@endsection

@section('title-header')
    <div class='mb-1'>
        Create New Customer
    </div>
@endsection

@section('content')
    <a href="{{ route('dashboard.orders.index') }}" class="btn btn-primary">Back</a>

    <div class="mt-4">
        <form class="d-flex flex-column gap-3" method="POST" action="{{ route('dashboard.orders.store') }}">
            @csrf


            {{-- Customer Name --}}
            <div class="form-floating">
                <input id="customer_name" type="text" class="form-control @error('customer_name') is-invalid @enderror"
                    name="customer_name" required autocomplete="customer_name" autofocus placeholder="Customer Name"
                    value="{{ old('customer_name') }}">
                <label for="customer_name">Customer Name</label>

                @error('customer_name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            {{-- End Of Customer Name --}}

            {{-- Customer Email --}}
            <div class="form-floating">
                <input id="customer_email" type="email" class="form-control @error('customer_email') is-invalid @enderror"
                    name="customer_email" required autocomplete="customer_email" autofocus placeholder="Customer Email"
                    value="{{ old('customer_email') }}">
                <label for="customer_email">Customer Email</label>

                @error('customer_email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            {{-- End Of Customer Email --}}

            {{-- Phone Number --}}
            <div class="form-floating">
                <input id="phone_number" type="tel" class="form-control @error('phone_number') is-invalid @enderror"
                    name="phone_number" required autocomplete="phone_number" autofocus placeholder="Phone Number"
                    value="{{ old('phone_number') }}">
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
                    name="hub_name" required autocomplete="hub_name" autofocus placeholder="Hub Name"
                    value="{{ old('hub_name') }}">
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
                <textarea id="address" class="form-control @error('address') is-invalid @enderror" name="address" required
                    autocomplete="address" autofocus placeholder="Address" rows="3">{{ old('address') }}</textarea>
                <label for="address">Address</label>

                @error('address')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            {{-- End Of Address --}}

            {{-- Date --}}
            <div class="form-floating">
                <input id="date" type="date" class="form-control @error('date') is-invalid @enderror" name="date"
                    required autocomplete="date" autofocus placeholder="Date" value="{{ old('date') }}">
                <label for="date">Date</label>

                @error('date')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            {{-- End Of Date --}}

            {{-- Button Submit --}}
            <button class="btn btn-primary w-100 py-2" type="submit">Save</button>
            {{-- End Of Button Submit --}}
        </form>
    </div>
@endsection
