@extends('layouts.app')

@section('title')
    Dashboard
@endsection

@section('title-header')
    <div class='mb-1'>
        Dashboard
    </div>
@endsection
<style>
    /* Ensure all cards in the summary row are the same height */
    .summary-card {
        display: flex;
        flex-direction: column;
        justify-content: center;
        height: 100%;
    }

    .summary-row {
        display: flex;
        flex-wrap: wrap;
        gap: 1.5rem;
    }

    /* Style the out-of-stock list */
    .out-of-stock-list {
        max-height: 150px;
        overflow-y: auto;
        padding: 0;
        margin: 0;
        list-style: none;
    }

    .out-of-stock-list li {
        font-size: 0.9rem;
        font-weight: bold;
        color: #dc3545;
    }

    .out-of-stock-list::-webkit-scrollbar {
        width: 6px;
    }

    .out-of-stock-list::-webkit-scrollbar-thumb {
        background-color: #ced4da;
        border-radius: 3px;
    }

    body {
        font-family: 'Arial', sans-serif;
        background-color: #f8f9fa;
    }

    .card {
        border-radius: 0.75rem;
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }

    .card-box {
        height: 127px;
    }

    .card-box-pl {
        height: 385px;
    }

    .card:hover {
        transform: scale(1.02);
        box-shadow: 0px 10px 20px rgba(0, 0, 0, 0.1);
    }

    .card-title {
        font-size: 1rem;
        font-weight: 600;
        text-transform: uppercase;
        color: #6c757d;
    }

    .chart-container {
        position: relative;
        height: 300px;
        width: 100%;
    }

    .form-select {
        min-width: 120px;
        font-size: 0.875rem;
    }

    .fw-bold {
        font-weight: 700 !important;
    }

    .text-primary {
        color: #007bff !important;
    }

    .text-success {
        color: #28a745 !important;
    }

    .text-danger {
        color: #dc3545 !important;
    }
</style>

