<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Lang;

class QualiContentRequest extends FormRequest {
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
        $this->merge(['quali_contents' => json_decode($this->get('quali_contents'), true)]);
    }
}
