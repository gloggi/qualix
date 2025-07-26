<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;


class FeedbackAllocationRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'trainerCapacities' => 'required|array',
            'trainerCapacities.*' => 'required|array|size:2', // [trainerId, capacity]
            'trainerCapacities.*.0' => 'required|integer',
            'trainerCapacities.*.1' => 'required|integer|min:0', // Capacity >= 0 (to allow no allocations)

            'participantPreferences' => 'required|array',
            'participantPreferences.*' => 'required|array|min:1',
            'participantPreferences.*.0' => 'required|integer', // participantId
            'participantPreferences.*.*' => 'nullable|integer', // no preference is represented as null

            'numberOfWishes' => 'required|integer|min:0',

            'forbiddenWishes' => 'present|array',
            'forbiddenWishes.*' => 'required|array|size:2', // [participantId, trainerId]
            'forbiddenWishes.*.0' => 'required|integer',
            'forbiddenWishes.*.1' => 'required|integer',

            'defaultPriority' => 'sometimes|integer|min:1'
        ];
    }

    /**
     * Handle a passed validation attempt.
     *
     * @return void
     */
    public function prepareForValidation(): void
    {
        $this->merge([
            'defaultPriority' => $this->input('defaultPriority', 100),
        ]);
    }
}
