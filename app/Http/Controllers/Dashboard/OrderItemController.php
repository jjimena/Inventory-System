<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Role;
use App\Models\Customer;
// use Barryvdh\DomPDF\PDF;
// use Barryvdh\DomPDF\Facade as PDF;
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
        $customers = Customer::all(); // Fetch all customers
        $user = Auth::user(); // Retrieve the currently authenticated user

        return response()->view('dashboard.order-item.create', compact('customers', 'products', 'user'));
    }

    

    public function store(Request $request)
    {
        DB::beginTransaction();
    
        try {
            $validatedData = $request->validate([
                'quantity' => 'required|integer|min:1',
                'product_id' => 'required|exists:products,id',
                'customer_id' => 'required|exists:customers,id',
            ]);
    
            
            if ($request->user()->role_id === 3) {
                $customer = \App\Models\Customer::firstOrCreate(
                    [
                        'customer_name' => $request->user()->name,
                        'customer_phone_number' => $request->user()->phone_number,
                        'hub_name' => $request->user()->hub_name,
                        'address' => $request->user()->address,
                        // 'date' => now(),
                    ]
                );
    
                if (
                    $customer->customer_name !== $request->user()->name ||
                    $customer->customer_phone_number !== $request->user()->phone_number ||
                    $customer->customer_email !== $request->user()->email ||
                    $customer->hub_name !== $request->user()->hub_name ||
                    $customer->address !== $request->user()->address
                ) {
                    return redirect()->back()->withErrors(['customer_id' => 'The selected customer is invalid.']);
                }
                $validatedData['customer_id'] = $customer->id;
            }
    
            $productBatches = Product::where('id', $validatedData['product_id'])
                ->orderBy('created_at')
                ->get();
    
            // dd($productBatches);
            
            $remainingQuantity = $validatedData['quantity'];
            $totalPrice = 0;
    
            foreach ($productBatches as $batch) {
                if ($remainingQuantity <= 0) {
                    break;
                }
    
                $batchQuantity = $batch->quantity_in_stock;
    
                if ($request->user()->role_id !== 3 && $remainingQuantity > $batchQuantity) {
                    return redirect()->back()->withErrors(['quantity' => 'The quantity is greater than the quantity in stock.']);
                }
    
                $quantityToDeduct = min($remainingQuantity, $batchQuantity);
                $remainingQuantity -= $quantityToDeduct;
                $batch->quantity_in_stock -= $quantityToDeduct;
                $totalPrice += $quantityToDeduct * $batch->price;
                $batch->save();
    
                // dd($batch);
                $orderItem = new OrderItem();
                $orderItem->quantity = $quantityToDeduct;
                $orderItem->unit_price = $batch->price;
                $orderItem->product_id = $batch->id;
                $orderItem->customer_id = $validatedData['customer_id'];
                $orderItem->user_id = auth()->id();
                $orderItem->status = 'pending';
                $orderItem->save();
                
                // dd($orderItem);
            }
    
            if ($remainingQuantity > 0) {
                DB::rollBack();
                return redirect()->back()->withErrors(['quantity' => 'Not enough stock to fulfill the order.'])->withInput();
            }
    
            DB::commit();
    
            return view('dashboard.order-item.payment', compact('orderItem', 'totalPrice'));
    
        } catch (ValidationException $e) {
            DB::rollBack();
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to create OrderItem. ' . $e->getMessage());
        }
    }
    

    public function show(OrderItem $orderItem): Response
    {
        return response()
            ->view('dashboard.order-item.show', compact('orderItem'));
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

            if ($orderItem->status === 'pending') {
                $orderItem->status = 'rejected';
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


    // public function reject(Request $request, $orderItemId): RedirectResponse
    // {
    //     $orderItem = OrderItem::findOrFail($orderItemId);

    //     if ($orderItem->status === 'pending') {
    //         $orderItem->status = 'rejected';
    //         $orderItem->save();

    //         return redirect()->route('dashboard.order-items.index')->with('success', 'Order successfully rejected.');
    //     }
    //     return redirect()->route('dashboard.order-items.index')->with('error', 'Order cannot be rejected.');
    // }
    

    public function search(Request $request)
    {
        $search = $request->input('search');

        $orderItems = OrderItem::whereHas('customer', function ($query) use ($search) {
            $query->where('customer_name', 'like', "%$search%");
        })->paginate(10);

        return view('dashboard.order-item.index', compact('orderItems'));
    }
    
    public function __construct()
    {
        ini_set('max_execution_time', 300); // Increase max execution time to 5 minutes
    }

    public function showReportForm(Request $request)
    {
        $type = $request->input('type', 'monthly'); // Default to 'monthly' if no type is provided
        $salesData = $this->getSalesData($type);

        $aggregatedSalesData = $salesData['aggregatedSalesData'];
        $productSalesData = $salesData['productSalesData'];

        return view('dashboard.reports.report_results', compact('aggregatedSalesData', 'productSalesData', 'type'));
    }

    public function generateReport(Request $request)
    {
        $type = $request->input('type');
        $download = $request->input('download', false);
        $salesData = $this->getSalesData($type);

        if ($download === 'pdf') {
            $viewName = $type === 'yearly' ? 'dashboard.reports.yearly_sales' : 'dashboard.reports.monthly_sales';
            $pdf = PDF::loadView($viewName, [
                'aggregatedSalesData' => $salesData['aggregatedSalesData'],
                'productSalesData' => $salesData['productSalesData'],
                'type' => $type
            ]);
            return $pdf->download($type . '_sales_report.pdf');
        }

        return view('dashboard.reports.report_results', [
            'aggregatedSalesData' => $salesData['aggregatedSalesData'],
            'productSalesData' => $salesData['productSalesData'],
            'type' => $type
        ]);
    }
    private function getSalesData($type)
    {
        // Aggregated sales data (yearly or monthly)
        if ($type === 'yearly') {
            $aggregatedSalesData = OrderItem::whereNotIn('status', ['rejected', 'pending'])
                ->select(
                    DB::raw('YEAR(order_items.created_at) as period'),
                    DB::raw('SUM(order_items.quantity * order_items.unit_price) as total_sales')
                )
                ->groupBy('period')
                ->orderBy('period', 'desc')
                ->get();
        } else {
            $aggregatedSalesData = OrderItem::whereNotIn('status', ['rejected', 'pending'])
                ->select(
                    DB::raw('YEAR(order_items.created_at) as year'),
                    DB::raw('MONTH(order_items.created_at) as month_number'),
                    DB::raw('MONTHNAME(order_items.created_at) as month_name'),
                    DB::raw('SUM(order_items.quantity * order_items.unit_price) as total_sales')
                )
                ->groupBy('year', 'month_number', 'month_name')
                ->orderBy('year', 'desc')
                ->orderBy('month_number')
                ->get();
        }
    
        // Product-specific sales data
        $productSalesData = OrderItem::whereNotIn('status', ['rejected', 'pending'])
            ->select(
                'products.name as product_name',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('AVG(order_items.unit_price) as unit_price'),
                DB::raw('SUM(order_items.quantity * order_items.unit_price) as total_price')
            )
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->groupBy('products.name')
            ->orderBy('products.name')
            ->get();
    
        return [
            'aggregatedSalesData' => $aggregatedSalesData,
            'productSalesData' => $productSalesData
        ];
    }
    
    // private function getSalesData($type)
    // {
    //     // Aggregated sales data (yearly or monthly)
    //     if ($type === 'yearly') {
    //         $aggregatedSalesData = OrderItem::where('status', '!=', 'rejected')
    //             ->select(
    //                 DB::raw('YEAR(order_items.created_at) as period'),
    //                 DB::raw('SUM(order_items.quantity * order_items.unit_price) as total_sales')
    //             )
    //             ->groupBy('period')
    //             ->orderBy('period', 'desc')
    //             ->get();
    //     } else {
    //         $aggregatedSalesData = OrderItem::where('status', '!=', 'rejected')
    //             ->select(
    //                 DB::raw('YEAR(order_items.created_at) as year'),
    //                 DB::raw('MONTH(order_items.created_at) as month_number'),
    //                 DB::raw('MONTHNAME(order_items.created_at) as month_name'),
    //                 DB::raw('SUM(order_items.quantity * order_items.unit_price) as total_sales')
    //             )
    //             ->groupBy('year', 'month_number', 'month_name')
    //             ->orderBy('year', 'desc')
    //             ->orderBy('month_number')
    //             ->get();
    //     }
    
    //     // Product-specific sales data
    //     $productSalesData = OrderItem::where('status', '!=', 'rejected')
    //         ->select(
    //             'products.name as product_name',
    //             DB::raw('SUM(order_items.quantity) as total_quantity'),
    //             DB::raw('AVG(order_items.unit_price) as unit_price'),
    //             DB::raw('SUM(order_items.quantity * order_items.unit_price) as total_price')
    //         )
    //         ->join('products', 'order_items.product_id', '=', 'products.id')
    //         ->groupBy('products.name')
    //         ->orderBy('products.name')
    //         ->get();
    
    //     return [
    //         'aggregatedSalesData' => $aggregatedSalesData,
    //         'productSalesData' => $productSalesData
    //     ];
    // }
    
    // private function getSalesData($type)
    // {
    //     // Aggregated sales data (yearly or monthly)
    //     if ($type === 'yearly') {
    //         $aggregatedSalesData = OrderItem::select(
    //             DB::raw('YEAR(order_items.created_at) as period'),
    //             DB::raw('SUM(order_items.quantity * order_items.unit_price) as total_sales')
    //         )
    //         ->groupBy('period')
    //         ->orderBy('period', 'desc')
    //         ->get();
    //     } else {
    //         $aggregatedSalesData = OrderItem::select(
    //             DB::raw('YEAR(order_items.created_at) as year'),
    //             DB::raw('MONTH(order_items.created_at) as month_number'),
    //             DB::raw('MONTHNAME(order_items.created_at) as month_name'),
    //             DB::raw('SUM(order_items.quantity * order_items.unit_price) as total_sales')
    //         )
    //         ->groupBy('year', 'month_number', 'month_name')
    //         ->orderBy('year', 'desc')
    //         ->orderBy('month_number')
    //         ->get();
    //     }

    //     // Product-specific sales data
    //     $productSalesData = OrderItem::select(
    //         'products.name as product_name',
    //         DB::raw('SUM(order_items.quantity) as total_quantity'),
    //         DB::raw('AVG(order_items.unit_price) as unit_price'),
    //         DB::raw('SUM(order_items.quantity * order_items.unit_price) as total_price')
    //     )
    //     ->join('products', 'order_items.product_id', '=', 'products.id')
    //     ->groupBy('products.name')
    //     ->orderBy('products.name')
    //     ->get();

    //     return [
    //         'aggregatedSalesData' => $aggregatedSalesData,
    //         'productSalesData' => $productSalesData
    //     ];
    // }
}