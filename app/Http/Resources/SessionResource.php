<?php

namespace App\Http\Resources;

use App\Delegate;
use App\Enums\SessionModerationType;
use Illuminate\Http\Resources\Json\JsonResource;

class SessionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request) {
        $segments = $request->segments();

        $moderators = $this->moderators->map(function (Delegate $delegate) {
            return [
                'name' => $delegate->name
            ];
        });

        $moderationType = ucwords(strtolower(array_flip(SessionModerationType::getTypes())[$this->moderation_type]));

        $moderationType = count($moderators) > 1 ?
            ($this->moderation_type === 1 ? "Chairpersons" : str_plural($moderationType)) :
            str_singular($moderationType);
        $data = [
            "id"              => $this->id,
            "moderators"      => $moderators,
            "title"           => $this->title,
            "sponsor"         => $this->sponsor,
            "moderation_type" => $moderationType,
            "extra"           => $this->extra_attributes,
        ];

        if (isset($segments[3]) and $segments[3] === 'sessions') {
            $data['talks'] = TalkResource::collection($this->talks);
        }


        return $data;
    }
}
