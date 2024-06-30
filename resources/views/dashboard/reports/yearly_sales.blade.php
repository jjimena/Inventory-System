{{-- <div class="container">
    <h1>Yearly Sales Report</h1>

    <table class="table">
        <thead>
            <tr>
                <th>Year</th>
                <th>Customer</th>
                <th>Product</th>
                <th>Quantity Ordered</th>
                <th>Total Sales</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($salesData as $data)
                <tr>
                    <td>{{ $data->period }}</td>
                    <td>{{ $data->customer_name }}</td>
                    <td>{{ $data->product_name }}</td>
                    <td>{{ $data->quantity }}</td>
                    <td>{{ $data->total_sales }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div> --}}
<div class="container mt-4">
    <h2>Yearly Sales Report</h2>

    <!-- Aggregated Sales Data -->
    <h3>Aggregated Sales Data</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Year</th>
                <th>Total Sales</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($aggregatedSalesData as $data)
                <tr>
                    <td>{{ $data->period }}</td>
                    <td>{{ number_format($data->total_sales, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Product Sales Data -->
    <h3>Product Sales Data</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Total Quantity Ordered</th>
                <th>Unit Price</th>
                <th>Total Price</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($productSalesData as $product)
                <tr>
                    <td>{{ $product->product_name }}</td>
                    <td>{{ $product->total_quantity }}</td>
                    <td>{{ number_format($product->unit_price, 2) }}</td>
                    <td>{{ number_format($product->total_price, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
