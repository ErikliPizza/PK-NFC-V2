<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('order')->get();
        return view('admin/pages/categories/index', compact('categories'));
    }

    public function show (Category $category)
    {
        return view('admin.pages.categories.show', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'title' => 'required|string|max:255|unique:categories,title,' . $category->id,
            'icon' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        $category->title = $request->input('title');

        if ($request->hasFile('icon')) {
            Storage::disk('local')->delete('public/images/categories/' . $category->icon);
            $image = $request->file('icon');
            $imageName = $category->title . '.' . $image->getClientOriginalExtension();
            $image->storeAs('public/images/categories', $imageName, 'local');
            $category->icon = $imageName;
        }

        $category->save();

        return redirect()->route('admin.categories.index')->with('success', 'Category updated successfully.');
    }

    public function store(Request $request)
    {
        // Validate the request data
        $request->validate([
            'order' => 'required|integer',
            'title' => 'required|string|max:255|unique:categories,title',
            'icon' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        // Process the uploaded image
        $image = $request->file('icon');
        $imageName = $request->input('title') . '.' . $image->getClientOriginalExtension();

        $image->storeAs('public/images/categories', $imageName, 'local');

        // Create the category
        Category::create([
            'order' => $request->input('order'),
            'title' => $request->input('title'),
            'icon' => $imageName,
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Category created successfully.');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        Storage::disk('local')->delete('public/images/categories/' . $category->icon);
        return redirect()->route('admin.categories.index')->with('success', $category->title . ' deleted successfully.');
    }

    public function updateOrder(Request $request)
    {
        $orderData = $request->input('order');

        foreach ($orderData as $categoryId => $newOrder) {
            Category::where('id', $categoryId)->update(['order' => $newOrder]);
        }

        return redirect()->route('admin.categories.index')->with('success', 'Category order updated successfully.');
    }

}
