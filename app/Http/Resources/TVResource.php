<?php

namespace App\Http\Resources;

use App\Models\TvModel as TV;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class TVResource
 * @mixin TV

 */
class TVResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'price' => $this->price,
            'brand' => $this->brand->name,
            'link' => $this->link,
            'image' => $this->image,
        ];
    }
}
