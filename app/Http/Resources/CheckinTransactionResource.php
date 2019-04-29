<?php

namespace App\Http\Resources;

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
            'event'    => $this->ticket->event->title,
            'delegate' => [
                'registration_id' => $this->payee->getRegistrationId(),
                'name'            => $this->payee->name,
                'email'           => $this->payee->email,
                'mobile'          => $this->payee->mobile,
                'role'            => implode(", ",
                    $this->payee->roles()->pluck('label')->toArray()),
                'institution'     => $this->payee->institution,
                'position'        => $this->payee->position,
                'department'      => $this->payee->department,
                'sponsor'         => ($record = $this->payee->sponsorRecord) ? $record->sponsor : null
            ],
            'ticket'   => $this->ticket->name,
            'check_in' => $this->getCheckInRecords(),
        ];
    }
}
