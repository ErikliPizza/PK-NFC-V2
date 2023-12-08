@extends('admin.layouts.app')
@section('content')
    <div class="max-w-3xl mx-auto">
        <form class="flex justify-end" action="{{ route('admin.categories.destroy', $category) }}" method="POST" id="deleteForm">
            @csrf
            @method('DELETE')
            <button type="button" class="text-red-600 hover:text-red-900" onclick="confirmDelete()">Delete Category</button>
        </form>
        <form action="{{ route('admin.categories.update', $category->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label for="title" class="block text-gray-700 text-sm font-bold mb-2">Title:</label>
                <input type="text" name="title" id="title" value="{{ $category->title }}" class="border rounded w-full py-2 px-3">
            </div>
            <div class="mb-4">
                <label for="icon" class="block text-gray-700 text-sm font-bold mb-2">Icon:</label>
                <input type="file" name="icon" id="icon" class="border rounded w-full py-2 px-3">
            </div>
            @if ($category->icon)
                <img src="{{ asset('storage/images/categories/' . $category->icon) }}" alt="Category Icon" class="mb-2" style="max-width: 100px;">
            @endif
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Update Category
            </button>
        </form>
    </div>
    <hr class="m-2">
    <p class="text-center">Related APPS</p>
    @foreach($category->apps as $app)
        <div class="flex items-center max-w-3xl mx-auto">
            <img src="{{ asset('storage/images/apps/' . $app->icon) }}" alt="Category Icon" class="mb-2" style="max-width: 100px;">
            <span>{{ $app->title }}</span>
        </div>
    @endforeach

    <script>
        function confirmDelete() {
            var confirmed = confirm("Are you sure that you want to delete this category? CRITICAL: ALL RELATED APPS TO THIS CATEGORY WILL BE PERMANENTLY DELETED");
            if (confirmed) {
                document.getElementById('deleteForm').submit();
            }
        }
    </script>
@endsection
