<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductStoreUpdateRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProductController extends Controller
{
    public function index(): Response
    {
        // Fetch products with relationships and optional category filtering
        $products = Product::with(['user', 'category', 'orderItems'])
            ->whereHas('category', function ($query) {
                $category = request()->input('category');
                if ($category == 'All' || empty($category)) {
                    return;
                }
                $query->where('name', request()->input('category', ''));
            })
            ->orderBy('name', 'asc')  // Order products by name in ascending order
            ->paginate(10)
            ->appends(request()->all());

        // Fetch all categories and add 'All' as an option
        $categories = Category::all()->toArray();
        $categories[] = ['name' => 'All'];

        // Fetch the first product (or null if none exists)
        $firstProduct = Product::first();

        // Pass the data to the view
        return response()
            ->view('dashboard.product.index', compact('products', 'categories', 'firstProduct'));
    }

    public function create(): Response
    {
        $categories = Category::all();

        return response()
            ->view('dashboard.product.create', compact('categories'));
    }

    public function store(ProductStoreUpdateRequest $request): RedirectResponse
    {
        // Check if a product with the same name and price already exists
        $existingProduct = Product::where('name', $request->input('name'))
            ->where('price', $request->input('price'))
            ->first();

        if ($existingProduct) {
            // If it exists, update the quantity
            $existingProduct->quantity_in_stock += $request->input('quantity');
            $existingProduct->save();

            return redirect()
                ->route('dashboard.products.index')
                ->with('success', 'Product quantity successfully updated.');
        }

        // Otherwise, create a new product
        $product = new Product();
        $product->name = $request->input('name');
        $product->description = $request->input('description');
        $product->price = $request->input('price');
        $product->quantity_in_stock = $request->input('quantity');
        $product->original_stocks = $request->input('quantity'); // Set original_stocks to the initial quantity
        $product->category_id = $request->input('category');
        $product->user_id = auth()->id();
        $product->save();

        return redirect()
            ->route('dashboard.products.index')
            ->with('success', 'Product successfully created.');
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


    public function show(Product $product)
    {
        $products = Product::orderBy('created_at', 'desc')->paginate(10)->appends(request()->all());  // This will keep the query params (like page) in the URL; // Fetch all products ordered by latest

        return view('dashboard.product.show', compact('products'));
    }

    public function edit(Product $product): Response
    {
        $categories = Category::all();

        return response()
            ->view('dashboard.product.edit', compact('categories', 'product'));
    }

    public function update(ProductStoreUpdateRequest $request, Product $product): RedirectResponse
    {
        // Check if there is another product with the same name and price
        $existingProduct = Product::where('name', $request->input('name'))
            ->where('price', $request->input('price'))
            ->where('id', '!=', $product->id) // Exclude the current product being updated
            ->first();

        if ($existingProduct) {
            // Merge quantities if a matching product exists
            $existingProduct->quantity_in_stock += $request->input('quantity');
            $existingProduct->save();

            // Delete the current product as it is merged
            $product->delete();

            return redirect()
                ->route('dashboard.products.index')
                ->with('success', 'Product successfully merged with an existing product.');
        }

        // Update the product if no merging is required
        $product->name = $request->input('name');
        $product->description = $request->input('description');
        $product->price = $request->input('price');
        $product->quantity_in_stock = $request->input('quantity');
        $product->original_stocks = $request->input('original_stocks'); // Update original_stocks if necessary
        $product->category_id = $request->input('category');
        $product->user_id = auth()->id();
        $product->save();

        return redirect()
            ->route('dashboard.products.index')
            ->with('success', 'Product successfully updated.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()
            ->route('dashboard.products.index')
            ->with('success', 'Product successfully deleted.');
    }
}
