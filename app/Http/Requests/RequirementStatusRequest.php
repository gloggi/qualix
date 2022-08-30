<?php

namespace App\Http\Requests;

use App\Models\RequirementStatus;
use Illuminate\Support\Facades\Lang;

class RequirementStatusRequest extends FormRequest {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'name' => 'required|max:255',
            'color' => 'required|in:' . implode(',', RequirementStatus::COLORS),
            'icon' => 'required|in:' . implode(',', RequirementStatus::ICONS),
        ];
    }

    public function attributes() {
        return Lang::get('t.models.requirement_status');
    }
}
