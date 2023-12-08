@extends('admin.layouts.app')

@section('content')

    <form action="{{ route('admin.categories.store') }}"class="m-3 p-6 bg-white rounded-md shadow-md flex justify-center items-center" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="pr-2">
            <!-- Order Input -->
            <div class="mb-4">
                <label for="order" class="block text-gray-700 font-bold mb-2">Order</label>
                <input type="number" id="order" name="order" class="border rounded w-full py-2 px-3">
            </div>
        </div>
        <div class="px-2">
            <!-- Title Input -->
            <div class="mb-4">
                <label for="title" class="block text-gray-700 font-bold mb-2">Title</label>
                <input type="text" id="title" name="title" class="border rounded w-full py-2 px-3">
            </div>
        </div>
        <div class="pl-2">
            <!-- Image Input -->
            <div class="mb-4">
                <label for="icon" class="block text-gray-700 font-bold mb-2">Image</label>
                <input type="file" id="icon" name="icon" class="border rounded w-full py-2 px-3">
            </div>
        </div>

        <!-- Submit Button -->
        <div class="pl-2">
            <!-- Image Input -->
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">&nbsp;</label>
                <button type="submit" class="bg-blue-500 text-white p-2 rounded hover:bg-blue-700">Create Category</button>
            </div>
        </div>
    </form>

    <table class="w-full divide-y divide-gray-200 table-fixed text-center">
        <tbody class="bg-white divide-y divide-gray-200">
        <form action="{{ route('admin.categories.updateOrder') }}" method="POST">
            @csrf
            @method('PUT')
        @foreach ($categories as $category)
            <tr>
                <td class="py-4 px-6 text-sm font-medium text-gray-900 whitespace-nowrap">
                    <input type="number" name="order[{{ $category->id }}]" value="{{ $category->order }}">
                </td>
                <td class="py-4 px-6 text-sm font-medium text-gray-900 whitespace-nowrap">
                    <img src="{{ asset('storage/images/categories/' . $category->icon) }}" alt="{{ $category->title }}" class="w-16 h-16 object-cover">
                </td>
                <td class="py-4 px-6 text-sm font-medium text-gray-900 whitespace-nowrap">{{ $category->title }}</td>
                <td class="py-4 px-6 text-sm font-medium text-right whitespace-nowrap">
                    <a href="/admin/categories/{{ $category->id }}" class="text-blue-600">Edit</a>
                </td>
            </tr>
        @endforeach

        </tbody>
    </table>
    <div class="flex justify-end mt-3">
        <button type="submit" class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Update Order
        </button>
    </div>
    </form>

@endsection
