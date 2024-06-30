<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Role;
use App\Models\User;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;

class UserController extends Controller
{
    public function index(): Response
    {
        $users = User::with('products', 'orders')->paginate(10);

        return response()
            ->view('dashboard.user.index', compact('users'));
    }

    public function create(): Response
    {
        $products = Product::all();
        $customers = Customer::all();
        $user = auth()->user();
            // Filter roles based on their IDs
        $roles = Role::whereIn('id', [1, 2, 3])->get(); // Assuming 1 is for STAFF and 2 is for ADMIN

        return response()
            ->view('dashboard.user.create', compact('roles', 'products', 'customers', 'user'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
            'phone_number' => 'nullable|string|max:255|unique:users,phone_number',
            'hub_name' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
        ]);
    
        // Custom validation for role_id
        if (!in_array($request->role_id, [1, 2, 3])) {
            return redirect()
                ->back()
                ->withErrors(['role_id' => 'The selected role is not valid. Please select either User or Admin.'])
                ->withInput();
        }
    
        // Check if phone number already exists
        if (User::where('phone_number', $request->phone_number)->exists()) {
            return redirect()
                ->back()
                ->withErrors(['phone_number' => 'This phone number is already taken.'])
                ->withInput();
        }
    
        // Create the user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
            'phone_number' => $request->phone_number,
            'hub_name' => $request->hub_name,
            'address' => $request->address,
        ]);
    
        // If role_id is 3 (HUB), create a corresponding customer record
        if ($request->role_id == 3) {
            \App\Models\Customer::create([
                'customer_name' => $user->name,
                'customer_phone_number' => $user->phone_number,
                'customer_email' => $user->email,
                'hub_name' => $user->hub_name,
                'address' => $user->address,
                'date' => now(),
            ]);
        }
    
        return redirect()
            ->route('dashboard.users.index')
            ->with('success', 'User successfully created.');
    }
    
    public function show(User $user)
    {
        return response()
            ->view('dashboard.user.show', compact('user'));
    }

    public function edit(User $user): Response
    {
        $this->authorize('user-edit-update', $user);
        // Fetch the current user's roles
        $roles = Role::all(); // Adjust this query as needed based on your application's logic

        return response()
            ->view('dashboard.user.edit', compact('user', 'roles')); // Pass the roles to the view
    }

    public function update(Request $request, User $user)
{
    $request->validate([
        'name' => 'required',
        'email' => 'required|email|unique:users,email,' . $user->id,
        'password' => 'nullable|min:8|confirmed',
        'role_id' => 'required|exists:roles,id',
        'phone_number' => 'nullable|string|max:255|unique:users,phone_number,' . $user->id,
        'hub_name' => 'nullable|string|max:255',
        'address' => 'nullable|string|max:255',
    ]);

    if (!in_array($request->role_id, [1, 2, 3])) {
        return redirect()
            ->back()
            ->withErrors(['role_id' => 'The selected role is not valid. Please select either User or Admin.'])
            ->withInput();
    }

    $user->update([
        'name' => $request->name,
        'email' => $request->email,
        'password' => $request->password ? Hash::make($request->password) : $user->password,
        'role_id' => $request->role_id,
        'phone_number' => $request->phone_number,
        'hub_name' => $request->hub_name,
        'address' => $request->address,
    ]);

    if ($request->role_id == 3) {
        \App\Models\Customer::updateOrCreate(
            [
                'customer_email' => $user->email
            ],
            [
                'customer_name' => $user->name,
                'customer_phone_number' => $user->phone_number,
                'hub_name' => $user->hub_name,
                'address' => $user->address,
                'date' => now(),
            ]
        );
    } 
    return redirect()
        ->route('dashboard.users.index')
        ->with('success', 'User successfully updated.');
}

    public function destroy(Request $request, User $user): RedirectResponse
    {
        $this->authorize('user-destroy', $user);


        $user->delete();
        return redirect()
            ->route('dashboard.users.index')
            ->with('success', 'User successfully deleted.');
    }
}
