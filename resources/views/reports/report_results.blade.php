@extends('layouts.app')

@section('title', 'Sales Report')

@section('content')
    <div class="container mt-5">
        <h1 class="mb-4">{{ ucfirst($type) }} Sales Report</h1>

        <div class="form-group mb-4">
            <form action="{{ route('dashboard.reports.generate') }}" method="POST" class="form-inline">
                @csrf
                <div class="form-group mr-2">
                    <label for="type" class="mr-2">Select Report Type:</label>
                    <select name="type" id="type" class="form-control" style="width: auto;"
                        onchange="this.form.submit()">
                        <option value="monthly" {{ $type === 'monthly' ? 'selected' : '' }}>Monthly Sales</option>
                        <option value="yearly" {{ $type === 'yearly' ? 'selected' : '' }}>Annual Sales</option>
                    </select>
                </div>
                <div class=" d-flex flex-column gap-3 table-responsive gap-2">
                    <noscript><button type="submit" class="btn btn-primary">View Report</button></noscript>
                </div>
            </form>
        </div>

        <div class="table-responsive gap-2">
            @if ($type === 'monthly')
                <table class="table table-bordered table-striped">
                    <thead class="thead-dark">
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
            @elseif ($type === 'yearly')
                <table class="table table-bordered table-striped">
                    <thead class="thead-dark">
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
            @endif
        </div>

        {{ $salesData->links() }}

        <form action="{{ route('dashboard.reports.generate') }}" method="POST" class="mt-4">
            @csrf
            <input type="hidden" name="type" value="{{ $type }}">
            <input type="hidden" name="download" value="pdf">
            <button type="submit" class="btn btn-success">Download as PDF</button>
        </form>
    </div>
@endsection
