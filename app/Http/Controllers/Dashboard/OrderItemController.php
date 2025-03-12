<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Role;
use App\Models\Customer;
use PDF;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Session;
use App\Models\{Order, OrderItem, Product};
use Illuminate\Validation\ValidationException;
use Illuminate\Http\{Response, RedirectResponse};
use App\Http\Requests\{OrderItemStoreRequest, OrderItemUpdateRequest};

class OrderItemController extends Controller
{
    public function index(Request $request): Response
    {
        $user = auth()->user();
        $sortField = $request->get('sort', 'id'); // Default sort by id
        $sortOrder = $request->get('order', 'asc'); // Default order ascending
    
        if ($sortField === 'customer_name') {
            $sortField = 'customers.customer_name';
        } elseif ($sortField === 'product_name') {
            $sortField = 'products.name';
        }
    
        $orderItemsQuery = OrderItem::with(['customer', 'product'])
            ->join('customers', 'order_items.customer_id', '=', 'customers.id')
            ->join('products', 'order_items.product_id', '=', 'products.id');
    
        if ($user->role_id === 3) { // Assuming 3 is the role_id for HUB
            $orderItemsQuery->where('order_items.user_id', $user->id);
        }
    
        $orderItems = $orderItemsQuery
            ->orderBy($sortField, $sortOrder)
            ->select('order_items.*') // Ensure only order_items columns are selected
            ->paginate(10)
            ->appends(['sort' => $sortField, 'order' => $sortOrder]); // Append sort parameters
    
        return response()
            ->view('dashboard.order-item.index', compact('orderItems', 'sortField', 'sortOrder'));
    }
 
    public function create(): Response
    {
        $products = Product::orderBy('name', 'asc')->get();
        $customerTypes = Customer::select('customer_type')->distinct()->pluck('customer_type');
        $customers = Customer::all(); // Fetch all customers
        $user = Auth::user(); // Retrieve the currently authenticated user

        return response()->view('dashboard.order-item.create', compact('customers', 'products', 'user', 'customerTypes'));
    }

    // public function store(Request $request)
    // {
    //     DB::beginTransaction();
    
    //     try {
    //         $validatedData = $request->validate([
    //             'quantity' => 'required|integer|min:1',
    //             'product_id' => 'required|exists:products,id',
    //             'customer_id' => 'required|exists:customers,id',
    //             'customer_type' => 'required|string', // Ensure customer_type is received
    //         ]);
    
    //         // If the user has a specific role, handle customer creation logic
    //         if ($request->user()->role_id === 3) {
    //             $customer = \App\Models\Customer::firstOrCreate(
    //                 [
    //                     'customer_name' => $request->user()->name,
    //                     'customer_phone_number' => $request->user()->phone_number,
    //                     'hub_name' => $request->user()->hub_name,
    //                     'address' => $request->user()->address,
    //                 ]
    //             );
    
    //             $validatedData['customer_id'] = $customer->id;
    //         }
    
    //         $product = Product::findOrFail($validatedData['product_id']);
    
    //         // Ensure there's enough stock
    //         if ($product->quantity_in_stock < $validatedData['quantity']) {
    //             return redirect()->back()->withErrors(['quantity' => 'Not enough stock to fulfill the order.'])->withInput();
    //         }
    
    //         // Determine unit price based on customer type
    //         $unitPrice = $product->price;
    
    //         if ($validatedData['customer_type'] === "Wholesale") {
    //             if ($unitPrice >= 1000) {
    //                 $unitPrice -= 60;
    //             } elseif ($unitPrice > 200 && $unitPrice < 1000) {
    //                 $unitPrice -= 40;
    //             }
    //             // If price ≤ 200, no deduction (unitPrice remains the same)
    //         }
    
    //         // Create OrderItem
    //         $orderItem = OrderItem::create([
    //             'quantity' => $validatedData['quantity'],
    //             'unit_price' => $unitPrice,
    //             'product_id' => $product->id,
    //             'customer_id' => $validatedData['customer_id'],
    //             'user_id' => auth()->id(),
    //             'status' => 'cancel', // Pending until payment is confirmed
    //         ]);
    
    //         DB::commit();
    
    //         // Calculate total price after discount
    //         $totalPrice = $orderItem->quantity * $unitPrice;
    
