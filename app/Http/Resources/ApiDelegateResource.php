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
            'title'      => $this->prefix,
            'surname'    => $this->last_name,
            'given_name' => $this->first_name,
            'country'    => $this->country
        ];
    }
}
