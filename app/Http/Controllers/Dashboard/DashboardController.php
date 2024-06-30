<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Role;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index() 
    {
        $products = Product::with(['category', 'user'])->get(); 
        
        // Map over products to add a 'isLowInStock' attribute
        $productsWithStatus = $products->map(function ($product) {
            $product->isLowInStock = $product->quantity_in_stock <= 3 ? true : false;
            return $product;
        });

        // Filter products that are low in stock
        $lowInStockProducts = $productsWithStatus->filter(function ($product) {
            return $product->isLowInStock;
        });

        // Calculate the total quantity of products that are low in stock
        $totalLowInStockQuantity = $lowInStockProducts->sum(function ($product) {
            return $product->quantity_in_stock;
        });

        // Calculate the total number of products that are low in stock
        $totalLowInStockCount = $lowInStockProducts->count();
        
        // Calculate the total quantity in stock, including out of stock products
        $totalQuantityInStock = $products->sum(function ($product) {
            return $product->quantity_in_stock;
        });

        // Fetch the total number of orders
        $totalOrders = OrderItem::count();

        // Fetch monthly sales data
        $monthlySalesData = OrderItem::select(
            'products.name as product_name',
            DB::raw('SUM(order_items.quantity) as total_quantity'),
            DB::raw('SUM(order_items.quantity * order_items.unit_price) as total_sales'),
            DB::raw('MONTH(order_items.created_at) as month')
        )
        ->join('products', 'order_items.product_id', '=', 'products.id')
        ->groupBy('products.name', DB::raw('MONTH(order_items.created_at)'))
        ->get();

        // Calculate the total sales
        $totalSales = OrderItem::sum(DB::raw('quantity * unit_price'));

        $statusData = OrderItem::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')->toArray();
            
        $user = Auth::user();

        if ($user->role_id === Role::HUB) {
            return redirect()->route('dashboard.products.index');
        }

        // Pass the out-of-stock products, total quantity in stock, total out-of-stock quantity, monthly sales data, and total sales to the view
        return view('dashboard.index', compact(
            'products', 'totalOrders', 'lowInStockProducts', 'totalLowInStockQuantity', 'totalLowInStockCount', 
            'productsWithStatus', 'totalQuantityInStock', 'monthlySalesData', 'totalSales', 'statusData'
        ));
    }
}
