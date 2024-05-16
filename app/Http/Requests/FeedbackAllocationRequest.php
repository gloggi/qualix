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
            'trainerCapacities.*.0' => 'required|integer',      // Trainer-ID als String
            'trainerCapacities.*.1' => 'required|integer|min:1', // Capacity als Integer > 0

            'participantPreferences' => 'required|array',
            'participantPreferences.*' => 'required|array',
            'participantPreferences.*.0' => 'required|integer',
            'participantPreferences.*.1:*' => 'required|string',

            'numberOfWishes' => 'required|integer|min:0',

            'forbiddenWishes' => 'present|array',
            'forbiddenWishes.*' => 'required|array|size:2', // [participantId, trainerId]
            'forbiddenWishes.*.0' => 'required|integer',
            'forbiddenWishes.*.1' => 'required|integer',

            'defaultPriority' => 'sometimes|integer|min:1'
        ];
    }
}
