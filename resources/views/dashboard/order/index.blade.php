@extends('layouts.app')

@section('title')
    Orders
@endsection

@section('title-header')
    <div class='mb-1'>
        Customer
    </div>
@endsection

@section('content')
    <div class="d-flex justify-content-between mb-4">
        <div>
            <a href="{{ route('dashboard.customers.create') }}" class="btn btn-primary">Create New Customer</a>
        </div>
    </div>

    {{-- Alert Success --}}
    @if (session('success'))
        <div class="alert alert-success mb-4" role="alert">
            {{ session('success') }}
        </div>
    @endif
    {{-- End Of Alert Success --}}

    {{-- Table --}}
    <div class="table-responsive mt-4">
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Customer Name</th>
                    <th>Customer Email</th>
                    <th>Phone Number</th>
                    <th>Address</th>
                    <th>Hub Name</th>
                    <th>Total Products Ordered</th>
                    <th>Total Purchased</th>
                    <th>Actions</th> <!-- Add this header -->
                </tr>
            </thead>
            <tbody>
                @foreach ($customers as $customer)
                    <tr>
                        <th scope="row">{{ $loop->iteration }}</th>
                        <td>{{ $customer['customer_name'] }}</td>
                        <td>{{ $customer['customer_email'] }}</td>
                        <td>{{ $customer['customer_phone_number'] }}</td>
                        <td>{{ $customer['address'] }}</td>
                        <td>{{ $customer['hub_name'] }}</td>
                        <td class="text-center">{{ $customer['total_quantity'] }}</td>
                        <td class="price-column">₱ {{ number_format($customer['total_price'], 2) }}</td>
                        <td>
                            <a href="{{ route('dashboard.customers.edit', $customer['id']) }}"
                                class="btn btn-sm btn-primary">Manage</a>
                            <form action="{{ route('dashboard.customers.destroy', $customer['id']) }}" method="POST"
                                style="display: inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger"
                                    onclick="return confirm('Are you sure you want to delete this customer?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-3">
        {{ $customers->links() }}
    </div>

    <script>
        document.getElementById('generate-report').addEventListener('click', function(e) {
            e.preventDefault(); // Prevent the default form submission

            var reportType = document.getElementById('report-type').value; // Get the selected report type

            fetch('{{ route('dashboard.customers.create') }}', { // Assuming you have a named route for this action
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}' // Include CSRF token for security
                    },
                    body: JSON.stringify({
                        type: reportType
                    }) // Send the report type as JSON
                })
                .then(response => response.blob()) // Convert the response to a Blob object
                .then(blob => {
                    var url = window.URL.createObjectURL(blob);
                    var a = document.createElement('a');
                    a.href = url;
                    a.download = `customers_report_${reportType}.pdf`; // Set the download filename
                    a.click(); // Trigger the download
                })
                .catch(error => console.error('Error:', error));
        });
    </script>
@endsection
