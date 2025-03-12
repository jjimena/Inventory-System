<div>
    <h2>{{ ucfirst($type) }} Sales Report</h2>

    @if ($type === 'daily')
        <h3>Date: {{ \Carbon\Carbon::parse($date)->format('F d, Y') }}</h3>
    @elseif ($type === 'monthly')
        <h3>{{ \Carbon\Carbon::create()->month($month)->format('F') }} {{ $year }}</h3>
    @elseif ($type === 'yearly')
        <h3>{{ $year }}</h3>
    @endif

    <table class="table">
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Total Order</th>
                <th>Unit Price</th>
                <th>Total Price</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($productSalesData as $product)
                <tr>
                    <td>{{ $product->product_name }}</td>
                    <td class="text-start">{{ $product->total_quantity }}</td>
                    <td class="text-start">{{ number_format($product->unit_price, 2) }}</td>
                    <td class="text-start">{{ number_format($product->total_price, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="text-end"><strong>Total:</strong></td>
                <td class="text-start"><strong>{{ number_format($productSalesData->sum('total_price'), 2) }}</strong>
                </td>
            </tr>
        </tfoot>
    </table>
</div>
