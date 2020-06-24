<?php

namespace App\Http\Requests;

use App\Models\Requirement;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Lang;

class BlockRequest extends FormRequest {
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
            'name' => 'required|max:255',
            'full_block_number' => 'regex:/^\d+\.\d+$/|nullable',
            'block_date' => 'date|required',
            'requirements' => 'nullable|regex:/^\d+(,\d+)*$/|allExistInCourse',
        ];
    }

    public function attributes() {
        return Lang::get('t.models.block');
    }
}
