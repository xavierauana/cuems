<?php

namespace App\Http\Resources;

use App\Delegate;
use Illuminate\Http\Resources\Json\JsonResource;

class TalkResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request) {

        $speakers = $this->speakers->map(function (int $delegateId) {
            return new DelegateResource(Delegate::find($delegateId));
        });

        return [
            'topic'    => $this->title,
            'speakers' => $speakers
        ];
    }
}
