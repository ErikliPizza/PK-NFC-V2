<?php

namespace App\Http\Controllers;

use App\Http\Resources\CardResource;
use Illuminate\Http\Request;
use App\Models\Card;
use App\Models\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Intervention\Image\ImageManager;

class CatalogController extends Controller
{
    public function show(Card $card): \Inertia\Response
    {
        // Authorize the user's view permission for the card
        $this->authorize('owner', $card);
        // Load card information with associated relationships
        return Inertia::render('Card/Catalog/Show', [
            'card' => CardResource::make($card)
        ]);
    }
    public function destroy(Image $image): \Illuminate\Http\RedirectResponse
    {
        $card = Card::findOrFail($image->card_id);
        $this->authorize('owner', $card);

        $oldImagePath = 'images/catalog/' . $image->filename;
        $disk = Storage::disk('gcs');
        if ($disk->exists($oldImagePath)) {
            $disk->delete($oldImagePath);
        }
        $image->delete();
        return redirect()->back()->with('success', __('notifications.card_updated'));
    }
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        // Validate the request data
        $request->validate([
            'id' => 'required',
            'image' => 'required|array'
        ]);

        // Find the card to be updated
        $card = Card::findOrFail($request->id);
        if ($card->images->count() >= 12) {
            return redirect()->back()->withErrors(['image' => 'maximum 12 images.']);
        }
        // Authorize the user's update permission for the card
        $this->authorize('owner', $card);

        // Upload Image
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
        $desiredRatio = 1.6667;
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
        $storagePath = 'images/catalog/' . $filename;

        // Use the GCS disk to store the image
        $disk = Storage::disk('gcs');
        $disk->put($storagePath, $img->stream(), 'public');

        Image::create([
            'card_id' => $request->id,
            'filename' => $filename,
        ]);
        return redirect()->back()->with('success', __('notifications.card_updated'));

    }
    public function updateOrder(Request $request)
    {
        $order = $request->input('order');

        foreach ($order as $item) {
            $image = Image::findOrFail($item['image_id']);
            $card = Card::findOrFail($image->card_id);
            $this->authorize('owner', $card);

            $image->order = $item['order'];
            $image->save();
        }

        return redirect()->back()->with('success', __('notifications.card_updated'));
    }
}
