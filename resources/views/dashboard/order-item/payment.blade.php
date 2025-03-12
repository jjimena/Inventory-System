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
    {{-- Back Button --}}
    <div class="mb-3">
        <a href="{{ route('dashboard.order-items.index') }}" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left"></i> Back to Order Items
        </a>
    </div>

    {{-- Alerts --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
            <i class="bi bi-exclamation-circle-fill me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Payment Form --}}
    <div class="card shadow-lg">
        <div class="card-body">
            <h5 class="card-title text-center mb-4">Payment for {{ $orderItem->customer->customer_name }}</h5>

            {{-- <h5 class="card-title text-center mb-4">Payment for Order Item #{{ $orderItem->id }}</h5> --}}

            <form method="POST"
                action="{{ route('dashboard.order-items.payment.process', ['orderItemId' => $orderItem->id]) }}">
                @csrf
                <input type="hidden" name="orderItemId" value="{{ $orderItem->id }}">

                <p><strong>Total Price:</strong> ₱ {{ number_format($totalPrice, 2) }}</p>

                <!-- Payment Method Selection -->
                <div class="form-group">
                    <label for="payment_method">Payment Method</label>
                    <select id="payment_method" name="payment_method" class="form-control custom-width" required>
                        <option value="" disabled selected>Select Payment Method</option>
                        <option value="Card" {{ old('payment_method') == 'Card' ? 'selected' : '' }}>Credit/Debit Card
                        </option>
                        <option value="GCash" {{ old('payment_method') == 'GCash' ? 'selected' : '' }}>GCash</option>
                        <option value="cod" {{ old('payment_method') == 'cod' ? 'selected' : '' }}>Cash</option>
                    </select>
                    @error('payment_method')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Reference Number Input -->
                <div id="reference-number-fields" style="display: none;">
                    <div class="form-group">
                        <label for="reference_number">Reference Number</label>
                        <input type="text" id="reference_number" name="reference_number"
                            class="form-control custom-width" value="{{ old('reference_number') }}">
                        @error('reference_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- GCash Number Input -->
                <div id="gcash-number-field" class="form-group" style="display: none;">
                    <label for="gcash_number">GCash Number</label>
                    <input type="text" id="gcash_number" name="gcash_number" class="form-control custom-width"
                        value="{{ old('gcash_number') }}">
                    @error('gcash_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- COD Payment Info -->
                <div id="cod-payment-fields" style="display: none;">
                    <p>Cash on Delivery payment will be collected at the time of delivery.</p>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary">Submit</button>

                <!-- Cancel Payment Button -->
                <a href="{{ route('dashboard.order-items.index') }}" class="btn btn-secondary">Cancel Payment</a>
            </form>
        </div>
    </div>
@endsection

<style>
    /* Custom width for form controls */
    .custom-width {
        max-width: 500px;
        width: 100%;
        margin-bottom: 1rem;
    }

    /* General form styling */
    .form-group {
        margin-bottom: 1.5rem;
    }

    .btn {
        padding: 0.75rem 1.5rem;
    }

    .alert {
        margin-bottom: 1rem;
    }

    .card-body {
        max-width: 500px;
    }

    .card {
        max-width: 500px;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const paymentMethodSelect = document.getElementById('payment_method');
        const referenceNumberFields = document.getElementById('reference-number-fields');
        const gcashNumberField = document.getElementById('gcash-number-field');
        const codPaymentFields = document.getElementById('cod-payment-fields');

        // Update visible fields based on payment method
        function updatePaymentFields() {
            const paymentMethod = paymentMethodSelect.value;
            referenceNumberFields.style.display = paymentMethod === 'Card' || paymentMethod === 'GCash' ?
                'block' : 'none';
            gcashNumberField.style.display = paymentMethod === 'GCash' ? 'block' : 'none';
            codPaymentFields.style.display = paymentMethod === 'cod' ? 'block' : 'none';
        }

        // Attach event listener and initialize fields on page load
        paymentMethodSelect.addEventListener('change', updatePaymentFields);
        updatePaymentFields();
    });
</script>
