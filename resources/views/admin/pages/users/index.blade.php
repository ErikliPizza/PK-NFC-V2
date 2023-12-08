@extends('admin.layouts.app')

@section('content')
    <form action="{{ route('admin.users.index') }}" method="GET" class="mb-4">
        <div class="flex">
            <input type="text" name="search" class="border border-gray-300 p-2 rounded-l w-full" placeholder="Search by email">
            <button type="submit" class="bg-blue-500 text-white p-2 rounded-r">Search</button>
        </div>
    </form>
    @if($search)
        <div class="mb-4">
            <p class="text-sm">You searched for: <strong>{{ $search }}</strong></p>
        </div>
    @endif
    <table class="w-full divide-y divide-gray-200 table-fixed text-center">
        <tbody class="bg-white divide-y divide-gray-200">
        @foreach ($users as $user)
            <tr>
                <td class="py-4 px-6 text-sm font-medium text-gray-900 whitespace-nowrap">{{ $user->name }}</td>
                <td class="py-4 px-6 text-sm font-medium text-gray-900 whitespace-nowrap">{{ $user->email }}</td>
                <td class="py-4 px-6 text-sm font-medium text-right whitespace-nowrap">
                    <a href="/admin/users/{{ $user->id }}" class="text-blue-600">Edit</a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div class="m-2">
        {{ $users->links() }} {{-- Display pagination links --}}
    </div>
@endsection
