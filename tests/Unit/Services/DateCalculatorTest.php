<?php

namespace Tests\Unit\Services;

use App\Services\DateCalculator;
use Tests\TestCase;

class DateCalculatorTest extends TestCase {
    /**
     * @dataProvider getExamples
     * @param $expected int expected outcome of the date calculation
     * @param $parameters array input for the date calculator
     */
    public function test_calculateYearFromWeekdayAndDate($expected, $parameters) {
        // given
        $dateCalculator = new DateCalculator;

        // when
        $result = $dateCalculator->calculateYearFromWeekdayAndDate(...$parameters);

        // then
        $this->assertEquals($expected, $result);
    }

    public function getExamples() {
        return [
            'basic test (Sat, 12th of January 2019)' => [2019, [2019, 5, 1, 12]],
            'later year (Sun, 12th of January 2020)' => [2020, [2015, 6, 1, 12]],
            'long skip due to leap years (Sat, 1st of January 2030)' => [2030, [2020, 5, 1, 12]],
            '29th of February (Mon, 29th of February 2016)' => [2016, [2013, 0, 2, 29]],
        ];
    }
}
