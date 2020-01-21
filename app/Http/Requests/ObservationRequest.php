<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ObservationRequest extends FormRequest
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
            'participant_ids' => 'required|regex:/^\d+(,\d+)*$/',
            'content' => 'required|max:1023',
            'impression' => 'required|in:0,1,2',
            'block_id' => 'required|numeric',
            'requirement_ids' => 'regex:/^\d+(,\d+)*$/|nullable',
            'category_ids' => 'regex:/^\d+(,\d+)*$/|nullable',
        ];
    }
}
