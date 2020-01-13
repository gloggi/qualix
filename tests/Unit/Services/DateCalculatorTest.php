<?php

namespace Tests\Unit\Services;

use App\Services\DateCalculator;
use Tests\TestCase;

class DateCalculatorTest extends TestCase {

    public function test_calculateYearFromWeekdayAndDate() {
        // given
        $examples = [
            2019 => [2019, 5, 1, 12], // basic test (Sat, 12th of January 2019)
            2020 => [2015, 6, 1, 12], // later year (Sun, 12th of January 2020)
            2030 => [2020, 5, 1, 12], // long skip due to leap years (Sat, 1st of January 2030)
            2016 => [2013, 0, 2, 29], // 29th of February (Mon, 29th of February 2016)
        ];
        $dateCalculator = new DateCalculator;

        foreach ($examples as $expected => $parameters) {

            // when
            $result = $dateCalculator->calculateYearFromWeekdayAndDate(...$parameters);

            // then
            $this->assertEquals($expected, $result);

        }
    }
}
