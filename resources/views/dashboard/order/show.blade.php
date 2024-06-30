@extends('layouts.app')

@section('title')
    Order Detail
@endsection

@section('title-header')
    <div class='1'>
        Customer's Order Details
    </div>
@endsection

@section('content')
    @foreach ($order->orderItems as $orderItem)
        <div>
            <!-- Display order item details here -->
            <p>Product: {{ $orderItem->product->name }}</p>
            <p>Quantity: {{ $orderItem->quantity }}</p>
            <p>Unit Price: {{ $orderItem->unit_price }}</p>
            <!-- Add more details as needed -->
        </div>
    @endforeach
@endsection
