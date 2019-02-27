<?php

namespace App\Http\Requests;

use App\Delegate;
use App\Transaction;
use Illuminate\Foundation\Http\FormRequest;

class StoreDelegateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return !!auth()->user();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return array_merge((new Delegate)->getStoreRules(),
            (new Transaction)->getRules());
    }

    public function validated() {
        return $this->convertData(parent::validated());
    }

    private function convertData(array $data) {
        $data['institution'] = $data['other_institution'] ?? $data['institution'];
        $data['training_organisation'] = $data['training_other_organisation'] ?? $data['training_organisation'];

        return $data;
    }
}
