@extends('layouts.app')

@section('title')
    Payment
@endsection

@section('title-header')
    <div class='mb-1'>
        Payment Form
    </div>
@endsection

@section('content')

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="container">
        <h1>Payment for Order Item #{{ $orderItem->id }}</h1>


        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <p>Total Price: ₱ {{ number_format($totalPrice, 2) }}</p>

        <form method="POST" action="{{ route('dashboard.order-items.payment.process', ['orderItemId' => $orderItem->id]) }}">
            @csrf
            <input type="hidden" name="orderItemId" value="{{ $orderItem->id }}">

            <div class="form-group">
                <label for="payment_method">Payment Method</label>
                <select id="payment_method" name="payment_method" class="form-control" required>
                    <option value="">Select Payment Method</option>
                    <option value="Card" {{ old('payment_method') == 'Card' ? 'selected' : '' }}>Credit/Debit Card
                    </option>
                    <option value="GCash" {{ old('payment_method') == 'GCash' ? 'selected' : '' }}>GCash</option>
                    <option value="cod" {{ old('payment_method') == 'cod' ? 'selected' : '' }}>Cash on Delivery</option>
                </select>
                @error('payment_method')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div id="reference-number-fields" style="display: none;">
                <div class="form-group">
                    <label for="reference_number">Reference Number</label>
                    <input type="text" id="reference_number" name="reference_number" class="form-control"
                        value="{{ old('reference_number') }}">
                    @error('reference_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div id="gcash-number-field" class="form-group" style="display: none;">
                <label for="gcash_number">GCash Number</label>
                <input type="text" id="gcash_number" name="gcash_number" class="form-control"
                    value="{{ old('gcash_number') }}">
                @error('gcash_number')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div id="cod-payment-fields" style="display: none;">
                <p>Cash on Delivery payment will be collected at the time of delivery.</p>
            </div>

            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>

    <script>
        // Prevent back button from showing payment form after successful payment
        window.onload = function() {
            if (window.history.replaceState) {
                window.history.replaceState(null, null, window.location.href);
            }
        };

        // Display relevant payment fields based on selected payment method
        document.addEventListener('DOMContentLoaded', function() {
            const paymentMethodSelect = document.getElementById('payment_method');
            const referenceNumberFields = document.getElementById('reference-number-fields');
            const gcashNumberField = document.getElementById('gcash-number-field');
            const codPaymentFields = document.getElementById('cod-payment-fields');

            paymentMethodSelect.addEventListener('change', function() {
                referenceNumberFields.style.display = 'none';
                gcashNumberField.style.display = 'none';
                codPaymentFields.style.display = 'none';

                if (this.value === 'GCash') {
                    referenceNumberFields.style.display = 'block';
                    gcashNumberField.style.display = 'block';
                } else if (this.value === 'Card') {
                    referenceNumberFields.style.display = 'block';
                } else if (this.value === 'cod') {
                    codPaymentFields.style.display = 'block';
                }
            });

            // Trigger change event on page load to set the correct fields
            paymentMethodSelect.dispatchEvent(new Event('change'));
        });
    </script>
@endsection
