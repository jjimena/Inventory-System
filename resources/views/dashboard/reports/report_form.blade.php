<!-- resources/views/reports/report_form.blade.php -->

@extends('layouts.app')

@section('title', 'Generate Reports')

@section('content')
    <div class="container">
        <h1>Generate Sales Reports</h1>
        <form action="{{ route('dashboard.generate.report') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="type">Select Report Type:</label>
                <select name="type" id="type" class="form-control">
                    <option value="monthly">Monthly Sales Report</option>
                    <option value="yearly">Annual Sales Report</option>
                </select>
            </div>
            <div class="form-group">
                <label for="download">Download as PDF:</label>
                <input type="checkbox" name="download" value="pdf">
            </div>
            <button type="submit" class="btn btn-primary">Generate Report</button>
        </form>
    </div>
@endsection
