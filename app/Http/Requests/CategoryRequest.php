<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Lang;

class CategoryRequest extends FormRequest {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'name' => 'required|max:255',
        ];
    }

    public function attributes() {
        return Lang::get('t.models.category');
    }
}
