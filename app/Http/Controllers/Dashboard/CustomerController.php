<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;


class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::all();
        return view('dashboard.customer.index', compact('customers'));
    }

    public function create()
    {
        return view('dashboard.customer.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'customer_name' => 'required|max:255',
            'customer_email' => 'required|email|unique:customers',
            'customer_phone_number' => 'nullable|max:20',
            'hub_name' => 'nullable|max:255',
            'address' => 'nullable',
            'date' => 'nullable|date',
        ]);

        $existingCustomer = Customer::where('customer_email', $validatedData['customer_email'])->first();

        if ($existingCustomer) {
            return redirect()->back()->with('error', 'Customer already exists!');
        }

        Customer::create($validatedData);
        return redirect()->route('dashboard.orders.index')->with('success', 'Customer created successfully!');
    }

    public function show(string $id)
    {
        $customer = Customer::findOrFail($id);
        return response()->view('dashboard.customer.show', compact('customer'));
    }

    public function edit(string $id)
    {
        $customer = Customer::findOrFail($id);
        return view('dashboard.customer.edit', compact('customer'));
    }

    public function update(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email|unique:customers,customer_email,' . $id,
            // 'customer_email' => 'nullable|email|unique:customer_email,'. $id,
            'customer_phone_number' => 'required|nullable|max:20',
            'hub_name' => 'nullable|string|max:255',
            'address' => 'required|string|max:255',
            'date' => 'nullable|date',
        ]);
    
        $customer = Customer::findOrFail($id);
        $customer->update($validatedData);
    
        return redirect()->route('dashboard.customers.index')->with('success', 'Customer updated successfully.');
    }

    public function destroy(string $id)
    {
        try {
        $customer = Customer::findOrFail($id);
        $customer->delete();

            return redirect()->route('dashboard.orders.index')->with('success', 'Customer deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('dashboard.orders.index')->with('error', 'Failed to delete customer. ' . $e->getMessage());
        }
    }

    public function yourMethodName(Request $request)
    {
        $customer = Customer::findOrFail($request->customer_id);

        return view('your.view.name', compact('customer'));
    }
}