    //         // Redirect to the payment form
    //         return view('dashboard.order-item.payment', compact('orderItem', 'totalPrice'));
    
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return redirect()->back()->with('error', 'Failed to create OrderItem. ' . $e->getMessage());
    //     }
    // }

//     public function store(Request $request)
// {
//     DB::beginTransaction();

//     try {
//         $validatedData = $request->validate([
//             'quantity' => 'required|integer|min:1',
//             'product_id' => 'required|exists:products,id',
//             'customer_id' => 'required|exists:customers,id',
//             'customer_type' => 'required|string|in:Retail,Wholesale',
//         ]);

//         // Handle customer creation if user has role_id = 3
//         if ($request->user()->role_id === 3) {
//             $customer = Customer::firstOrCreate(
//                 [
//                     'customer_name' => $request->user()->name,
//                     'customer_phone_number' => $request->user()->phone_number,
//                     'hub_name' => $request->user()->hub_name,
//                     'address' => $request->user()->address,
//                     'customer_type' => $validatedData['customer_type'], // Ensure type matches
//                 ]
//             );

//             $validatedData['customer_id'] = $customer->id;
//         } else {
//             // Ensure the selected customer belongs to the selected type
//             $customer = Customer::where('id', $validatedData['customer_id'])
//                 ->where('customer_type', $validatedData['customer_type'])
//                 ->first();

//             if (!$customer) {
//                 return redirect()->back()->withErrors(['customer_id' => 'Selected buyer does not match the chosen customer type.'])->withInput();
//             }
//         }

//         $product = Product::findOrFail($validatedData['product_id']);

//         // Ensure there's enough stock
//         if ($product->quantity_in_stock < $validatedData['quantity']) {
//             return redirect()->back()->withErrors(['quantity' => 'Not enough stock to fulfill the order.'])->withInput();
//         }

//         // Determine unit price based on customer type
//         $unitPrice = $product->price;

//         if ($validatedData['customer_type'] === "Wholesale") {
//             if ($unitPrice >= 1000) {
//                 $unitPrice -= 60;
//             } elseif ($unitPrice > 200 && $unitPrice < 1000) {
//                 $unitPrice -= 40;
//             }
//         }

//         // Create OrderItem
//         $orderItem = OrderItem::create([
//             'quantity' => $validatedData['quantity'],
//             'unit_price' => $unitPrice,
//             'product_id' => $product->id,
//             'customer_id' => $validatedData['customer_id'],
//             'user_id' => auth()->id(),
//             'status' => 'cancel', // Pending until payment is confirmed
//         ]);

//         DB::commit();

//         // Calculate total price after discount
//         $totalPrice = $orderItem->quantity * $unitPrice;

//         // Redirect to the payment form
//         return view('dashboard.order-item.payment', compact('orderItem', 'totalPrice'));

//     } catch (\Exception $e) {
//         DB::rollBack();
//         return redirect()->back()->with('error', 'Failed to create OrderItem. ' . $e->getMessage());
//     }
// }

// public function store(Request $request)
// {
//     DB::beginTransaction();

//     try {
//         $validatedData = $request->validate([
//             'quantity' => 'required|integer|min:1',
//             'product_id' => 'required|exists:products,id',
//             'customer_type' => 'required|string|in:Retail,Wholesale,Walk-in',
//             'customer_id' => 'nullable|exists:customers,id', // Make customer_id optional
//         ]);

//         // Fetch or create customer based on type
//         if ($validatedData['customer_type'] === 'Walk-in') {
//             // Check if a generic "Walk-in Customer" exists, otherwise create one
//             $customer = Customer::firstOrCreate(
//                 ['customer_name' => 'Walk-in Customer'],
//                 [
//                     'customer_phone_number' => 'N/A',
//                     'hub_name' => 'N/A',
//                     'address' => 'N/A',
//                     'customer_type' => 'Walk-in',
//                 ]
//             );
//             $validatedData['customer_id'] = $customer->id;
//         } else {
//             // Ensure a valid customer is selected for Retail/Wholesale orders
//             $customer = Customer::where('id', $validatedData['customer_id'])
//                 ->where('customer_type', $validatedData['customer_type'])
//                 ->first();

//             if (!$customer) {
//                 return redirect()->back()->withErrors(['customer_id' => 'Selected buyer does not match the chosen customer type.'])->withInput();
//             }
//         }

//         $product = Product::findOrFail($validatedData['product_id']);

//         // Ensure there's enough stock
//         if ($product->quantity_in_stock < $validatedData['quantity']) {
//             return redirect()->back()->withErrors(['quantity' => 'Not enough stock to fulfill the order.'])->withInput();
//         }

//         // Determine unit price based on customer type
//         $unitPrice = $product->price;

//         if ($validatedData['customer_type'] === "Wholesale") {
//             if ($unitPrice >= 1000) {
//                 $unitPrice -= 60;
//             } elseif ($unitPrice > 200 && $unitPrice < 1000) {
//                 $unitPrice -= 40;
//             }
//         }

//         // Create OrderItem
//         $orderItem = OrderItem::create([
//             'quantity' => $validatedData['quantity'],
//             'unit_price' => $unitPrice,
//             'product_id' => $product->id,
//             'customer_id' => $validatedData['customer_id'],
//             'user_id' => auth()->id(),
//             'status' => 'cancel', // Pending until payment is confirmed
//         ]);

//         DB::commit();

//         // Calculate total price after discount
//         $totalPrice = $orderItem->quantity * $unitPrice;

//         // Redirect to the payment form
//         return view('dashboard.order-item.payment', compact('orderItem', 'totalPrice'));

//     } catch (\Exception $e) {
//         DB::rollBack();
//         return redirect()->back()->with('error', 'Failed to create OrderItem. ' . $e->getMessage());
//     }
// }

public function store(Request $request)
{
    DB::beginTransaction();

    try {
        $validatedData = $request->validate([
            'quantity' => 'required|integer|min:1',
            'product_id' => 'required|exists:products,id',
            'customer_type' => 'required|string|in:Retail,Wholesale,Walk-in',
            'customer_id' => 'required|exists:customers,id', // Ensure customer_id is required
        ]);

        // Ensure the selected customer matches the selected customer type
        $customer = Customer::where('id', $validatedData['customer_id'])
            ->where('customer_type', $validatedData['customer_type'])
            ->first();

        if (!$customer) {
            return redirect()->back()->withErrors(['customer_id' => 'Selected buyer does not match the chosen customer type.'])->withInput();
        }

        $product = Product::findOrFail($validatedData['product_id']);

        // Ensure there's enough stock
        if ($product->quantity_in_stock < $validatedData['quantity']) {
            return redirect()->back()->withErrors(['quantity' => 'Not enough stock to fulfill the order.'])->withInput();
        }

        // Determine unit price based on customer type
        $unitPrice = $product->price;

        if ($validatedData['customer_type'] === "Wholesale") {
            if ($unitPrice >= 1000) {
                $unitPrice -= 60;
            } elseif ($unitPrice > 200 && $unitPrice < 1000) {
                $unitPrice -= 40;
            }
        }

        // Create OrderItem
        $orderItem = OrderItem::create([
            'quantity' => $validatedData['quantity'],
            'unit_price' => $unitPrice,
            'product_id' => $product->id,
            'customer_id' => $customer->id,
            'user_id' => auth()->id(),
            'status' => 'cancel', // Pending until payment is confirmed
        ]);

        DB::commit();

        // Calculate total price after discount
        $totalPrice = $orderItem->quantity * $unitPrice;

        // Redirect to the payment form
        return view('dashboard.order-item.payment', compact('orderItem', 'totalPrice'));

    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with('error', 'Failed to create OrderItem. ' . $e->getMessage());
    }
}




