@extends('admin.layouts.app')

@section('content')
    <form action="{{ route('admin.cards.index') }}" method="GET" class="mb-4">
        <div class="flex">
            <input type="text" name="search" class="border border-gray-300 p-2 rounded-l w-full" placeholder="Search by slug or email">
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
        @foreach ($cards as $card)
            <tr>
                <td class="py-4 px-6 text-sm font-medium text-gray-900 whitespace-nowrap">
                    <img src="{{ Storage::disk('gcs')->url("images/cards/" . $card->image) }}" alt="{{ $card->title }}" class="w-16 h-16 object-cover rounded-full">
                </td>
                <td class="py-4 px-6 text-sm font-medium text-gray-900 whitespace-nowrap">{{ $card->title }}</td>
                <td class="py-4 px-6 text-sm font-medium text-gray-900 whitespace-nowrap">{{ $card->slug }}</td>
                <td class="py-4 px-6 text-sm font-medium text-right whitespace-nowrap">
                    <a href="/admin/cards/{{ $card->id }}" class="text-blue-600">Edit</a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div class="m-2">
        {{ $cards->links() }} {{-- Display pagination links --}}
    </div>
@endsection
