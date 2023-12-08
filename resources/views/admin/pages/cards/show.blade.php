@extends('admin.layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <a href="{{ route('admin.cards.index') }}" class="cursor-pointer">
                <x-back-svg/>
            </a>
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-2xl font-bold">Card Details</h2>
                    <form action="{{ route('admin.cards.destroy', $card) }}" method="POST" id="deleteForm">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="text-red-600 hover:text-red-900" onclick="confirmDelete()">Delete Card</button>
                    </form>
                </div>

                <div class="flex items-center mb-4">
                    <div class="w-16 h-16 bg-gray-300 rounded-full mr-4">
                        <img src="{{ Storage::disk('gcs')->url("images/cards/" . $card->image) }}" class="w-full h-full object-cover rounded-full" alt="User Avatar">
                    </div>

                    <div>
                        <p class="text-xl font-bold">{{ $card->title }}</p>
                        <p class="text-gray-600">{{ $card->slug }}</p>
                        <div><span class="text-gray-600">created</span> @if($card->created_at)<span class="font-bold">{{ $card->created_at->diffForHumans() }}</span>@endif <span class="text-gray-600">by</span> <a class="font-bold text-blue-500" href="/admin/users/{{ $card->user->id }}">{{ $card->user->name }}</a></div>
                    </div>
                </div>

                @if($generatedSlug)
                    <p class="text-xl font-bold">Founded Signatures</p>
                    <p class="text-gray-600">{{ $generatedSlug->slug }}</p>
                    <p>{{ $generatedSlug->created_at }}</p>
                @endif

                <div class="flex justify-end">
                    <a href="/nfc/{{ $card->slug }}" target="_blank" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-6 rounded cursor-pointer">Show</a>
                </div>
            </div>
        </div>
    </div>
    <script>
        function confirmDelete() {
            var confirmed = confirm("Are you sure you want to delete this card?");
            if (confirmed) {
                document.getElementById('deleteForm').submit();
            }
        }
    </script>
@endsection
