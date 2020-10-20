<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Lang;

class InvitationRequest extends FormRequest {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'email' => 'required|email|max:50',
        ];
    }

    public function attributes() {
        return Lang::get('t.models.invitation');
    }
}
