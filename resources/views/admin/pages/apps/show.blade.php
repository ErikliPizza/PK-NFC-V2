@extends('admin.layouts.app')
@section('content')
    <div class="max-w-3xl mx-auto">
        <form class="flex justify-end" action="{{ route('admin.apps.destroy', $app) }}" method="POST" id="deleteForm">
            @csrf
            @method('DELETE')
            <button type="button" class="text-red-600 hover:text-red-900" onclick="confirmDelete()">Delete App</button>
        </form>
        <form action="{{ route('admin.apps.update', $app->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label for="title" class="block text-gray-700 text-sm font-bold mb-2">Title*:</label>
                <input type="text" name="title" id="title" value="{{ $app->title }}" class="border rounded w-full py-2 px-3">
            </div>
            <div class="mb-4">
                <label for="placeholder" class="block text-gray-700 text-sm font-bold mb-2">Placeholder:</label>
                <input type="text" name="placeholder" id="placeholder" value="{{ $app->placeholder }}" class="border rounded w-full py-2 px-3">
            </div>
            <div class="mb-4">
                <label for="prefix" class="block text-gray-700 text-sm font-bold mb-2">Prefix:</label>
                <input type="text" name="prefix" id="prefix" value="{{ $app->prefix }}" class="border rounded w-full py-2 px-3">
            </div>
            <div class="mb-4">
                <label for="regex" class="block text-gray-700 text-sm font-bold mb-2">Regex*:</label>
                <input type="text" name="regex" id="regex" value="{{ $app->regex }}" class="border rounded w-full py-2 px-3">
            </div>
            <div class="mb-4">
                <label for="suffix" class="block text-gray-700 text-sm font-bold mb-2">Suffix:</label>
                <input type="text" name="suffix" id="suffix" value="{{ $app->suffix }}" class="border rounded w-full py-2 px-3">
            </div>
            <div class="mb-4">
                <label for="component" class="block text-gray-700 text-sm font-bold mb-2">Component*:</label>
                <input type="text" name="component" id="component" value="{{ $app->component }}" class="border rounded w-full py-2 px-3">
            </div>
            <div class="mb-4">
                <label for="category_id" class="block text-gray-700 text-sm font-bold mb-2">Category*:</label>
                <select class="p-2" name="category_id" id="category_id">
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ $category->id == $app->category_id ? 'selected' : '' }}>
                            {{ $category->title }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label for="icon" class="block text-gray-700 text-sm font-bold mb-2">Icon:</label>
                <input type="file" name="icon" id="icon" class="border rounded w-full py-2 px-3">
            </div>
            @if ($app->icon)
                <img src="{{ asset('storage/images/apps/' . $app->icon) }}" alt="Category Icon" class="mb-2" style="max-width: 100px;">
            @endif
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mb-2">
                Update App
            </button>
        </form>
    </div>

    <script>
        function confirmDelete() {
            var confirmed = confirm("Are you sure you want to delete this app? CRITICAL: ALL RELATED INFORMATIONS RELATED TO THIS APP WILL BE PERMANENTLY DELETED");
            if (confirmed) {
                document.getElementById('deleteForm').submit();
            }
        }
    </script>
@endsection
