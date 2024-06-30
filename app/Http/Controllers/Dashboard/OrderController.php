<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Customer;
use App\Models\OrderItem;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\OrderStoreUpdateRequest;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        // Paginate customers with their order items
        $customers = Customer::with('orderItems')
            ->paginate(10); // Adjust the number per page as per your requirement

        // Transform the paginated collection to compute total price and total quantity
        $customers->getCollection()->transform(function ($customer) {
            $totalPrice = $customer->orderItems->sum(function ($orderItem) {
                return $orderItem->unit_price * $orderItem->quantity;
            });
            $totalQuantity = $customer->orderItems->sum('quantity');

            return [
                'id' => $customer->id,
                'customer_name' => $customer->customer_name,
                'customer_email' => $customer->customer_email,
                'customer_phone_number' => $customer->customer_phone_number,
                'address' => $customer->address,
                'hub_name' => $customer->hub_name,
                'total_price' => $totalPrice,
                'total_quantity' => $totalQuantity,
            ];
        });

        return view('dashboard.order.index', compact('customers'));
    }

    public function create(): Response
    {
        $products = Product::all();
        $customers = Customer::all();
        $orderItems = OrderItem::all();
        $user = Auth::user(); // Retrieve the currently authenticated user

        return response()
            ->view('dashboard.order.create', compact('customers', 'products', 'orderItems', 'user'));
    }

    public function store(OrderStoreUpdateRequest $request): RedirectResponse
    {
        return redirect()
            ->route('dashboard.orders.index')
            ->with('success', 'Order successfully created.');
    }

    public function customerOrders($customerId)
    {
        $customer = Customer::findOrFail($customerId);

        // Fetch all orders for the customer with their order items and products
        $orders = Order::with(['orderItems.product'])
            ->where('customer_id', $customerId)
            ->get();

        // Calculate the total price for each order
        foreach ($orders as $order) {
            $totalPrice = 0;
            foreach ($order->orderItems as $item) {
                $totalPrice += $item->unit_price * $item->quantity;
            }
            $order->total_price = $totalPrice;
        }

        return view('dashboard.orders.customer_orders', compact('customer', 'orders'));
    }

    public function show($id)
    {
        $order = Order::with('orderItems')->findOrFail($id);
        return view('dashboard.order.show', compact('order'));
    }

    public function edit(Customer $customer)
    {
        return view('dashboard.order.edit', compact('customer'));
    }

    public function update(OrderStoreUpdateRequest $request, Order $order): RedirectResponse
    {
        $order->date = $request->input('date');
        $order->customer_name = $request->input('customer_name');
        $order->customer_email = $request->input('customer_email');
        $order->save();

        return redirect()
            ->route('dashboard.orders.index')
            ->with('success', 'Order successfully updated.');
    }

    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();
        return redirect()->route('dashboard.orders.index')->with('success', 'Order deleted successfully');
    }

    public function orderItems(Order $order): Response
    {
        $orderItems = OrderItem::with('product')->where('order_id', $order->id)->paginate(10);

        return response()
            ->view('dashboard.order.order-item', compact('order', 'orderItems'));
    }
}
