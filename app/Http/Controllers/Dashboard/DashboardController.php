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

        // Get out-of-stock products (where quantity_in_stock == 0)
        $outOfStockProducts = $products->filter(function ($product) {
            return $product->quantity_in_stock === 0;
        });

        // Calculate the total quantity of products that are low in stock
        $totalLowInStockQuantity = $lowInStockProducts->sum(function ($product) {
            return $product->quantity_in_stock;
        });

        // Calculate the total number of products that are low in stock
        $totalLowInStockCount = $lowInStockProducts->count();

        // Get the total number of out-of-stock products
        $totalOutOfStockCount = $outOfStockProducts->count();

        // Calculate the total quantity in stock, including out of stock products
        $totalQuantityInStock = $products->sum(function ($product) {
            return $product->quantity_in_stock;
        });
        // $totalQuantityInStock = $products->sum('quantity_in_stock');

        // Fetch the total number of orders
        $totalOrders = OrderItem::count();

        // Calculate the total sales excluding canceled orders
        $totalSales = OrderItem::where('status', '!=', 'cancel') // Exclude canceled orders
        ->sum(DB::raw('quantity * unit_price'));

        // For the product list chart (names and quantities)
        $productListData = $products->map(function ($product) {
            return [
                'product_name' => $product->name,
                'quantity' => $product->quantity_in_stock // You can change this to another metric if necessary
            ];
        });

        $statusData = OrderItem::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')->toArray();
            
        $user = Auth::user();

        if ($user->role_id === Role::HUB) {
            return redirect()->route('dashboard.products.index');
        }

        $annualSalesData = OrderItem::select(
            'products.name as product_name',
            'order_items.unit_price as price',
            DB::raw('SUM(order_items.quantity * order_items.unit_price) as total_sales'),
            DB::raw('YEAR(order_items.created_at) as year') // Add year field
        )
        ->join('products', 'order_items.product_id', '=', 'products.id')
        ->where('order_items.status', '!=', 'cancel')
        ->groupBy('products.name', 'order_items.unit_price', DB::raw('YEAR(order_items.created_at)'))
        ->get();

        // Fetch annual sales data
        // $annualSalesData = OrderItem::select(
        //     'products.name as product_name',
        //     'order_items.unit_price as price',
        //     DB::raw('SUM(order_items.quantity * order_items.unit_price) as total_sales')
        // )
        // ->join('products', 'order_items.product_id', '=', 'products.id')
        // ->where('order_items.status', '!=', 'cancel') // Exclude canceled orders
        // ->groupBy('products.name', 'order_items.unit_price')
        // ->get();

        // Fetch monthly sales data

        $monthlySalesData = OrderItem::select(
            'products.name as product_name',
            'order_items.unit_price as price',
            DB::raw('SUM(order_items.quantity * order_items.unit_price) as total_sales'),
            DB::raw('MONTH(order_items.created_at) as month'),
            DB::raw('YEAR(order_items.created_at) as year') // Add year field
        )
        ->join('products', 'order_items.product_id', '=', 'products.id')
        ->where('order_items.status', '!=', 'cancel')
        ->groupBy('products.name', 'order_items.unit_price', DB::raw('MONTH(order_items.created_at)'), DB::raw('YEAR(order_items.created_at)'))
        ->get();
        
        // $monthlySalesData = OrderItem::select(
        //     'products.name as product_name',
        //     'order_items.unit_price as price',
        //     DB::raw('SUM(order_items.quantity * order_items.unit_price) as total_sales'),
        //     DB::raw('MONTH(order_items.created_at) as month')
        // )
        // ->join('products', 'order_items.product_id', '=', 'products.id')
        // ->where('order_items.status', '!=', 'cancel') // Exclude canceled orders
        // ->groupBy('products.name', 'order_items.unit_price', DB::raw('MONTH(order_items.created_at)'))
        // // ->groupBy('products.name', DB::raw('MONTH(order_items.created_at)'))
        // ->get();

        $availableYears = OrderItem::selectRaw('YEAR(created_at) as year')
        ->distinct()
        ->orderBy('year', 'desc')
        ->pluck('year')
        ->toArray();


        // Pass out-of-stock products, total count, and other data to the view
        return view('dashboard.index', compact(
            'products', 'totalOrders', 'lowInStockProducts', 'totalLowInStockQuantity', 
            'totalLowInStockCount', 'totalOutOfStockCount', 'outOfStockProducts', 
            'productsWithStatus', 'totalQuantityInStock', 'monthlySalesData', 'totalSales',
            'statusData', 'productListData', 'annualSalesData', 'productListData', 'availableYears'
        ));
    }
}
