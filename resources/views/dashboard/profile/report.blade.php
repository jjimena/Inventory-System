{{-- resources/views/dashboard/order-items/report.blade.php --}}

{{-- <!DOCTYPE html>
<html>

<head>
    <title>Order Items Report</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>
    <h1>Order Items Report</h1>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Order Item</th>
                <th>Quantity</th>
                <th>Price</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($orderItems as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ $item->price }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html> --}}

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ ucfirst($type) }} Sales Report</title>
    <style>
        /* Your CSS styles for the report */
    </style>
</head>

<body>
    <h1>{{ ucfirst($type) }} Sales Report</h1>

    @if ($type === 'monthly')
        <h2>Monthly Sales</h2>
        <table border="1">
            <thead>
                <tr>
                    <th>Year</th>
                    <th>Month</th>
                    <th>Total Sales</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($salesData as $sale)
                    <tr>
                        <td>{{ $sale->year }}</td>
                        <td>{{ Carbon::create()->month($sale->month)->format('F') }}</td>
                        <td>{{ $sale->total_sales }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @elseif ($type === 'yearly')
        <h2>Annual Sales</h2>
        <table border="1">
            <thead>
                <tr>
                    <th>Year</th>
                    <th>Total Sales</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($salesData as $sale)
                    <tr>
                        <td>{{ $sale->year }}</td>
                        <td>{{ $sale->total_sales }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

</body>

</html>
