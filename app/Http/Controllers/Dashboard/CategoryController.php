<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CategoryController extends Controller
{
    public function index(): Response
    {
        $categories = Category::with('products')->paginate(10);

        return response()
            ->view('dashboard.category.index', compact('categories'));
    }

    public function create(): Response
    {
        return response()
            ->view('dashboard.category.create');
    }

    public function store()
    {
        request()->validate([
            'name' => 'required|unique:categories,name',
        ]);

        Category::create(request()->only(['name']));

        return redirect()
            ->route('dashboard.categories.index')
            ->with('success', 'Category successfully created.');
    }

    public function edit(Category $category): Response
    {
        return response()
            ->view('dashboard.category.edit', compact('category'));
    }

    public function update(Category $category)
    {
        request()->validate([
            'name' => "required|unique:categories,name,$category->id",
        ]);

        $category->name = request()->input('name');
        $category->save();

        return redirect()
            ->route('dashboard.categories.index')
            ->with('success', 'Category successfully updated.');
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()
            ->route('dashboard.categories.index')
            ->with('success', 'Category successfully deleted.');
    }
}
