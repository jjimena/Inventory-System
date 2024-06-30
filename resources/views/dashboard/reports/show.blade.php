@extends('layouts.app')

@section('title')
    {{ ucfirst($type) }} Report
@endsection

@section('content')
    <h1>{{ ucfirst($type) }} Report</h1>

    {{-- Display report data here --}}
    <div class="table-responsive mt-4">
        <table class="table">
            <thead>
                <tr>
                    <th>Period</th>
                    <th>Customer Name</th>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Total Sales</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($salesData as $data)
                    <tr>
                        <td>{{ $data->period ?? $data->year . ' ' . $data->month }}</td>
                        <td>{{ $data->customer_name }}</td>
                        <td>{{ $data->product_name }}</td>
                        <td>{{ $data->quantity }}</td>
                        <td>₱ {{ number_format($data->total_sales, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <a href="{{ route('dashboard.download.report', ['type' => $type]) }}" class="btn btn-primary">Download PDF</a>
@endsection
