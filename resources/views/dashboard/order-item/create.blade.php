@extends('layouts.app')

@section('title')
    Purchase Order
@endsection

@section('title-header')
    <div class='mb-1'>
        New Order
    </div>
@endsection

@section('content')
    @if (session()->has('success'))
        <div class="alert alert-success" role="alert">
            {{ session('success') }}
        </div>
    @endif

    <div class="card shadow-lg">
        <div class="card-body">
            <h5 class="card-title mb-4 text-center">Purchase Item</h5>
            <form method="POST" action="{{ route('dashboard.order-items.store') }}">
                @csrf
                <div class="row g-3">
                    {{-- Product Selection --}}
                    <div class="col-md-6">
                        <label for="product_id" class="form-label">
                            <i class="bi bi-box"></i> Product
                        </label>
                        <select id="product_id" name="product_id"
                            class="form-select @error('product_id') is-invalid @enderror" onchange="updateProductDetails()"
                            required>
                            @foreach ($products->sortBy('name') as $product)
                                @if ($product->quantity_in_stock > 0)
                                    <option value="{{ $product->id }}" data-price="{{ $product->price }}"
                                        data-stock="{{ $product->quantity_in_stock }}">
                                        {{ $product->name }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                        @error('product_id')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                        <input type="hidden" id="original_price" name="original_price">
                    </div>

                    {{-- Quantity --}}
                    <div class="col-md-6">
                        <label for="quantity" class="form-label">
                            <i class="bi bi-sort-numeric-up"></i> Quantity
                        </label>
                        <input type="number" id="quantity" name="quantity"
                            class="form-control @error('quantity') is-invalid @enderror" placeholder="Enter quantity"
                            value="{{ old('quantity') }}" required>
                        @error('quantity')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Price --}}
                    <div class="col-md-6">
                        <label for="price" class="form-label">
                            <i class="bi bi-currency-dollar"></i> Price
                        </label>
                        <input type="text" id="price" name="price" class="form-control" disabled
                            placeholder="Price">
                    </div>

                    {{-- Stock --}}
                    <div class="col-md-6">
                        <label for="quantity_in_stock" class="form-label">
                            <i class="bi bi-boxes"></i> Stock on Hand
                        </label>
                        <input type="text" id="quantity_in_stock" name="quantity_in_stock" class="form-control" disabled
                            placeholder="Available stock">
                    </div>

                    {{-- Customer Type --}}
                    <div class="col-md-6">
                        <label for="customer_type" class="form-label">
                            <i class="bi bi-tags-fill"></i> Customer Type
                        </label>
                        <select id="customer_type" name="customer_type" class="form-select" required>
                            <option value="">All Customer</option>
                            @foreach ($customerTypes as $type)
                                <option value="{{ $type }}">{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Buyer --}}
                    <div class="col-md-6">
                        <label for="customer_id" class="form-label">
                            <i class="bi bi-person-fill"></i> Buyer
                        </label>
                        <select id="customer_id" name="customer_id" class="form-select" required>
                            <option value="">Select Buyer</option>
                            @foreach ($customers as $customer)
                                <option value="{{ $customer->id }}" data-type="{{ $customer->customer_type }}">
                                    {{ $customer->customer_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Submit Button --}}
                    <div class="d-grid mt-4">
                        <button class="btn btn-success btn-lg" type="submit">
                            <i class="bi bi-save"></i> Save
                        </button>
                    </div>
            </form>
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let customerTypeSelect = document.getElementById("customer_type");
            let buyerSelect = document.getElementById("customer_id");

            customerTypeSelect.addEventListener("change", function() {
                let selectedType = this.value;

                // Reset buyer dropdown to default option
                buyerSelect.innerHTML = '<option value="">Select Buyer</option>';

                // Filter buyers based on selected customer type
                @json($customers).forEach(customer => {
                    if (selectedType === "" || customer.customer_type === selectedType) {
                        let option = document.createElement("option");
                        option.value = customer.id;
                        option.dataset.type = customer.customer_type;
                        option.textContent = customer.customer_name;
                        buyerSelect.appendChild(option);
                    }
                });
            });
        });
    </script>
    <script>
        function updateCustomerList() {
            const customerType = document.getElementById('customer_type').value;
            const customerSelect = document.getElementById('customer_id');
            customerSelect.innerHTML = '<option value="">Select Customer</option>';

            fetch(`{{ url('/dashboard/customers-by-type') }}/${customerType}`)
                .then(response => response.json())
                .then(customers => {
                    customers.forEach(customer => {
                        let option = document.createElement('option');
                        option.value = customer.id;
                        option.textContent = customer.customer_name;
                        customerSelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Error fetching customers:', error));
        }

        function updateProductDetails() {
            const productSelect = document.getElementById('product_id');
            const selectedOption = productSelect.options[productSelect.selectedIndex];
            const price = parseFloat(selectedOption.getAttribute('data-price'));
            const stock = selectedOption.getAttribute('data-stock');

            document.getElementById('price').value = price.toFixed(2);
            document.getElementById('quantity_in_stock').value = stock;
            document.getElementById('original_price').value = price;

            updatePrice(); // Ensure price updates when changing products
        }

        function updatePrice() {
            const customerType = document.getElementById('customer_type').value;
            const originalPrice = parseFloat(document.getElementById('original_price').value);

            let finalPrice = originalPrice; // Default to original price

            if (customerType === "Wholesale") {
                if (originalPrice >= 1000) {
                    finalPrice -= 60; // Deduct 60 if price is 1000 or more
                } else if (originalPrice > 200 && originalPrice < 1000) {
                    finalPrice -= 40; // Deduct 40 if price is between 200 and 1000
                }
                // If price is 200 or less, no deduction (default finalPrice = originalPrice)
            }

            document.getElementById('price').value = finalPrice.toFixed(2);
        }

        // Ensure the price updates instantly when customer type changes
        document.getElementById('customer_type').addEventListener('change', updatePrice);

        document.addEventListener('DOMContentLoaded', function() {
            updateProductDetails();
        });

        document.getElementById('product_id').addEventListener('change', function() {
            updateProductDetails();
        });
    </script>
@endsection
