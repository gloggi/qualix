<?php

namespace Tests\Unit\Services\Import\Blocks\ECamp3;

use App\Services\Import\Blocks\ECamp3\ECamp3BlockOverviewParser;
use Illuminate\Support\Collection;
use Tests\TestCase;

class ECamp3BlockOverviewParserTest extends TestCase
{
    /** @var ECamp3BlockOverviewParser */
    protected $parser;

    public function setUp(): void
    {
        parent::setUp();
        $this->parser = app()->make(ECamp3BlockOverviewParser::class);
    }

    private function getFilePath(string $filename): string
    {
        return base_path('tests/resources/' . $filename);
    }

    public function test_shouldParseECamp3BlockOverview_German()
    {
        $filePath = $this->getFilePath('ecamp3_de.pdf');

        if (!file_exists($filePath)) {
            $this->markTestSkipped('File ecamp3_de.pdf not found in tests/resources');
        }

        /** @var Collection $result */
        $result = $this->parser->parse($filePath);

        $this->assertEquals([
            ['full_block_number' => '1.1', 'day_number' => 1, 'block_number' => 1, 'name' => 'Ausbildung 1', 'block_date' => '13.09.2026'],
            ['full_block_number' => '2.1', 'day_number' => 2, 'block_number' => 1, 'name' => 'Ausbildung 2', 'block_date' => '14.09.2026'],
            ['full_block_number' => '2.2', 'day_number' => 2, 'block_number' => 2, 'name' => '67  GruStu', 'block_date' => '14.09.2026'],
            ['full_block_number' => '3.1', 'day_number' => 3, 'block_number' => 1, 'name' => 'Block', 'block_date' => '15.09.2026'],
            ['full_block_number' => '3.2', 'day_number' => 3, 'block_number' => 2, 'name' => 'Reisen', 'block_date' => '15.09.2026'],
            ['full_block_number' => '4.1', 'day_number' => 4, 'block_number' => 1, 'name' => 'Ausbildung 67', 'block_date' => '16.09.2026'],
            ['full_block_number' => '4.2', 'day_number' => 4, 'block_number' => 2, 'name' => 'RQF', 'block_date' => '16.09.2026'],
            ['full_block_number' => '5.1', 'day_number' => 5, 'block_number' => 1, 'name' => 'RF BLock', 'block_date' => '17.09.2026'],
            ['full_block_number' => '5.2', 'day_number' => 5, 'block_number' => 2, 'name' => 'RQF FFF  {', 'block_date' => '17.09.2026'],
            ['full_block_number' => '6.1', 'day_number' => 6, 'block_number' => 1, 'name' => 'Ausbildung 9.9', 'block_date' => '18.09.2026'],
        ], array_values($result->all()));
    }

    public function test_shouldParseECamp3BlockOverview_French()
    {
        $filePath = $this->getFilePath('ecamp3_fr.pdf');

        if (!file_exists($filePath)) {
            $this->markTestSkipped('File ecamp3_fr.pdf not found in tests/resources');
        }

        /** @var Collection $result */
        $result = $this->parser->parse($filePath);

        $this->assertEquals([
            ['full_block_number' => '1.1', 'day_number' => 1, 'block_number' => 1, 'name' => 'Ausbildung 1', 'block_date' => '13.09.2026'],
            ['full_block_number' => '2.1', 'day_number' => 2, 'block_number' => 1, 'name' => 'Ausbildung 2', 'block_date' => '14.09.2026'],
            ['full_block_number' => '2.2', 'day_number' => 2, 'block_number' => 2, 'name' => '67  GruStu', 'block_date' => '14.09.2026'],
            ['full_block_number' => '3.1', 'day_number' => 3, 'block_number' => 1, 'name' => 'Block', 'block_date' => '15.09.2026'],
            ['full_block_number' => '3.2', 'day_number' => 3, 'block_number' => 2, 'name' => 'Reisen', 'block_date' => '15.09.2026'],
            ['full_block_number' => '4.1', 'day_number' => 4, 'block_number' => 1, 'name' => 'Ausbildung 67', 'block_date' => '16.09.2026'],
            ['full_block_number' => '4.2', 'day_number' => 4, 'block_number' => 2, 'name' => 'RQF', 'block_date' => '16.09.2026'],
            ['full_block_number' => '5.1', 'day_number' => 5, 'block_number' => 1, 'name' => 'RF BLock', 'block_date' => '17.09.2026'],
            ['full_block_number' => '5.2', 'day_number' => 5, 'block_number' => 2, 'name' => 'RQF FFF  {', 'block_date' => '17.09.2026'],
            ['full_block_number' => '6.1', 'day_number' => 6, 'block_number' => 1, 'name' => 'Ausbildung 9.9', 'block_date' => '18.09.2026'],
        ], array_values($result->all()));
    }