@section('content')
    <div class="container mt-4">
        <!-- Summary Row -->
        <div class="row mb-4 g-4">
            <!-- Number of Products Card -->
            <div class="col-md-4">
                <div class="card shadow-lg border-0">
                    <div class="card-body text-center">
                        <h6 class="card-title text-uppercase text-muted mb-2">Number of Products</h6>
                        <p class="card-text display-4 fw-bold">{{ $totalQuantityInStock }}</p>
                    </div>
                </div>
            </div>

            <!-- Products Out of Stock Card -->
            <div class="col-md-4">
                <div class="card shadow-lg border-0">
                    <div class="card-body card-box text-center">
                        <h6 class="card-title text-uppercase text-muted mb-2">Products Out of Stock</h6>
                        <div class="card-text">
                            @if (count($outOfStockProducts) > 0)
                                <ul class="list-unstyled text-danger fw-bold">
                                    @foreach ($outOfStockProducts as $product)
                                        <li>{{ $product->name }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="display-4 text-success fw-bold">0</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Sales Card -->
            <div class="col-md-4">
                <div class="card shadow-lg border-0">
                    <div class="card-body text-center">
                        <h6 class="card-title text-uppercase text-muted mb-2">Total Sales</h6>
                        <p class="card-text display-4 fw-bold">₱ {{ number_format($totalSales, 2) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="row g-4">
            <!-- Sales Distribution Chart -->
            <div class="col-lg-6">
                <div class="card shadow-lg border-0">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="card-title text-uppercase text-muted">Sales Distribution</h6>
                            <div class="d-flex">
                                <select id="yearSelector" class="form-select me-2">
                                    @foreach ($availableYears as $year)
                                        <option value="{{ $year }}">{{ $year }}</option>
                                    @endforeach
                                </select>
                                <select id="salesViewSelector" class="form-select me-2">
                                    <option value="annual">Annual</option>
                                    <option value="monthly">Monthly</option>
                                </select>
                                <select id="monthSelector" class="form-select d-none">
                                    @foreach (range(1, 12) as $month)
                                        <option value="{{ $month }}">
                                            {{ DateTime::createFromFormat('!m', $month)->format('F') }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                        </div>
                        <div class="chart-container">
                            <canvas id="salesChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product List Chart -->
            <div class="col-lg-6">
                <div class="card shadow-lg border-0">
                    <div class="card-body card-box-pl">
                        <h6 class="card-title text-uppercase text-muted mb-3">Product List</h6>
                        <div class="chart-container">
                            <canvas id="productListChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const annualSalesData = @json($annualSalesData);
        const monthlySalesData = @json($monthlySalesData);

        const ctxSales = document.getElementById('salesChart').getContext('2d');
        let salesChart;

        const yearSelector = document.getElementById('yearSelector');
        const salesViewSelector = document.getElementById('salesViewSelector');
        const monthSelector = document.getElementById('monthSelector');

        const truncateProductName = (name, maxLength = 5) => {
            return name.length > maxLength ? `${name.substring(0, maxLength)}...` : name;
        };

        const updateSalesChart = (viewType, year, month = null) => {
            let labels = [];
            let data = [];
            let label = '';

            if (viewType === 'annual') {
                const filteredData = annualSalesData.filter(item => item.year == year);
                labels = filteredData.map(item => truncateProductName(item.product_name) +
                    ` (₱${item.price.toFixed(2)})`);
                data = filteredData.map(item => item.total_sales);
                label = `Annual Sales (${year})`;
            } else if (viewType === 'monthly' && month) {
                const filteredData = monthlySalesData.filter(item => item.year == year && item.month ==
                    month);
                labels = filteredData.map(item => truncateProductName(item.product_name) +
                    ` (₱${item.price.toFixed(2)})`);
                data = filteredData.map(item => item.total_sales);
                label =
                    `Sales in ${new Date(0, month - 1).toLocaleString('default', { month: 'long' })} ${year}`;
            }

            if (salesChart) salesChart.destroy();

            salesChart = new Chart(ctxSales, {
                type: 'bar',
                data: {
                    labels: labels.length > 0 ? labels : ["No Data"],
                    datasets: [{
                        label: label,
                        data: data.length > 0 ? data : [0], // Ensure chart is not empty
                        backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Total Sales (₱)'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Products (Name and Price)'
                            }
                        }
                    }
                }
            });
        };

        // Initialize chart with default year selection
        updateSalesChart('annual', yearSelector.value);

        yearSelector.addEventListener('change', function() {
            if (salesViewSelector.value === 'annual') {
                updateSalesChart('annual', this.value);
            } else {
                updateSalesChart('monthly', this.value, monthSelector.value);
            }
        });

        salesViewSelector.addEventListener('change', function() {
            if (this.value === 'annual') {
                monthSelector.classList.add('d-none');
                updateSalesChart('annual', yearSelector.value);
            } else {
                monthSelector.classList.remove('d-none');
                updateSalesChart('monthly', yearSelector.value, monthSelector.value);
            }
        });

        monthSelector.addEventListener('change', function() {
            updateSalesChart('monthly', yearSelector.value, this.value);
        });
    });


    // Product List Chart Data
    const productListData = @json($productListData);
    const ctxProductList = document.getElementById('productListChart').getContext('2d');
    // Function to truncate product names
    // const truncateProductName = (name, maxLength = 5) => {
    //     return name.length > maxLength ? `${name.substring(0, maxLength)}...` : name;
    // };

    new Chart(ctxProductList, {
        type: 'bar',
        data: {
            // labels: productListData.map(item => item.product_name),
            labels: productListData.map(item => truncateProductName(item
                .product_name)), // Apply truncation here
            datasets: [{
                label: 'Product Quantity',
                data: productListData.map(item => item.quantity),
                backgroundColor: 'rgba(75, 192, 192, 0.5)',
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const productListData = @json($productListData);
        const ctxProductList = document.getElementById('productListChart').getContext('2d');

        const truncateProductName = (name, maxLength = 5) => {
            return name.length > maxLength ? `${name.substring(0, maxLength)}...` : name;
        };

        new Chart(ctxProductList, {
            type: 'bar',
            data: {
                labels: productListData.map(item => truncateProductName(item.product_name)),
                datasets: [{
                    label: 'Product Quantity',
                    data: productListData.map(item => item.quantity),
                    backgroundColor: 'rgba(75, 192, 192, 0.5)',
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    });
</script>
