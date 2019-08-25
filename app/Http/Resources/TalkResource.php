<?php

namespace App\Http\Resources;

use App\Delegate;
use Illuminate\Http\Resources\Json\JsonResource;

class TalkResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request) {
        $segments = $request->segments();

        $speakers = $this->speakers->map(function (int $delegateId) {
            $speaker = Delegate::find($delegateId);

            return [
                'name' => $speaker->name
            ];
        });

        $data = [
            'topic'    => $this->title,
            'speakers' => $speakers,
            'extra'    => $this->extra_attributes
        ];
        if (isset($segments[3]) and $segments[3] === 'talks') {
            $data['session'] = new SessionResource($this->session);
        }

        return $data;
    }
}
