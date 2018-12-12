<?php

namespace App\Http\Requests;

use App\Delegate;
use App\Services\InputMutator;
use Illuminate\Foundation\Http\FormRequest;

class DelegateUpdateRequest extends FormRequest
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
        return (new Delegate)->getStoreRules();
    }

    public function validated() {

        return (new InputMutator(parent::validated()))
            ->boolean([
                'is_verified',
                'is_duplicated',
            ])
            ->get();

    }
}
