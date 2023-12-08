<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Models\GeneratedSlug;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CardController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index(Request $request)
    {
        $search = $request->input('search');

        $cards = Card::with('user');

        if ($search) {
            $cards->where(function($query) use ($search) {
                $query->where('slug', 'LIKE', "%$search%")
                    ->orWhereHas('user', function($query) use ($search) {
                        $query->where('email', 'LIKE', "%$search%");
                    });
            });
        }

        $cards = $cards->paginate(10);

        return view('admin/pages/cards/index', compact('cards', 'search'));
    }

    public function show(Card $card)
    {
        $cardSlug = Str::before($card->slug, '?'); // Extract the slug part before '?'
        $generatedSlug = GeneratedSlug::where('slug', 'like', "%$cardSlug%")->first();
        return view('admin.pages.cards.show', compact('card', 'generatedSlug'));
    }

    public function destroy(Card $card)
    {
        $card->delete();
        $disk = Storage::disk('gcs');
        $imagePath = 'images/cards/' . $card->image;
        if ($disk->exists($imagePath) && $card->image !== 'default.jpg') {
            $disk->delete($imagePath);
        }
        return redirect()->route('admin.cards.index')->with('success', $card->slug . ' deleted successfully.');
    }
}
