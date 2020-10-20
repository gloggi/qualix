<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Lang;

class QualiContentRequest extends FormRequest {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'quali_contents' => 'required|validQualiContent',
        ];
    }

    public function attributes() {
        return Lang::get('t.models.quali');
    }

    /**
     * Handle a passed validation attempt.
     *
     * @return void
     */
    protected function prepareForValidation() {
        parent::prepareForValidation();
        $this->merge(['quali_contents' => json_decode($this->get('quali_contents'), true)]);
    }
}
