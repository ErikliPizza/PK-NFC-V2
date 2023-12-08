<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'component' => $this->component,
            'title' => $this->title,
            'icon' => $this->icon,
            'prefix' => $this->prefix,
            'suffix' => $this->suffix,
            'placeholder' => $this->placeholder,
            'multiple_selection' => $this->multiple_selection,
            'regex' => $this->regex,
            'category_id' => $this->category_id,
            'category' => $this->whenLoaded('category', function () {
                return $this->category->id;
            }),
        ];
    }
}
