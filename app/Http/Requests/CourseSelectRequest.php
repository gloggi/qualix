<?php

namespace App\Http\Requests;

use App\Models\Leiter;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CourseSelectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Leiter::where('kurs_id', '=', $this->kurs->id)->where('user_id', '=', Auth::user()->getAuthIdentifier())->exists();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
          //
        ];
    }
}
