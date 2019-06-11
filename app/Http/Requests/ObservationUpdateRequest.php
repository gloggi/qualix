<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ObservationUpdateRequest extends FormRequest
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
            'content' => 'required',
            'impression' => 'required|in:0,1,2',
            'block_id' => 'required|numeric',
            'requirement_ids' => 'regex:/^\d+(,\d+)*$/|nullable',
            'category_ids' => 'regex:/^\d+(,\d+)*$/|nullable',
        ];
    }
}
