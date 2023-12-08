<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\App;
use Illuminate\Support\Facades\Storage;

class AppController extends Controller
{
    public function index()
    {
        $apps = App::orderBy('default_order')->get();
        $categories = Category::orderBy('order')->get();

        return view('admin/pages/apps/index', compact('apps', 'categories'));
    }

    public function show (App $app)
    {
        $categories = Category::orderBy('order')->get();
        return view('admin.pages.apps.show', compact('app', 'categories'));
    }

    public function store(Request $request)
    {
        // Validate the form data
        $validatedData = $request->validate([
            'default_order' => 'nullable|integer',
            'title' => 'required|string|max:255',
            'placeholder' => 'nullable|string|max:255',
            'prefix' => 'nullable|string|max:255',
            'regex' => 'required|string|max:512',
            'suffix' => 'nullable|string|max:255',
            'component' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id', // Assuming your categories table is named 'categories'
            'icon' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Adjust mime types and max file size as needed
        ]);

        // Process the uploaded image
        $image = $request->file('icon');
        $imageName = $request->input('title') . '.' . $image->getClientOriginalExtension();

        $image->storeAs('public/images/apps', $imageName, 'local');
        // Create a new App instance with the validated data
        App::create([
            'default_order' => $validatedData['default_order'],
            'title' => $validatedData['title'],
            'placeholder' => $validatedData['placeholder'],
            'prefix' => $validatedData['prefix'],
            'regex' => $validatedData['regex'],
            'suffix' => $validatedData['suffix'],
            'component' => $validatedData['component'],
            'category_id' => $validatedData['category_id'],
            'icon' => $imageName,
        ]);

        // Redirect back or to a success page
        return redirect()->back()->with('success', 'App created successfully!');
    }

    public function update(Request $request, App $app)
    {
        // Validate the form data
        $validatedData = $request->validate([
            'default_order' => 'nullable|integer',
            'title' => 'required|string|max:255',
            'placeholder' => 'nullable|string|max:255',
            'prefix' => 'nullable|string|max:255',
            'regex' => 'required|string|max:512',
            'suffix' => 'nullable|string|max:255',
            'component' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('icon')) {
            Storage::disk('local')->delete('public/images/apps/' . $app->icon);
            $image = $request->file('icon');
            $imageName = $app->title . '.' . $image->getClientOriginalExtension();
            $image->storeAs('public/images/apps', $imageName, 'local');
            $validatedData['icon'] = $imageName;
        }

        // Update the attributes
        $app->update($validatedData);

        // Redirect back or to a success page
        return redirect()->back()->with('success', 'App updated successfully!');
    }

    public function destroy(App $app)
    {
        $app->delete();
        Storage::disk('local')->delete('public/images/apps/' . $app->icon);
        return redirect()->route('admin.apps.index')->with('success', $app->title . ' deleted successfully.');
    }

    public function updateOrder(Request $request)
    {
        $orderData = $request->input('default_order');

        foreach ($orderData as $appId => $newOrder) {
            App::where('id', $appId)->update(['default_order' => $newOrder]);
        }

        return redirect()->route('admin.apps.index')->with('success', 'Apps order updated successfully.');
    }
}
