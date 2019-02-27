<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CheckinDelegateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request) {
        return [
            'first_name' => $this->first_name,
            'last_name'  => $this->first_name,
            'ticket'     => ($transaction = $this->transactions->first()) ? $transaction->ticket->name : null,
            'role'       => $this->roles->map(function (DelegateRole $role
            ) {
                return $role->label;
            })->reduce(function ($carry, $roleLabel) {
                return $carry .= $roleLabel . ", ";
            }, ""),
            'check_in'   => null,
        ];
    }
}