    // public function store(Request $request)
    // {
    //     DB::beginTransaction();
    
    //     try {
    //         $validatedData = $request->validate([
    //             'quantity' => 'required|integer|min:1',
    //             'product_id' => 'required|exists:products,id',
    //             'customer_id' => 'required|exists:customers,id',
    //             'customer_type' => 'required|string|in:Retail,Wholesale', // Ensure valid customer type
    //         ]);
    
    //         // Fetch customer and verify the type
    //         $customer = Customer::where('id', $validatedData['customer_id'])
    //             ->where('customer_type', $validatedData['customer_type'])
    //             ->first();
    
    //         if (!$customer) {
    //             return redirect()->back()->withErrors(['customer_id' => 'Selected buyer does not match the chosen customer type.'])->withInput();
    //         }
    
    //         $product = Product::findOrFail($validatedData['product_id']);
    
    //         // Ensure there's enough stock
    //         if ($product->quantity_in_stock < $validatedData['quantity']) {
    //             return redirect()->back()->withErrors(['quantity' => 'Not enough stock to fulfill the order.'])->withInput();
    //         }
    
    //         // Determine unit price based on customer type
    //         $unitPrice = $product->price;
    
    //         if ($validatedData['customer_type'] === "Wholesale") {
    //             if ($unitPrice >= 1000) {
    //                 $unitPrice -= 60;
    //             } elseif ($unitPrice > 200 && $unitPrice < 1000) {
    //                 $unitPrice -= 40;
    //             }
    //         }
    
