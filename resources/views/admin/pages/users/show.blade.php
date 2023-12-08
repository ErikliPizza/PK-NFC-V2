@extends('admin.layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <a href="{{ route('admin.users.index') }}" class="cursor-pointer">
                <x-back-svg/>
            </a>
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-2xl font-bold">User Details</h2>
                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" id="deleteForm">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="text-red-600 hover:text-red-900" onclick="confirmDelete()">Delete User</button>
                    </form>
                </div>

                <div class="flex items-center mb-4">
                    <div>
                        <p class="text-xl font-bold">{{ $user->name }}</p>
                        <p class="text-gray-600">{{ $user->email }}</p>
                        <p><strong>Premium Status:</strong> {{ $user->premium ? 'Premium' : 'Free' }}</p>
                    </div>
                </div>

                <h3 class="text-xl font-bold mb-2">Change Password</h3>
                <form action="{{ route('admin.users.update', $user) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-4">
                        <label for="password" class="block text-gray-700 text-sm font-bold mb-2">New Password</label>
                        <input type="text" name="password" id="password" class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div class="flex items-center justify-between">
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Change Password</button>
                        <a href="/admin/cards?search={{ $user->email }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded cursor-pointer">Show Cards</a>
                    </div>
                </form>
                <div class="flex justify-center">
                    <form method="post" action="{{ route('toggle.premium', ['user' => $user]) }}">
                        @csrf
                        <button type="submit" class="bg-blue-500 hover:bg-sky-900 text-white font-bold py-2 px-4 rounded">Toggle Premium</button>
                    </form>
                </div>

            </div>
        </div>
    </div>
    <script>
        function confirmDelete() {
            var confirmed = confirm("Are you sure you want to delete this user?");
            if (confirmed) {
                document.getElementById('deleteForm').submit();
            }
        }
    </script>
@endsection
