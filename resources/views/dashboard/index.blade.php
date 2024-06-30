@extends('layouts.app')

@section('title')
    Dashboard
@endsection

@section('title-header')
    <div class='mb-1'>
        Dashboard
    </div>
@endsection

@section('content')
    <div class="container mt-4">
        <!-- Summary Row -->
        <div class="row mb-4">
            <!-- Total Quantity in Stock Card -->
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <h5 class="card-title"><strong>Number of Products</strong></h5>
                        <p class="card-text display-4">{{ $totalQuantityInStock }}</p>
                    </div>
                </div>
            </div>

            <!-- Products Out of Stock Card -->
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <h5 class="card-title"><strong>Products Out of Stock</strong></h5>
                        <p class="card-text display-4">{{ $totalLowInStockCount }}</p>
                    </div>
                </div>
            </div>
            <!-- Total Sales Card -->
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <h5 class="card-title"><strong>Total Sales</strong></h5>
                        <p class="card-text display-4">{{ number_format($totalSales, 2) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row mb-4">
            <!-- Monthly Sales Bar Chart -->
            <div class="col-md-6">
                <div class="card mcard shadow-sm mb-4">
                    <div class="card-body text-center">
                        <h5 class="card-title"><strong>Monthly Sales Distribution</strong></h5>
                        <div class="chart-container">
                            <canvas id="monthlySalesChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status Pie Chart -->
            <div class="col-md-6 d-flex justify-content-center">
                <div class="card mcard shadow-sm mb-4">
                    <div class="card-body text-center">
                        <h5 class="card-title"><strong>Order Status Distribution</strong></h5>
                        <div class="chart-container">
                            <canvas id="statusPieChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .card-title {
            font-weight: bold;
            font-size: 1.2rem;
        }

        .card-text.display-4 {
            font-size: 2.5rem;
            font-weight: 300;
        }

        .shadow-sm {
            box-shadow: 0 .125rem .25rem rgba(0, 0, 0, .075) !important;
        }

        .chart-container {
            width: 100%;
            height: 350px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .mcard {
            width: 100%;
            max-width: 700px;
            margin: 0 auto;
        }

        .lnscard {
            width: 250px;
        }
    </style>
@endsection

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Monthly Sales Data
        const monthlySalesData = @json($monthlySalesData);
        const labels = monthlySalesData.map(item => `${item.product_name} (${item.month})`);
        const data = monthlySalesData.map(item => item.total_sales);

        const ctx = document.getElementById('monthlySalesChart').getContext('2d');
        const monthlySalesChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Monthly Sales',
                    data: data,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                        'rgba(255, 159, 64, 0.2)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Order Status Data
        const statusData = @json($statusData); // Ensure this data is passed from the controller
        const statusLabels = Object.keys(statusData);
        const statusCounts = Object.values(statusData);

        const ctxPie = document.getElementById('statusPieChart').getContext('2d');
        const statusPieChart = new Chart(ctxPie, {
            type: 'pie',
            data: {
                labels: statusLabels,
                datasets: [{
                    label: 'Order Status',
                    data: statusCounts,
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(255, 159, 64, 0.2)'
                    ],
                    borderColor: [
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(255, 99, 132, 1)',
                        'rgba(255, 159, 64, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true
            }
        });
    });
</script>
