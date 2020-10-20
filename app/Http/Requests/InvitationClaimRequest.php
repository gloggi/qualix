<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Lang;

class InvitationClaimRequest extends FormRequest {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'token' => 'required|max:128',
        ];
    }

    public function attributes() {
        return Lang::get('t.models.invitation_claim');
    }
}
