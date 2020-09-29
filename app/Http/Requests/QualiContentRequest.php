<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'qualiContents' => 'required|validQualiContent',
        ];
    }

    /**
     * Handle a passed validation attempt.
     *
     * @return void
     */
    protected function prepareForValidation() {
        $this->merge(['qualiContents' => json_decode($this->get('qualiContents'), true)]);
    }
}
