<?php

namespace App\Http\Controllers;

use App\Http\Resources\CardResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\TinyAppResource;
use App\Models\App;
use App\Models\Card;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Services\CardImportService;
class CardController extends Controller
{
    /**
     * Display a listing of the user's cards.
     *
     * @return \Inertia\Response
     */
    public function index(): \Inertia\Response
    {
        // Load the user with their associated cards
        $user = Auth::user()->load('cards');

        // Retrieve the user's default card
        $card = Card::find($user->default_card);

        // Prepare the card resource
        $card = $card ? CardResource::make($card) : null;

        // Get categories with associated apps, ordered by order
        $categories = CategoryResource::collection(
            Category::with('apps')
                ->orderBy('order')
                ->get()
        );

        // Map the user's cards to only include id and title
        $cards = $user->cards->map->only('id', 'title');

        // Render the Inertia view based on the presence of a card
        return $card
            ? Inertia::render('Card/Index', compact('card', 'cards', 'categories'))
            : Inertia::render('Card/NoCard');
    }


    /**
     * Display the specified card.
     *
     * @param  \App\Models\Card  $card
     * @return \Inertia\Response
     */
    public function show(Card $card): \Inertia\Response
    {
        // Authorize the user's view permission for the card
        $this->authorize('owner', $card);

        // Map the user's cards for display
        $cards = $card->user->cards->map(function ($card) {
            return [
                'id' => $card->id,
                'title' => $card->title,
                'image' => $card->image ? Storage::disk('gcs')->url("images/cards/" . $card->image)
                    : Storage::disk('gcs')->url("images/cards/default.jpg")
            ];
        })->toArray();

        // Load card information with associated relationships
        return Inertia::render('Card/Show', [
            'card' => CardResource::make($card->load([
                'informations' => function ($query) {
                    $query->orderBy('order', 'asc');
                },
                'informations.app',
                'informations.app.category'
            ])),
            'apps' => TinyAppResource::collection(App::with('category')->get()),
            'cards' => $cards
        ]);
    }


    /**
     * Update the specified card.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request): \Illuminate\Http\RedirectResponse
    {
        // Validate the request data
        $request->validate([
            'id' => 'required',
            'title' => [
                'required',
                'string',
                'max:30'
            ],
            'image' => 'nullable|array'
        ]);

        // Find the card to be updated
        $card = Card::findOrFail($request->id);

        // Authorize the user's update permission for the card
        $this->authorize('owner', $card);

        // Update card title
        $card->title = $request->input('title');

        // Check if a new image is provided
        if (!empty($request->input('image'))) {
            // Update Image
            $manager = new ImageManager();
            $base64Data = Str::after($request->input('image')[0]['src'], 'base64,');
            $decodedImage = base64_decode($base64Data);
            $img = $manager->make($decodedImage);

            // Resize the image if necessary
            $maxDimension = 1920;
            if ($img->width() > $maxDimension || $img->height() > $maxDimension) {
                $img->resize($maxDimension, $maxDimension, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            }

            // Validate the image aspect ratio
            $desiredRatio = 1;
            $actualRatio = $img->width() / $img->height();
            $tolerance = 0.01;
            if (abs($actualRatio - $desiredRatio) > $tolerance) {
                return redirect()->back()->withErrors(['image' => __('notifications.image_ratio_error')]);
            }

            // Validate the image format
            $allowedFormats = ['jpeg', 'png', 'jpg'];
            $mime = strtolower($img->mime());
            $extension = str_replace('image/', '', $mime);

            if (!in_array($extension, $allowedFormats)) {
                return redirect()->back()->withErrors(['image' => __('notifications.image_extension_error')]);
            }

            // Optimize the image for quality and file size
            $img->encode($extension, 80);
            $filename = uniqid() . '.' . $extension;

            // Define the path within the GCS bucket
            $storagePath = 'images/cards/' . $filename;

            // Use the GCS disk to store the image
            $disk = Storage::disk('gcs');
            $disk->put($storagePath, $img->stream(), 'public');

            // Delete the existing image file from GCS if it exists
            if (!!$card->image) {
                $oldImagePath = 'images/cards/' . $card->image;
                if ($disk->exists($oldImagePath)) {
                    $disk->delete($oldImagePath);
                }
            }

            // Delete existing image file if it exists
            if (!!$card->image) {
                $this->removeImage($card->image);
            }

            $card->image = $filename;
        }

        // Save the updated card
        $card->save();

        return redirect()->back()->with('success', __('notifications.card_updated'));
    }


    /**
     * Remove the specified image from storage.
     *
     * @param string $image
     * @return void
     */
    protected function removeImage($image)
    {
        $disk = Storage::disk('gcs');
        $oldImagePath = 'images/cards/' . $image;
        if ($disk->exists($oldImagePath) && $image !== 'default.jpg') {
            $disk->delete($oldImagePath);
        }
    }


