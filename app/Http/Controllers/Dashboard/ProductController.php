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
        $products = Product::with(['user', 'category', 'orderItems'])
        ->whereHas('category', function ($query) {
            $category = request()->input('category');
            if ($category == 'All' || empty($category)) {
                return;
            }
            $query->where('name', request()->input('category', ''));
        })
        ->paginate(10)
        ->appends(request()->all());        
        
        $categories = Category::all()->toArray();
        $categories[] = ['name' => 'All'];
        
    return response()
    ->view('dashboard.product.index', compact('products', 'categories'));    
    }

    public function create(): Response
    {
        $categories = Category::all();

        return response()
            ->view('dashboard.product.create', compact('categories'));
    }

    public function store(ProductStoreUpdateRequest $request): RedirectResponse
    {
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

    public function show(Product $product): Response
    {
        return response()
            ->view('dashboard.product.show', compact('product'));
    }

    public function edit(Product $product): Response
    {
        $categories = Category::all();

        return response()
            ->view('dashboard.product.edit', compact('categories', 'product'));
    }

    public function update(ProductStoreUpdateRequest $request, Product $product): RedirectResponse
    {
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
