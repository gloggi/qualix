<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TNStoreRequest extends FormRequest
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
            'pfadiname' => 'required',
            'abteilung' => '',
            'bild' => 'nullable|image|max:2000',
        ];
    }

    public function validated()
    {
        $return =  parent::validated();

        if (isset($return['bild'])) {
            $path = $return['bild']->store('public/images');
            $return['bild_url'] = $path;
        }

        return $return;
    }
}
