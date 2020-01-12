<?php

namespace App\Services;

use Illuminate\Support\Carbon;

class DateCalculator
{
    /**
     * Calculate the earliest year greater or equal to $startYear, in which the given date is the given weekday.
     *
     * @param $startYear
     * @param $weekday
     * @param $month
     * @param $day
     * @return integer year number
     */
    function calculateYearFromWeekdayAndDate($startYear, $weekday, $month, $day) {
        while(true) {
            $date = Carbon::create($startYear, $month, $day);
            if ($date->weekday() == $weekday) {
                return $startYear;
            }
            $startYear++;
        }
        // Useless return for static analysis
        return 0;
    }
}
