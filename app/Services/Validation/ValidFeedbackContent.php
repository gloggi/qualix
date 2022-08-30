<?php

namespace App\Services\Validation;

use App\Models\Course;
use App\Models\Participant;
use App\Services\TiptapFormatter;
use Illuminate\Routing\Route;
use Illuminate\Validation\Validator;

class ValidFeedbackContent {

    /** @var Course $course */
    protected $course;
    /** @var Participant|null $participant */
    protected $participant;

    public function __construct(Route $route) {
        $this->course = $route->parameter('course');
        $this->participant = $route->parameter('participant');
    }

    protected function getObservations() {
        return $this->participant->participant_observations()->pluck('id');
    }

    protected function getRequirements() {
        return $this->course->requirements()->pluck('id');
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param $attribute
     * @param mixed $value
     * @param $parameters
     * @param $validator Validator
     * @return bool
     */
    public function validate($attribute, $value, $parameters, $validator) {
        if (!is_array($value)) return false;

        $requirements = $this->getRequirements();
        $observations = $this->getObservations();

        return app(TiptapFormatter::class)::isValid($value, $requirements, $observations, $this->course->requirement_statuses()->pluck('id'));
    }

}
