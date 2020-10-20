<?php

namespace App\Http\Requests;

class ErrorReportRequest extends FormRequest {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255',
            'description' => 'required|max:2048',
            'previousUrl' => 'url',
            'eventId' => 'required',
        ];
    }
}
