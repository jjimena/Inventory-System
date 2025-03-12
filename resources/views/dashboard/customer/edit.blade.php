@extends('layouts.app')

@section('title')
    Edit Customer
@endsection

@section('title-header')
    <div class='mb-1'>
        Edit Customer
    </div>
@endsection

@section('content')
    <div class="col-md-6">
        <a href="{{ route('dashboard.orders.index') }}" class="btn btn-primary">Back</a>
    </div>

    <div class="mt-2">
        <div class="col-md-6">
            <form class="d-flex flex-column gap-2" method="POST"
                action="{{ route('dashboard.customers.update', $customer->id) }}">
                @csrf
                @method('PUT')

                {{-- Customer Name --}}
                <div class="form-floating">
                    <input id="customer_name" type="text"
                        class="form-control @error('customer_name') is-invalid @enderror" name="customer_name" required
                        autocomplete="customer_name" autofocus placeholder="Customer Name"
                        value="{{ old('customer_name', $customer->customer_name) }}">
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
                    <input id="customer_email" type="email"
                        class="form-control @error('customer_email') is-invalid @enderror" name="customer_email"
                        autocomplete="customer_email" placeholder="Customer Email"
                        value="{{ old('customer_email', $customer->customer_email) }}" />
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
                    <input id="customer_phone_number" type="tel"
                        class="form-control @error('customer_phone_number') is-invalid @enderror"
                        name="customer_phone_number" required autocomplete="customer_phone_number"
                        placeholder="Phone Number"
                        value="{{ old('customer_phone_number', $customer->customer_phone_number) }}">
                    <label for="customer_phone_number">Phone Number</label>

                    @error('customer_phone_number')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                {{-- End Of Phone Number --}}

                {{-- Address --}}
                <div class="form-floating">
                    <input id="address" type="text" class="form-control @error('address') is-invalid @enderror"
                        name="address" required autocomplete="address" placeholder="Address"
                        value="{{ old('address', $customer->address) }}">
                    <label for="address">Address</label>

                    @error('address')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                {{-- End Of Address --}}

                {{-- Customer Type --}}
                <div class="form-floating">
                    <select id="customer_type" name="customer_type"
                        class="form-select @error('customer_type') is-invalid @enderror" required>
                        <option value="Walk-in"
                            {{ old('customer_type', $customer->customer_type) === 'Walk-in' ? 'selected' : '' }}>Walk-in
                        </option>
                        <option value="Wholesale"
                            {{ old('customer_type', $customer->customer_type) === 'Wholesale' ? 'selected' : '' }}>
                            Wholesale</option>
                    </select>
                    <label for="customer_type">Customer Type</label>

                    @error('customer_type')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                {{-- End Of Customer Type --}}


                {{-- Button Submit --}}
                <button class="btn btn-primary w-100 py-2" type="submit">Save</button>
                {{-- End Of Button Submit --}}
            </form>
        </div>
    </div>
@endsection
