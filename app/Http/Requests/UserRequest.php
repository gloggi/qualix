<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Lang;

class UserRequest extends FormRequest {
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
            'name' => 'required|max:30',
            'group' => 'max:255',
            'image' => 'nullable|image|max:2000',
        ];
    }

    /**
     * Get the validated data from the request.
     *
     * @return array
     */
    public function validated() {
        $validated = parent::validated();
        if (isset($validated['image'])) {
            $validated['image_url'] = $validated['image']->store('public/images');
            unset($validated['image']);
        }
        return $validated;
    }

    public function attributes() {
        return Lang::get('t.models.user');
    }
}
