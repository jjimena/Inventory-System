{{-- <!-- resources/views/reports/monthly_sales.blade.php -->

<!DOCTYPE html>
<html>

<head>
    <title>Monthly Sales Report</title>
    <style>
        /* Define your CSS styles here */
        body {
            font-family: Arial, sans-serif;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table,
        th,
        td {
            border: 1px solid #ddd;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>
    <h1>Monthly Sales Report</h1>

    <table>
        <thead>
            <tr>
                <th>Year</th>
                <th>Month</th>
                <th>Customer</th>
                <th>Product</th>
                <th>Quantity Ordered</th>
                <th>Total Sales</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($salesData as $data)
                <tr>
                    <td>{{ $data->year }}</td>
                    <td>{{ $data->month }}</td>
                    <td>{{ $data->customer_name }}</td>
                    <td>{{ $data->product_name }}</td>
                    <td>{{ $data->quantity }}</td>
                    <td>{{ $data->total_sales }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>

</html> --}}


<!-- resources/views/reports/monthly_sales.blade.php -->

@extends('layouts.app')

@section('title', 'Monthly Sales Report')

@section('content')
    <div class="container mt-2">
        <h1>Monthly Sales Report</h1>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Year</th>
                    <th>Month</th>
                    <th>Total Sales</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($salesData as $data)
                    <tr>
                        <td>{{ $data->year }}</td>
                        <td>{{ $data->month }}</td>
                        <td>{{ number_format($data->total_sales, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
