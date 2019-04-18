<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'abteilung' => '',
            'bild' => 'nullable|image|max:2000',
        ];
    }

    /**
     * Get the validated data from the request.
     *
     * @return array
     */
    public function validated() {
        $validated = parent::validated();
        if (isset($validated['bild'])) {
            $validated['bild_url'] = $validated['bild']->store('public/images');
            unset($validated['bild']);
        }
        return $validated;
    }
}