    //         // Create OrderItem
    //         $orderItem = OrderItem::create([
    //             'quantity' => $validatedData['quantity'],
    //             'unit_price' => $unitPrice,
    //             'product_id' => $product->id,
    //             'customer_id' => $customer->id,
    //             'user_id' => auth()->id(),
    //             'status' => 'cancel', // Pending until payment is confirmed
    //         ]);
    
    //         DB::commit();
    
    //         // Redirect to the payment form with success message
    //         return redirect()->route('dashboard.order-item.payment', ['orderItemId' => $orderItem->id])
    //             ->with('success', 'Order item created successfully!');
    
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return redirect()->back()->with('error', 'Failed to create OrderItem. ' . $e->getMessage());
    //     }
    // }
    

    
    public function show($customerId)
    {
        // Fetch the customer details
        $customer = Customer::findOrFail($customerId);

        // Fetch all orders for this specific customer
        $orderItems = OrderItem::with('product')
            ->where('customer_id', $customerId)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

            // Calculate total
        $totalSpent = $orderItems->sum(function ($order) {
            return $order->quantity * $order->unit_price;
        });
        // Calculate the total spent for all order items (excluding canceled orders)
        $totalSpent = $customer->orderItems()
            ->where('status', '!=', 'cancel') // Exclude canceled orders
            ->get()
            ->sum(function ($orderItem) {
                return $orderItem->quantity * $orderItem->unit_price;
            });

        // Return the view with customer and order data
        return view('dashboard.order-item.show', compact('customer', 'orderItems', 'totalSpent'));
    }

    public function edit(OrderItem $orderItem): Response
    {
        return response()
            ->view('dashboard.order-item.edit', compact('orderItem'));
    }

