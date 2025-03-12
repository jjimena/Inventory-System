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

        <div class="form-group mt-2">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <form id="previewForm" action="{{ route('dashboard.reports.generate') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <label for="type">Select a Report Type:</label>
                                <form method="GET" action="{{ route('dashboard.reports.generate') }}">
                                    <select name="type" id="type" class="form-control"
                                        onchange="this.form.submit()">
                                        <option value="daily" {{ $type === 'daily' ? 'selected' : '' }}>Daily Sales
                                        </option>
                                        <option value="monthly" {{ $type === 'monthly' ? 'selected' : '' }}>Monthly Sales
                                        </option>
                                        <option value="yearly" {{ $type === 'yearly' ? 'selected' : '' }}>Annual Sales
                                        </option>
                                    </select>
                                    <input type="hidden" name="date" value="{{ $date }}">
                                    <input type="hidden" name="month" value="{{ $month }}">
                                    <input type="hidden" name="year" value="{{ $year }}">
                                </form>
                            </div>
                            @if ($type === 'daily')
                                <div class="col-md-6">
                                    <label for="date">Select Date:</label>
                                    <input type="date" name="date" id="date" class="form-control"
                                        value="{{ $date ?? now()->toDateString() }}" onchange="this.form.submit()">
                                </div>
                            @elseif ($type === 'monthly')
                                <div class="col-md-3">
                                    <label for="year">Select Year:</label>
                                    <select name="year" id="year" class="form-control"
                                        onchange="this.form.submit()">
                                        @foreach (range(now()->year, now()->year - 10) as $y)
                                            <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>
                                                {{ $y }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="month">Select Month:</label>
                                    <select name="month" id="month" class="form-control"
                                        onchange="this.form.submit()">
                                        @foreach (range(1, 12) as $m)
                                            <option value="{{ $m }}" {{ $m == $month ? 'selected' : '' }}>
                                                {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @elseif ($type === 'yearly')
                                <div class="col-md-6">
                                    <label for="year">Select Year:</label>
                                    <select name="year" id="year" class="form-control"
                                        onchange="this.form.submit()">
                                        @foreach (range(now()->year, now()->year - 10) as $y)
                                            <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>
                                                {{ $y }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                        </div>
                    </form>

                </div>
            </div>

            <!-- Buttons -->
            <div class="mt-3">
                <button type="button" class="btn btn-primary" onclick="submitPreview()">Preview Report</button>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-labelledby="previewModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="previewModalLabel">Report Preview</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="reportContent" class="border p-3"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" onclick="submitDownload()">Download PDF</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    @if ($type === 'yearly')
        <div class="mt-3">
            <table class="table">
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th class="text-end">Total Order</th>
                        <th class="text-end">Unit Price</th>
                        <th class="text-end">Total Price</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($productSalesData as $product)
                        <tr>
                            <td>{{ $product->product_name }}</td>
                            <td class="text-end">{{ $product->total_quantity }}</td>
                            <td class="text-end">{{ number_format($product->unit_price, 2) }}</td>
                            <td class="text-end">{{ number_format($product->total_price, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-end"><strong>Total:</strong></td>
                        <td class="text-end">
                            <strong>{{ number_format($productSalesData->sum('total_price'), 2) }}</strong>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    @elseif ($type === 'monthly')
        <div class="mt-3">
            <table class="table">
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th class="text-end">Total Order</th>
                        <th class="text-end">Unit Price</th>
                        <th class="text-end">Total Price</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($productSalesData as $product)
                        <tr>
                            <td>{{ $product->product_name }}</td>
                            <td class="text-end">{{ $product->total_quantity }}</td>
                            <td class="text-end">{{ number_format($product->unit_price, 2) }}</td>
                            <td class="text-end">{{ number_format($product->total_price, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-end"><strong>Total:</strong></td>
                        <td class="text-end">
                            <strong>{{ number_format($productSalesData->sum('total_price'), 2) }}</strong>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    @elseif ($type === 'daily')
        <div class="mt-3">
            <table class="table">
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th class="text-end">Total Order</th>
                        <th class="text-end">Unit Price</th>
                        <th class="text-end">Total Price</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($productSalesData as $product)
                        <tr>
                            <td>{{ $product->product_name }}</td>
                            <td class="text-end">{{ $product->total_quantity }}</td>
                            <td class="text-end">{{ number_format($product->unit_price, 2) }}</td>
                            <td class="text-end">{{ number_format($product->total_price, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-end"><strong>Total:</strong></td>
                        <td class="text-end">
                            <strong>{{ number_format($productSalesData->sum('total_price'), 2) }}</strong>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    @endif

    <script>
        // This function ensures that the table content updates dynamically after a form change
        function submitPreview() {
            const form = document.getElementById('previewForm');
            const formData = new FormData(form);

            fetch('{{ route('dashboard.reports.report_preview') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: formData,
                })
                .then(response => response.text())
                .then(html => {
                    document.getElementById('reportContent').innerHTML = html;
                    const modal = new bootstrap.Modal(document.getElementById('previewModal'));
                    modal.show(); // Show the modal with updated content
                })
                .catch(error => console.error('Error fetching preview:', error));
        }
        document.getElementById('previewForm').addEventListener('submit', function() {
            // Display a loading spinner or message
            document.body.classList.add('loading');
        });


        // This function submits a form for downloading the report as a PDF
        function submitDownload() {
            const form = document.createElement('form');
            form.action = '{{ route('dashboard.reports.generate') }}';
            form.method = 'POST';

            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken;
            form.appendChild(csrfInput);

            const inputs = [{
                name: 'type',
                value: '{{ $type }}'
            }, {
                name: 'year',
                value: '{{ request('year', now()->year) }}'
            }, {
                name: 'month',
                value: '{{ request('month', now()->month) }}'
            }, {
                name: 'download',
                value: 'pdf'
            }];

            @if ($type === 'daily')
                inputs.push({
                    name: 'date',
                    value: '{{ request('date', now()->toDateString()) }}'
                });
            @endif

            inputs.forEach(input => {
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = input.name;
                hiddenInput.value = input.value;
                form.appendChild(hiddenInput);
            });

            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);
        }
    </script>

@endsection
