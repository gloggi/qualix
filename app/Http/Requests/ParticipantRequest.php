<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Lang;

class ParticipantRequest extends FormRequest {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'scout_name' => 'required|max:255',
            'group' => 'max:255',
            'image' => 'nullable|image|max:2000',
            'remove_image' => 'nullable|boolean',
            'freetext' => 'nullable|max:65535',
        ];
    }

    /**
     * Get the validated data from the request.
     *
     * @param null $key
     * @param null $default
     * @return array
     */
    public function validated($key = null, $default = null) {
        $validated = parent::validated($key, $default);
        if (isset($validated['image'])) {
            $validated['image_url'] = $validated['image']->store('public/images');
            unset($validated['image']);
        } elseif ($this->boolean('remove_image')) {
            $validated['image_url'] = null;
        }
        unset($validated['remove_image']);
        return $validated;
    }

    public function attributes() {
        return Lang::get('t.models.participant');
    }
}
