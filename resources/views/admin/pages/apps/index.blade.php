@extends('admin.layouts.app')

@section('content')
    <form action="{{ route('admin.apps.store') }}"class="m-3 p-6 bg-white rounded-md shadow-md flex justify-center items-center" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="pr-2">
            <!-- Order Input -->
            <div class="mb-4">
                <label for="default_order" class="block text-gray-700 font-bold mb-2">Order</label>
                <input type="number" id="default_order" name="default_order" class="border rounded w-full py-2 px-3">
            </div>
        </div>
        <div class="px-2">
            <!-- Title Input -->
            <div class="mb-4">
                <label for="title" class="block text-gray-700 font-bold mb-2">Title *</label>
                <input type="text" id="title" name="title" class="border rounded w-full py-2 px-3">
            </div>
        </div>
        <div class="px-2">
            <!-- Placeholder Input -->
            <div class="mb-4">
                <label for="placeholder" class="block text-gray-700 font-bold mb-2">Placeholder</label>
                <input type="text" id="placeholder" name="placeholder" class="border rounded w-full py-2 px-3">
            </div>
        </div>
        <div class="px-2">
            <!-- Prefix Input -->
            <div class="mb-4">
                <label for="prefix" class="block text-gray-700 font-bold mb-2">Prefix</label>
                <input type="text" id="prefix" name="prefix" class="border rounded w-full py-2 px-3">
            </div>
        </div>
        <div class="px-2">
            <!-- Regex Input -->
            <div class="mb-4">
                <label for="regex" class="block text-gray-700 font-bold mb-2">Regex *</label>
                <input type="text" id="regex" name="regex" class="border rounded w-full py-2 px-3">
            </div>
        </div>
        <div class="px-2">
            <!-- Suffix Input -->
            <div class="mb-4">
                <label for="suffix" class="block text-gray-700 font-bold mb-2">Suffix</label>
                <input type="text" id="suffix" name="suffix" class="border rounded w-full py-2 px-3">
            </div>
        </div>
        <div class="px-2">
            <!-- Component Input -->
            <div class="mb-4">
                <label for="component" class="block text-gray-700 font-bold mb-2">Component *</label>
                <input type="text" id="component" name="component" class="border rounded w-full py-2 px-3">
            </div>
        </div>
        <div class="px-2">
            <!-- Component Input -->
            <div class="mb-4">
                <label for="category_id" class="block text-gray-700 font-bold mb-2">Category *</label>
                <select class="p-2" name="category_id" id="category_id">
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->title }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="pl-2">
            <!-- Image Input -->
            <div class="mb-4">
                <label for="icon" class="block text-gray-700 font-bold mb-2">Image *</label>
                <input type="file" id="icon" name="icon" class="border rounded w-full py-2 px-3">
            </div>
        </div>

        <!-- Submit Button -->
        <div class="pl-2">
            <!-- Image Input -->
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">&nbsp;</label>
                <button type="submit" class="bg-blue-500 text-white p-2 rounded hover:bg-blue-700">Create</button>
            </div>
        </div>
    </form>

    <table class="w-full divide-y divide-gray-200 table-fixed text-center">
        <tbody class="bg-white divide-y divide-gray-200">
        <form action="{{ route('admin.apps.updateOrder') }}" method="POST">
            <div class="flex justify-end m-3">
                <button type="submit" class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Update Order
                </button>
            </div>
            @csrf
            @method('PUT')
            @foreach ($apps as $app)
                <tr>
                    <td class="py-4 px-6 text-sm font-medium text-gray-900 whitespace-nowrap">
                        <input type="number" name="default_order[{{ $app->id }}]" value="{{ $app->default_order }}">
                    </td>
                    <td class="py-4 px-6 text-sm font-medium text-gray-900 whitespace-nowrap">
                        <img src="{{ asset('storage/images/apps/' . $app->icon) }}" alt="{{ $app->title }}" class="w-16 h-16 object-cover">
                    </td>
                    <td class="py-4 px-6 text-sm font-medium text-gray-900 whitespace-nowrap">{{ $app->title }}</td>
                    <td class="py-4 px-6 text-sm font-medium text-right whitespace-nowrap">
                        <a href="/admin/apps/{{ $app->id }}" class="text-blue-600">Edit</a>
                    </td>
                </tr>
        @endforeach

        </tbody>
    </table>
    </form>

@endsection
