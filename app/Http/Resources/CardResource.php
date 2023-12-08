<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class CardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $informationResource = request()->route()->getName() === 'nfc.show'
            ? BasicInformationResource::collection($this->whenLoaded('informations'))
            : InformationResource::collection($this->whenLoaded('informations'));

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'title' => $this->title,
            'image' => $this->image
                ? Storage::disk('gcs')->url("images/cards/" . $this->image)
                : Storage::disk('gcs')->url("images/cards/default.jpg"),
            'informations' => $informationResource,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'address' => $this->address,
            'slug' => $this->slug,
            'images' => CatalogImageResource::collection($this->images),
            'catalog' => $this->catalog,
            'pdfs' => PdfResource::collection($this->pdfs),
            'billing_information' => $this->billingInformation,
            'premium' => $this->user->premium,
            'bio' => $this->bio,
        ];
    }
}