    public function update(OrderItemUpdateRequest $request, OrderItem $orderItem): RedirectResponse
    {
        DB::beginTransaction();

        try {
            $order = Order::find($orderItem->order_id);
            $product = Product::find($orderItem->product_id);
            $product->quantity_in_stock -= $quantity;
            $product->save();

            $total_price_current_order_item = $orderItem->quantity * $orderItem->unit_price;
            $total_price_product = $product->price * $request->input('quantity');
            $total_price = $order->total_price - $total_price_current_order_item;
            $order->total_price = $total_price + $total_price_product;
            $order->save();

            $orderItem->quantity = $request->input('quantity');
            $orderItem->save();

            DB::commit();

            return redirect()
                ->route('dashboard.order-items.index')
                ->with('success', 'OrderItem Successfully updated.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->route('dashboard.order-items.index')
                ->with('error', 'Failed to update OrderItem.');
        }
    }

    public function destroy(OrderItem $orderItem)
    {
        DB::beginTransaction();

        try {
            $total_price_order_item = $orderItem->quantity * $orderItem->unit_price;

            $order = Order::find($orderItem->order_id);
            $order->total_price = $order->total_price - $total_price_order_item;
            $order->save();

            $product = Product::find($orderItem->product_id);
            $product->quantity_in_stock = $orderItem->quantity + $product->quantity_in_stock;
            $product->save();

            $orderItem->delete();

            DB::commit();

            return redirect()
                ->route('dashboard.order-items.index')
                ->with('success', 'OrderItem successfully deleted.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->with('error', 'Failed to delete OrderItem.');
        }
    }

    public function approveOrder(Request $request, $orderItemId)
    {
        $user = Auth::user();

        $orderItem = OrderItem::findOrFail($orderItemId);

        if ($orderItem->status === 'paid' ||  $orderItem->status === 'Cash On Delivery' || $orderItem->status === 'cod' && ($user->role_id !== 3 && $user->role_id !== 'HUB')) {
            $orderItem->status = 'approved';
            $orderItem->save();
            
            return redirect()->route('dashboard.order-items.index')->with('success', 'Order approved successfully!');
        }

        return redirect()->back()->with('error', 'Order cannot be approved.');
    }

    public function reject(Request $request, $orderItemId): RedirectResponse
    {
        DB::beginTransaction();

        try {
            $orderItem = OrderItem::findOrFail($orderItemId);

            if ($orderItem->status === 'cancel') {
                $orderItem->status = 'cancel';
                $orderItem->save();

                // Return the quantity back to the stock
                $product = Product::find($orderItem->product_id);
                $product->quantity_in_stock += $orderItem->quantity;
                $product->save();

                DB::commit();

                return redirect()->route('dashboard.order-items.index')->with('success', 'Order successfully rejected.');
            }
            
            DB::rollBack();
            return redirect()->route('dashboard.order-items.index')->with('error', 'Order cannot be rejected.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('dashboard.order-items.index')->with('error', 'Failed to reject OrderItem.');
        }
    }
    
    public function search(Request $request)
    {
        $query = $request->input('query');

        // Ensure the query is not empty
        if (!$query) {
            return response()->json([], 400);
        }

        // Fetch customers whose names match the query
        $customers = Customer::where('customer_name', 'like', "%$query%")
            ->orderBy('customer_name')
            ->get(['id', 'customer_name']);

        return response()->json($customers);
    }

    public function searchCustomers(Request $request)
    {
        $query = $request->input('query');
        $customers = Customer::where('customer_name', 'like', '%' . $query . '%')
            ->limit(10)
            ->get(['id', 'customer_name']);

        return response()->json($customers);
    }

    
    public function __construct()
    {
        ini_set('max_execution_time', 300); // Increase max execution time to 5 minutes
    }
    public function showReportForm(Request $request)
    {
        $type = $request->input('type', 'yearly'); // Default to 'yearly'
        $date = $request->input('date', now()->toDateString()); // Default to today for daily
        $month = $request->input('month', now()->month); // Default to current month
        $year = $request->input('year', now()->year); // Default to current year
    
        // Retrieve sales data
        $salesData = $this->getSalesData($type, $date, $month, $year);
    
        $aggregatedSalesData = $salesData['aggregatedSalesData'];
        $productSalesData = $salesData['productSalesData'];
    
        // Retrieve customer information if needed
        $customer = $request->input('customer_type', 'all'); // Default to 'all' customers or retrieve relevant data
        // Alternatively, if customer data comes from a database:
        // $customer = Customer::all(); // Example of fetching all customers
    
        return view('dashboard.reports.report_results', compact('aggregatedSalesData', 'productSalesData', 'type', 'date', 'month', 'year', 'customer'));
    }

    private function getSalesData($type, $date = null, $month = null, $year = null)
    {
        // Aggregated sales data
        if ($type === 'daily') {
            $aggregatedSalesData = OrderItem::whereNotIn('status', ['rejected', 'cancel'])
                ->select(
                    DB::raw('DATE(order_items.created_at) as date'),
                    DB::raw('SUM(order_items.quantity * order_items.unit_price) as total_sales')
                )
                ->when($date, fn($query) => $query->whereDate('order_items.created_at', $date)) // Filter by specific date
                ->groupBy('date')
                ->having('total_sales', '>', 0)
                ->orderBy('date', 'desc')
                ->get();
        } elseif ($type === 'monthly') {
            $aggregatedSalesData = OrderItem::whereNotIn('status', ['rejected', 'cancel'])
                ->select(
                    DB::raw('YEAR(order_items.created_at) as year'),
                    DB::raw('MONTH(order_items.created_at) as month_number'),
                    DB::raw('MONTHNAME(order_items.created_at) as month_name'),
                    DB::raw('SUM(order_items.quantity * order_items.unit_price) as total_sales')
                )
                ->when($year, fn($query) => $query->whereYear('order_items.created_at', $year))
                ->when($month, fn($query) => $query->whereMonth('order_items.created_at', $month))
                ->groupBy('year', 'month_number', 'month_name')
                ->having('total_sales', '>', 0)
                ->orderBy('year', 'desc')
                ->orderBy('month_number')
                ->get();
        } elseif ($type === 'yearly') {
            $aggregatedSalesData = OrderItem::whereNotIn('status', ['rejected', 'cancel'])
                ->select(
                    DB::raw('YEAR(order_items.created_at) as year'),
                    DB::raw('SUM(order_items.quantity * order_items.unit_price) as total_sales')
                )
                ->when($year, fn($query) => $query->whereYear('order_items.created_at', $year)) // Filter by year if specified
                ->groupBy('year')
                ->having('total_sales', '>', 0)
                ->orderBy('year', 'desc')
                ->get();
        } else {
            $aggregatedSalesData = collect(); // Default empty collection
        }

        // Product-specific sales data
        $productSalesData = OrderItem::whereNotIn('status', ['rejected', 'cancel'])
            ->select(
                'products.name as product_name',
                'order_items.unit_price',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.quantity * order_items.unit_price) as total_price')
            )
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->when($type === 'daily' && $date, fn($query) => $query->whereDate('order_items.created_at', $date))
            ->when($type === 'monthly' && $year, fn($query) => $query->whereYear('order_items.created_at', $year))
            ->when($type === 'monthly' && $month, fn($query) => $query->whereMonth('order_items.created_at', $month))
            ->when($type === 'yearly' && $year, fn($query) => $query->whereYear('order_items.created_at', $year))
            ->groupBy('products.name', 'order_items.unit_price') // Group by product name and price
            ->having('total_price', '>', 0) // Ensure only products with sales are included
            ->orderBy('products.name')
            ->get();

        return [
            'aggregatedSalesData' => $aggregatedSalesData,
            'productSalesData' => $productSalesData,
        ];
    }

    public function generateReport(Request $request)
    {
        $type = $request->input('type', 'yearly');
        $date = $request->input('date');
        $month = $request->input('month');
        $year = $request->input('year', now()->year);
        $customers = $request->input('customer_type', 'both'); // Default to 'both'
        $download = $request->input('download', false);

        $salesData = $this->getSalesData($type, $date, $month, $year, $customers);

        if ($download === 'pdf') {
            $filename = match ($type) {
                'daily' => "{$type}_sales_report_{$date}.pdf",
                'monthly' => "{$type}_sales_report_{$month}_{$year}.pdf",
                'yearly' => "{$type}_sales_report_{$year}.pdf",
                default => "{$type}_sales_report.pdf",
            };

            $pdf = PDF::loadView("dashboard.reports.{$type}_sales", [
                'aggregatedSalesData' => $salesData['aggregatedSalesData'],
                'productSalesData' => $salesData['productSalesData'],
                'type' => $type,
                'date' => $date,
                'month' => $month,
                'year' => $year,
            ]);

            return $pdf->download($filename);
        }

        return view('dashboard.reports.report_results', [
            'aggregatedSalesData' => $salesData['aggregatedSalesData'],
            'productSalesData' => $salesData['productSalesData'],
            'type' => $type,
            'date' => $date,
            'month' => $month,
            'year' => $year,
            'customer_type' => $customers,
        ]);
    }


    public function previewReport(Request $request)
    {
        // Default to 'yearly' if 'type' is not provided
        $type = $request->input('type', 'yearly');
        $month = $request->input('month'); // For monthly report
        $date = $request->input('date');  // For daily report
        $year = $request->input('year', now()->year); // Default to current year if not specified
        $download = $request->input('download', false);

        // Fetch sales data based on report type
        $salesData = $this->getSalesData($type, $date, $month, $year);

        return view('dashboard.reports.report_preview', [
            'aggregatedSalesData' => $salesData['aggregatedSalesData'],
            'productSalesData' => $salesData['productSalesData'],
            'type' => $type,
            'date' => $date,  // Pass date for daily report
            'month' => $month,
            'year' => $year,
        ])->render();
    }
}