    public function test_shouldParseECamp3BlockOverview_Italian()
    {
        $filePath = $this->getFilePath('ecamp3_it.pdf');

        if (!file_exists($filePath)) {
            $this->markTestSkipped('File ecamp3_it.pdf not found in tests/resources');
        }

        /** @var Collection $result */
        $result = $this->parser->parse($filePath);

        $this->assertEquals([
            ['full_block_number' => '1.1', 'day_number' => 1, 'block_number' => 1, 'name' => 'Ausbildung 1', 'block_date' => '13.09.2026'],
            ['full_block_number' => '2.1', 'day_number' => 2, 'block_number' => 1, 'name' => 'Ausbildung 2', 'block_date' => '14.09.2026'],
            ['full_block_number' => '2.2', 'day_number' => 2, 'block_number' => 2, 'name' => '67  GruStu', 'block_date' => '14.09.2026'],
            ['full_block_number' => '3.1', 'day_number' => 3, 'block_number' => 1, 'name' => 'Block', 'block_date' => '15.09.2026'],
            ['full_block_number' => '3.2', 'day_number' => 3, 'block_number' => 2, 'name' => 'Reisen', 'block_date' => '15.09.2026'],
            ['full_block_number' => '4.1', 'day_number' => 4, 'block_number' => 1, 'name' => 'Ausbildung 67', 'block_date' => '16.09.2026'],
            ['full_block_number' => '4.2', 'day_number' => 4, 'block_number' => 2, 'name' => 'RQF', 'block_date' => '16.09.2026'],
            ['full_block_number' => '5.1', 'day_number' => 5, 'block_number' => 1, 'name' => 'RF BLock', 'block_date' => '17.09.2026'],
            ['full_block_number' => '5.2', 'day_number' => 5, 'block_number' => 2, 'name' => 'RQF FFF  {', 'block_date' => '17.09.2026'],
            ['full_block_number' => '6.1', 'day_number' => 6, 'block_number' => 1, 'name' => 'Ausbildung 9.9', 'block_date' => '18.09.2026'],
        ], array_values($result->all()));
    }

    public function test_shouldParseECamp3BlockOverview_English()
    {
        $filePath = $this->getFilePath('ecamp3_en.pdf');

        if (!file_exists($filePath)) {
            $this->markTestSkipped('File ecamp3_en.pdf not found in tests/resources');
        }

        /** @var Collection $result */
        $result = $this->parser->parse($filePath);

        $this->assertEquals([
            ['full_block_number' => '1.1', 'day_number' => 1, 'block_number' => 1, 'name' => 'Ausbildung 1', 'block_date' => '13.09.2026'],
            ['full_block_number' => '2.1', 'day_number' => 2, 'block_number' => 1, 'name' => 'Ausbildung 2', 'block_date' => '14.09.2026'],
            ['full_block_number' => '2.2', 'day_number' => 2, 'block_number' => 2, 'name' => '67  GruStu', 'block_date' => '14.09.2026'],
            ['full_block_number' => '3.1', 'day_number' => 3, 'block_number' => 1, 'name' => 'Block', 'block_date' => '15.09.2026'],
            ['full_block_number' => '3.2', 'day_number' => 3, 'block_number' => 2, 'name' => 'Reisen', 'block_date' => '15.09.2026'],
            ['full_block_number' => '4.1', 'day_number' => 4, 'block_number' => 1, 'name' => 'Ausbildung 67', 'block_date' => '16.09.2026'],
            ['full_block_number' => '4.2', 'day_number' => 4, 'block_number' => 2, 'name' => 'RQF', 'block_date' => '16.09.2026'],
            ['full_block_number' => '5.1', 'day_number' => 5, 'block_number' => 1, 'name' => 'RF BLock', 'block_date' => '17.09.2026'],
            ['full_block_number' => '5.2', 'day_number' => 5, 'block_number' => 2, 'name' => 'RQF FFF  {', 'block_date' => '17.09.2026'],
            ['full_block_number' => '6.1', 'day_number' => 6, 'block_number' => 1, 'name' => 'Ausbildung 9.9', 'block_date' => '18.09.2026'],
        ], array_values($result->all()));
    }

    public function test_shouldParseEmptyECamp3BlockOverview()
    {
        $filePath = $this->getFilePath('ecamp3_empty.pdf');

        if (!file_exists($filePath)) {
            $this->markTestSkipped('File ecamp3_empty.pdf not found in tests/resources');
        }

        /** @var Collection $result */
        $result = $this->parser->parse($filePath);

        $this->assertEquals([], $result->all());
    }

    public function test_shouldParseECamp3BlockOverview_Rumantsch()
    {
        $filePath = $this->getFilePath('ecamp3_rm.pdf');

        if (!file_exists($filePath)) {
            $this->markTestSkipped('File ecamp3_rm.pdf not found in tests/resources');
        }

        /** @var Collection $result */
        $result = $this->parser->parse($filePath);

        $this->assertEquals([
            ['full_block_number' => '1.1', 'day_number' => 1, 'block_number' => 1, 'name' => 'Ausbildung 1', 'block_date' => '13.09.2026'],
            ['full_block_number' => '2.1', 'day_number' => 2, 'block_number' => 1, 'name' => 'Ausbildung 2', 'block_date' => '14.09.2026'],
            ['full_block_number' => '2.2', 'day_number' => 2, 'block_number' => 2, 'name' => '67  GruStu', 'block_date' => '14.09.2026'],
            ['full_block_number' => '3.1', 'day_number' => 3, 'block_number' => 1, 'name' => 'Block', 'block_date' => '15.09.2026'],
            ['full_block_number' => '3.2', 'day_number' => 3, 'block_number' => 2, 'name' => 'Reisen', 'block_date' => '15.09.2026'],
            ['full_block_number' => '4.1', 'day_number' => 4, 'block_number' => 1, 'name' => 'Ausbildung 67', 'block_date' => '16.09.2026'],
            ['full_block_number' => '4.2', 'day_number' => 4, 'block_number' => 2, 'name' => 'RQF', 'block_date' => '16.09.2026'],
            ['full_block_number' => '5.1', 'day_number' => 5, 'block_number' => 1, 'name' => 'RF BLock', 'block_date' => '17.09.2026'],
            ['full_block_number' => '5.2', 'day_number' => 5, 'block_number' => 2, 'name' => 'RQF FFF  {', 'block_date' => '17.09.2026'],
            ['full_block_number' => '6.1', 'day_number' => 6, 'block_number' => 1, 'name' => 'Ausbildung 9.9', 'block_date' => '18.09.2026'],
        ], array_values($result->all()));
    }
}
