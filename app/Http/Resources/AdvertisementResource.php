<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Spatie\MediaLibrary\Models\Media;

class AdvertisementResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request) {
        return [
            'buyer'       => $this->buyer,
            'description' => $this->description,
            'type_id'     => $this->type->id,
            'type_name'   => $this->type->name,
            'logo'        => optional($this->getFirstMedia('logo'))->getFullUrl(),
            'banners'     => $this->getMedia('banners')
                                  ->map(function (Media $media) {
                                      return $media->getFullUrl();
                                  })
        ];
    }
}
