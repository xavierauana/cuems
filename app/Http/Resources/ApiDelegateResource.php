<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ApiDelegateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request) {
        return [
            'title'   => $this->prefix,
            'name'    => $this->first_name . " " . $this->last_name,
            'country' => $this->country
        ];
    }
}
