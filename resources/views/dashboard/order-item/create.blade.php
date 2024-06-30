@extends('layouts.app')

@section('title')
    Purchase Order
@endsection

@section('title-header')
    <div class='mb-1'>
        Purchase Order
    </div>
@endsection

@section('content')
    <div class="col-md-6">
        <a href="{{ route('dashboard.order-items.index') }}" class="btn btn-primary">Back</a>
    </div>

    @if (session()->has('success'))
        <div class="mt-3 alert alert-success" role="alert">
            {{ session('success') }}
        </div>
    @endif

    <div class="mt-2 d-flex justify-content-left">
        <div class="col-md-6">
            <form class="d-flex flex-column gap-2" method="POST" action="{{ route('dashboard.order-items.store') }}">
                @csrf

                {{-- Product ID --}}
                <div class="form-floating">
                    <select id="product_id" name="product_id" class="form-control @error('product_id') is-invalid @enderror"
                        required autofocus onchange="updateProductDetails()">
                        @php
                            $displayedProducts = [];
                        @endphp
                        @foreach ($products->sortBy('created_at') as $product)
                            @if (!array_key_exists($product->name, $displayedProducts))
                                @if ($product->quantity_in_stock > 0)
                                    <option value="{{ $product->id }}" data-price="{{ $product->price }}"
                                        data-stock="{{ $product->quantity_in_stock }}">
                                        {{ $product->name }}
                                    </option>
                                    @php
                                        // Track that this product name has been displayed
                                        $displayedProducts[$product->name] = $product->quantity_in_stock;
                                    @endphp
                                @endif
                            @endif
                        @endforeach
                    </select>
                    <label for="product_id">Product Name</label>

                    @error('product_id')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                {{-- End Of Product ID --}}

                {{-- Price --}}
                <div class="form-floating">
                    <input id="price" type="text" class="form-control" name="price" disabled placeholder="Price">
                    <label for="price">Price</label>
                </div>
                {{-- End Of Price --}}

                {{-- Quantity in Stock --}}
                <div class="form-floating">
                    <input id="quantity_in_stock" type="text" class="form-control" name="quantity_in_stock" disabled
                        placeholder="Quantity in Stock">
                    <label for="quantity_in_stock">Stock on Hand</label>
                </div>
                {{-- End Of Quantity in Stock --}}


                {{-- Quantity --}}
                <div class="form-floating">
                    <input id="quantity" type="number" class="form-control @error('quantity') is-invalid @enderror"
                        name="quantity" required autocomplete="quantity" autofocus placeholder="Quantity"
                        value="{{ old('quantity') }}">
                    <label for="quantity">Quantity</label>

                    @error('quantity')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                {{-- End Of Quantity --}}

                @if (auth()->user()->role_id === App\Models\Role::ADMIN || auth()->user()->role_id === App\Models\Role::STAFF)
                    <div class="form-floating">
                        <select id="customer_id" name="customer_id"
                            class="form-control @error('customer_id') is-invalid @enderror" required autofocus>
                            @foreach ($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->customer_name }}</option>
                            @endforeach
                        </select>
                        <label for="customer_id">Distributor</label>

                        @error('customer_id')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                @else
                    <input type="hidden" name="customer_id" value="{{ auth()->user()->id }}">
                @endif

                {{-- Button Submit --}}
                <button class="btn btn-primary w-100 py-2" type="submit">Save</button>
                {{-- End Of Button Submit --}}
            </form>
        </div>
    </div>

    {{-- JavaScript to update price and stock --}}
    <script>
        function updateProductDetails() {
            const productSelect = document.getElementById('product_id');
            const selectedOption = productSelect.options[productSelect.selectedIndex];
            const price = selectedOption.getAttribute('data-price');
            const stock = selectedOption.getAttribute('data-stock');

            document.getElementById('price').value = price;
            document.getElementById('quantity_in_stock').value = stock;
        }

        // Set the initial product details on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateProductDetails();
        });
    </script>
@endsection
