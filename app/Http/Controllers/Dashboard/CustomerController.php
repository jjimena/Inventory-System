<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;

class CustomerController extends Controller
{
    public function index()
    {
        $customerTypes = Customer::select('customer_type')->distinct()->pluck('customer_type');
        $customers = Customer::orderBy('created_at', 'desc')->paginate(10); 
        return view('dashboard.customer.index', compact('customers'));
    }

    public function create()
    {
        $customerTypes = ['Walk-in', 'Wholesale']; // Define available customer types
        return view('dashboard.customer.create', compact('customerTypes'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'customer_name' => 'required|max:255',
            'customer_email' => 'required|email|unique:customers',
            'customer_phone_number' => 'nullable|max:20',
            'address' => 'nullable|string|max:255',
            'date' => 'nullable|date',
            'customer_type' => 'required|in:Walk-in,Wholesale',
        ]);

        Customer::create($validatedData);
        return redirect()->route('dashboard.orders.index')->with('success', 'Customer created successfully!');
    }

    public function show($id)
    {
        $customer = Customer::findOrFail($id);
        return view('dashboard.customer.show', compact('customer'));
    }

    public function edit($id)
    {
        $customer = Customer::findOrFail($id);
        $customerTypes = ['Walk-in', 'Wholesale'];
        return view('dashboard.customer.edit', compact('customer', 'customerTypes'));
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email|unique:customers,customer_email,' . $id,
            'customer_phone_number' => 'nullable|max:20',
            'address' => 'nullable|string|max:255',
            'date' => 'nullable|date',
            'customer_type' => 'required|in:Walk-in,Wholesale',
        ]);

        $customer = Customer::findOrFail($id);
        $customer->update($validatedData);

        return redirect()->route('dashboard.orders.index')->with('success', 'Customer updated successfully.');
    }

    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();

        return redirect()->route('dashboard.orders.index')->with('success', 'Customer deleted successfully.');
    }

    // Fetch customers by type
    public function getCustomersByType($type)
    {
        $customers = Customer::where('customer_type', $type)->get(['id', 'customer_name']);
        return response()->json($customers);
    }

    public function search(Request $request)
    {
        $query = $request->input('query');

        if (!$query) {
            return response()->json([], 400);
        }

        $customers = Customer::where('customer_name', 'like', "%$query%")
            ->orderBy('customer_name')
            ->get(['id', 'customer_name']);

        return response()->json($customers);
    }
}
