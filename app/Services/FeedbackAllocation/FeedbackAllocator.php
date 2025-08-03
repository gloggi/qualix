<?php

namespace App\Services\FeedbackAllocation;

interface FeedbackAllocator
{

    /**
     * Tries to allocate feedbacks to participants based on their preferences and trainer capacities.
     *
     * @param array $trainerCapacities capacities of trainers
     * @param array $participantPreferences preferences of participants
     * @param int $numberOfWishes number of wishes each participant can have
     * @param array $forbiddenWishes list of forbidden wishes
     * @param int $defaultPriority default priority for wishes
     * @return array list of allocated feedbacks
     */
    public function tryToAllocateFeedbacks(array $trainerCapacities, array $participantPreferences, int $numberOfWishes, array $forbiddenWishes, int $defaultPriority = 100, bool $unweighted = false): array;
}
