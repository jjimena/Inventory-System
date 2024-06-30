@extends('layouts.app')

@section('title', ucfirst($type) . ' Sales Report')

@section('title-header')
    <div class='mb-1'>
        Generate Reports
    </div>
@endsection
@section('content')
    <div>
        <h1>{{ ucfirst($type) }} Sales Report</h1>

        <div class="form-group mt-2 ">
            <div class="row align-items-center">
                <div class="col-md-2">
                    <form action="{{ route('dashboard.reports.generate') }}" method="POST">
                        @csrf
                        <label for="type">Select Report Type:</label>
                        <div class="d-flex">
                            <select name="type" id="type" class="form-control" onchange="this.form.submit()">
                                <option value="monthly" {{ $type === 'monthly' ? 'selected' : '' }}>Monthly Sales</option>
                                <option value="yearly" {{ $type === 'yearly' ? 'selected' : '' }}>Annual Sales</option>
                            </select>
                            <noscript><button type="submit" class="btn btn-primary ml-2">View Report</button></noscript>
                        </div>
                    </form>
                </div>
                <div class="col-md-10 text-md-right mt-2 mt-md-0">
                    <form action="{{ route('dashboard.reports.generate') }}" method="POST">
                        @csrf
                        <input type="hidden" name="type" value="{{ $type }}">
                        <input type="hidden" name="download" value="pdf">
                        <button type="submit" class="btn btn-success">Generate Report</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    @if ($type === 'monthly')
        <div class="mt-3">
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
    @elseif ($type === 'yearly')
        <div class="mt-3">
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
    @endif
    </div>
@endsection

{{-- <div class="ml-auto">
    <form id="generateReportForm" action="{{ route('dashboard.generate.report', ['type' => 'monthly']) }}"
        method="get">
        @csrf
        <div class="form-group">
            <label for="reportType">Select Report Type:</label><br>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" id="monthlyReport" name="reportType" value="monthly"
                    checked>
                <label class="form-check-label" for="monthlyReport">Monthly</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" id="yearlyReport" name="reportType" value="yearly">
                <label class="form-check-label" for="yearlyReport">Yearly</label>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Generate Report</button>
    </form>
</div> --}}
