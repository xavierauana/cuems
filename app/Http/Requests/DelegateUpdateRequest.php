<?php

namespace App\Http\Requests;

use App\Delegate;
use App\Enums\DelegateDuplicationStatus;
use App\Services\InputMutator;
use App\Transaction;
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
        return array_merge(
            (new Delegate)->getStoreRules(),
            (new Transaction)->getRules()
        );
    }

    public function validated() {

        $data = (new InputMutator(parent::validated()))
            ->boolean([
                'is_verified',
            ])
            ->get();

        $data['is_duplicated'] = $data['is_duplicated'] ?? DelegateDuplicationStatus::NO;

        return $data;

    }
}
