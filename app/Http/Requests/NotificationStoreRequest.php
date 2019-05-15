<?php

namespace App\Http\Requests;

use App\Notification;
use App\Services\InputMutator;
use Illuminate\Foundation\Http\FormRequest;

class NotificationStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return (new Notification)->getStoreRules();
    }

    public function validated() {

        return (new InputMutator(parent::validated()))
            ->boolean([
                'include_ticket',
                'verified_only',
                'include_duplicated',
            ])
            ->date("schedule", 'd M Y H:i')
            ->get();

    }
}
