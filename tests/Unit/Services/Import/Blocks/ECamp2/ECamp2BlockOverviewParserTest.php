<?php

namespace Tests\Unit\Services\Import\Blocks\ECamp2;

use App\Exceptions\ECamp2BlockOverviewParsingException;
use App\Services\DateCalculator;
use App\Services\Import\Blocks\ECamp2\ECamp2BlockOverviewParser;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Tests\ReadsSpreadsheets;
use Tests\TestCase;

class ECamp2BlockOverviewParserTest extends TestCase {

    use ReadsSpreadsheets;

    /** @var ECamp2BlockOverviewParser */
    protected $parser;

    public function test_shouldParseECamp2BlockOverview() {
        // given
        $this->mock(DateCalculator::class, function ($mock) {
            $mock->shouldReceive('calculateYearFromWeekdayAndDate')->andReturn(2020, 2020, 2021);
        });
        $this->setUpInputFile('Blockuebersicht.xls');
        /** @var ECamp2BlockOverviewParser $parser */
        $this->parser = app()->make(ECamp2BlockOverviewParser::class);

        // when
        /** @var Collection $result */
        $result = $this->parser->parse('path/to/file/doesnt/matter/we/are/using/mocks');

        // then
        $this->assertEquals([
            ['name' => 'Erster Block', 'block_date' => Carbon::create(2020, 03, 21), 'day_number' => '1', 'block_number' => '1', 'full_block_number' => '1.1'],
            ['name' => 'Zweiter Block am gleichen Tag', 'block_date' => Carbon::create(2020, 03, 21), 'day_number' => '1', 'block_number' => '2', 'full_block_number' => '1.2'],
            ['name' => 'Dritter Block am nächsten Tag', 'block_date' => Carbon::create(2021, 03, 22), 'day_number' => '2', 'block_number' => '1', 'full_block_number' => '2.1'],
            ['name' => 'Mehrstellige Blocknummer', 'block_date' => Carbon::create(2021, 03, 23), 'day_number' => '10', 'block_number' => '10', 'full_block_number' => '10.10']
        ], array_values($result->all()));
    }

    public function test_shouldParseEmptyECamp2BlockOverview() {
        // given
        $this->mock(DateCalculator::class, function ($mock) {
            $mock->shouldReceive('calculateYearFromWeekdayAndDate')->andReturn(2020, 2020, 2021);
        });
        $this->setUpInputFile('Blockuebersicht-empty.xls');
        /** @var ECamp2BlockOverviewParser $parser */
        $this->parser = app()->make(ECamp2BlockOverviewParser::class);

        // when
        /** @var Collection $result */
        $result = $this->parser->parse('path/to/file/doesnt/matter/we/are/using/mocks');

        // then
        $this->assertEquals([], $result->all());
    }

    public function test_shouldThrowECamp2BlockOverviewParsingException_whenColumnsNotAsExpected() {
        // given
        $this->mock(DateCalculator::class, function ($mock) {
            $mock->shouldReceive('calculateYearFromWeekdayAndDate')->andReturn(2020, 2020, 2021);
        });
        $this->setUpInputFile('Blockuebersicht-cutOff.xls');
        /** @var ECamp2BlockOverviewParser $parser */
        $this->parser = app()->make(ECamp2BlockOverviewParser::class);

        // then
        $this->expectException(ECamp2BlockOverviewParsingException::class);

        // when
        $this->parser->parse('path/to/file/doesnt/matter/we/are/using/mocks');
    }

    public function test_shouldThrowECamp2BlockOverviewParsingException_whenBezeichnungIsMalformed() {
        // given
        $this->mock(DateCalculator::class, function ($mock) {
            $mock->shouldReceive('calculateYearFromWeekdayAndDate')->andReturn(2020);
        });
        $this->setUpInputFile('Blockuebersicht-badBezeichnung.xls');
        /** @var ECamp2BlockOverviewParser $parser */
        $this->parser = app()->make(ECamp2BlockOverviewParser::class);

        // then
        $this->expectException(ECamp2BlockOverviewParsingException::class);

        // when
        $this->parser->parse('path/to/file/doesnt/matter/we/are/using/mocks');
    }

    public function test_shouldThrowECamp2BlockOverviewParsingException_whenDatumUndZeitIsMalformed() {
        // given
        $this->mock(DateCalculator::class, function ($mock) {
            $mock->shouldReceive('calculateYearFromWeekdayAndDate')->andReturn(2020);
        });
        $this->setUpInputFile('Blockuebersicht-badDatumUndZeit.xls');
        /** @var ECamp2BlockOverviewParser $parser */
        $this->parser = app()->make(ECamp2BlockOverviewParser::class);

        // then
        $this->expectException(ECamp2BlockOverviewParsingException::class);

        // when
        $this->parser->parse('path/to/file/doesnt/matter/we/are/using/mocks');
    }

}
