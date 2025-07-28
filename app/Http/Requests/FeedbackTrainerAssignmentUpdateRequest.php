<?php

namespace App\Http\Requests;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Lang;

class FeedbackTrainerAssignmentUpdateRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'feedbacks' => 'nullable|array',
            'feedbacks.*.users' => 'nullable|regex:/^\d+(,\d+)*$/|allExistInCourse:trainers,user_id',
        ];
    }

    public function attributes()
    {
        /** @var Collection $participantNames */
        $participantNames = $this->route('course')->participants->mapWithKeys(function ($participant) {
            return ['feedbacks.' . $participant->id . '.users' => trans('t.models.feedback.trainer_assignment', ['participant' => $participant->scout_name])];
        });
        return array_merge(Lang::get('t.models.feedback'), $participantNames->all());
    }
}
