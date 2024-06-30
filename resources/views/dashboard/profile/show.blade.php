<!-- resources/views/profile.blade.php -->
@extends('layouts.app')

@section('title')
    Profile
@endsection

@section('title-header')
    <div class='mb-1'>
        Profile
    </div>
@endsection

@section('content')
    <div class="container">
        @if (session()->has('success'))
            <div class="alert alert-success" role="alert">
                {{ session('success') }}
            </div>
        @endif
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">Name</th>
                    <th scope="col">Email</th>
                    <th scope="col">Phone Number</th>
                    @if ($user->role_id == 3)
                        <th scope="col">Hub Name</th>
                    @else
                        <td></td> <!-- Or any other placeholder you prefer -->
                    @endif
                    <th scope="col">Address</th>
                    <th scope="col">Role</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->phone_number }}</td>
                    <td>{{ $user->hub_name }}</td>
                    <td>{{ $user->address }}</td>
                    <td>{{ $roleName }}</td>
                    <!-- Add more cells as needed -->
                    @if (auth()->user()->role_id === 3)
                        <td>
                            <form id="add-customer-form" action="{{ route('dashboard.customers.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="customer_name" value="{{ $user->name }}">
                                <input type="hidden" name="customer_email" value="{{ $user->email }}">
                                <input type="hidden" name="customer_phone_number" value="{{ $user->phone_number }}">
                                <input type="hidden" name="hub_name" value="{{ $user->hub_name }}">
                                <input type="hidden" name="address" value="{{ $user->address }}">
                                <input type="hidden" name="date" value="{{ now()->format('Y-m-d') }}">
                            </form>
                        </td>
                    @endif
                </tr>
            </tbody>
        </table>
    </div>
@endsection