    /**
     * Delete the specified card.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request): \Illuminate\Http\RedirectResponse
    {
        try {
            // Find the card to be deleted
            $card = Card::findOrFail($request->input('id'));

            // Authorize the user's delete permission for the card
            $this->authorize('owner', $card);

            // Remove the card's image
            $this->removeImage($card->image);

            // Delete the card
            $card->delete();

            return redirect()->back()->with('success', __('notifications.card_deleted', ['title' => $card->title]));
        } catch (ModelNotFoundException $exception) {
            // Handle the case where the card was not found
            return redirect()->back()->with('error', __('notifications.card_not_found'));
        }
    }


    /**
     * Add location information to the specified card.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addLocation(Request $request): \Illuminate\Http\RedirectResponse
    {
        try {
            // Validate the request data
            $request->validate([
                'card_id' => 'required',
                'latitude' => 'required',
                'longitude' => 'required',
                'address' => 'nullable|string|max:255'
            ]);

            // Find the card to update
            $card = Card::findOrFail($request->card_id);

            // Authorize the user's swap permission for the card
            $this->authorize('owner', $card);

            // Fill and save the card with location information
            $card->fill($request->only(['latitude', 'longitude', 'address']));
            $card->save();

            return redirect()->back()->with('success', __('notifications.card_updated'));
        } catch (ModelNotFoundException $exception) {
            // Handle the case where the card was not found
            return redirect()->back()->with('error', __('notifications.card_not_found'));
        }
    }


    /**
     * Remove location information from the specified card.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function removeLocation(Request $request): \Illuminate\Http\RedirectResponse
    {
        try {
            // Validate the request data
            $request->validate([
                'card_id' => 'required',
            ]);

            // Find the card to update
            $card = Card::findOrFail($request->card_id);

            // Authorize the user's delete permission for the card
            $this->authorize('owner', $card);

            // Fill and save the card with null location information
            $card->fill([
                'latitude' => null,
                'longitude' => null,
                'address' => null
            ]);
            $card->save();

            return redirect()->back()->with('success', __('notifications.card_updated'));
        } catch (ModelNotFoundException $exception) {
            // Handle the case where the card was not found
            return redirect()->back()->with('error', __('notifications.card_not_found'));
        }
    }


    /**
     * Import information from one card to another.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Services\CardImportService  $importService
     * @return \Illuminate\Http\RedirectResponse
     */
    public function importFrom(Request $request, CardImportService $importService): \Illuminate\Http\RedirectResponse
    {
        try {
            // Validate the request data
            $request->validate([
                'card_id' => 'required',
                'other_card_id' => 'required'
            ]);

            // Find the current and other cards to import from and to
            $currentCard = Card::findOrFail($request->card_id);
            $otherCard = Card::findOrFail($request->other_card_id);

            // Authorize the user's import permission for both cards
            $this->authorize('owner', $currentCard);
            $this->authorize('owner', $otherCard);

            // Use the CardImportService to perform the import
            list($skipped, $created) = $importService->importInformation($currentCard, $otherCard);

            return redirect()->back()->with('info', __('notifications.card_imported', ['skipped' => $skipped, 'created' => $created]));
        } catch (ModelNotFoundException $exception) {
            // Handle the case where one of the cards was not found
            return redirect()->back()->with('error', __('notifications.card_not_found'));
        }
    }

    public function switchCatalog(Card $card, Request $request): \Illuminate\Http\RedirectResponse
    {
        // Validate the request data
        $request->validate([
            'status' => 'boolean',
        ]);

        // Authorize the user's update permission for the card
        $this->authorize('owner', $card);

        // Update the card's catalog status
        $card->catalog = (bool) $request->input('status');
        $card->save();
        return redirect()->back()->with('success', __('notifications.card_updated'));
    }

    /**
     * Update billing information to the specified card.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function billingInformation(Request $request): \Illuminate\Http\RedirectResponse
    {
        try {
            // Validate the request data
            $request->validate([
                'card_id' => 'required',
                't_ü' => 'nullable|max:255|string',
                'a' => 'nullable|max:255|string',
                't_s_n' => 'nullable|max:255|string',
                'v_d' => 'nullable|max:255|string',
                'v_n' => 'nullable|max:255|string',
                'm_n' => 'nullable|max:255|string',
                'email' => 'nullable|string|max:255',
                'k_a' => 'nullable|max:255|string',
            ]);

            // Find the card to update
            $card = Card::findOrFail($request->card_id);

            // Authorize the user's update permission for the card
            $this->authorize('owner', $card);

            // Check if billing information exists for the card and update or create it
            $billingInfoData = [
                't_ü' => $request->t_ü,
                'a' => $request->a,
                't_s_n' => $request->t_s_n,
                'v_d' => $request->v_d,
                'v_n' => $request->v_n,
                'm_n' => $request->m_n,
                'email' => $request->email,
                'k_a' => $request->k_a,
            ];

            $billingInformation = $card->billingInformation()->first();
            if ($billingInformation) {
                // If billing information exists, update it
                $billingInformation->update($billingInfoData);
            } else {
                // If not, create a new billing information record and associate it with the card
                $card->billingInformation()->create($billingInfoData);
            }

            return redirect()->back()->with('success', __('notifications.card_updated'));
        } catch (ModelNotFoundException $exception) {
            // Handle the case where the card was not found
            return redirect()->back()->with('error', __('notifications.card_not_found'));
        }
    }

    /**
     * Update about to the specified card.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeAbout(Request $request): \Illuminate\Http\RedirectResponse
    {
        try {
            // Validate the request data
            $request->validate([
                'card_id' => 'required',
                'about' => 'nullable|max:255|string',
            ]);
            // Find the card to update
            $card = Card::findOrFail($request->card_id);

            // Authorize the user's update permission for the card
            $this->authorize('owner', $card);

            // Update the billing_information column for the specified card
            $card->update([
                'bio' => $request->input('about', null), // Use input method with a default value of null
            ]);

            return redirect()->back()->with('success', __('notifications.card_updated'));
        } catch (ModelNotFoundException $exception) {
            // Handle the case where the card was not found
            return redirect()->back()->with('error', __('notifications.card_not_found'));
        }
    }
}
