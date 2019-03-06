<?php

namespace App\Http\Requests;

use App\Delegate;
use App\Services\AdminCreateDataTransformer;
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
        $rules = array_merge((new Delegate)->getStoreRules(),
            (new Transaction)->getRules());

        $rules = AdminCreateDataTransformer::transformRules($rules);

        return $rules;
    }

    public function validated() {
        return $this->convertData(parent::validated());
    }

    private function convertData(array $data) {
        $data = AdminCreateDataTransformer::transformInputs($data);

        $data['institution'] = $data['other_institution'] ?? $data['institution'];
        $data['training_organisation'] = $data['training_other_organisation'] ?? ($data['training_organisation'] ?? null);
        $data['position'] = $data['position'] == "Others" ? $data['other_position'] : $data['position'];

        return $data;
    }
}
