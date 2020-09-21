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
            'qualiContents' => 'required|array',
            'qualiContents.type' => 'required|in:doc',
            'qualiContents.content' => 'present|array',
            'qualiContents.content.*.type' => 'required|in:paragraph,observation,requirement',
            'qualiContents.content.*.attrs.passed' => 'nullable|in:0,1,null',
            'qualiContents.content.*.attrs.id' => 'nullable|numeric',
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
