<?php

namespace App\Http\Resources;

use App\DelegateRole;
use Illuminate\Http\Resources\Json\JsonResource;

class CheckinTransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request) {

        return [
            'event'     => $this->ticket->event->title,
            'delegate'  => new DelegateResource($this->payee),
            'ticket'    => $this->ticket->name,
            'check_in'  => $this->getCheckInRecords(),
        ];
    }
}
