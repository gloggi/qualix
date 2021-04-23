<?php

namespace Tests\Unit\Services\Import;

use App\Exceptions\UnsupportedFormatException;
use App\Services\Import\SpreadsheetReaderFactory;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use Tests\TestCase;

class SpreadsheetReaderFactoryTest extends TestCase {

    public function test_getReaderReturnsCorrectReader_forXLSFile() {
        // given
        /** @var SpreadsheetReaderFactory $factory */
        $factory = $this->app->make(SpreadsheetReaderFactory::class);

        // when
        $reader = $factory->getReader(__DIR__.'/../../../resources/Blockuebersicht.xls');

        // then
        $this->assertInstanceOf(Xls::class, $reader);
        $this->assertEquals(true, $reader->getReadDataOnly());
    }

    public function test_getReaderReturnsCorrectReader_forXLSXFile() {
        // given
        /** @var SpreadsheetReaderFactory $factory */
        $factory = $this->app->make(SpreadsheetReaderFactory::class);

        // when
        $reader = $factory->getReader(__DIR__.'/../../../resources/Blockuebersicht.xlsx');

        // then
        $this->assertInstanceOf(Xlsx::class, $reader);
        $this->assertEquals(true, $reader->getReadDataOnly());
    }

    public function test_getReaderReturnsCorrectReader_forCSVFile() {
        // given
        /** @var SpreadsheetReaderFactory $factory */
        $factory = $this->app->make(SpreadsheetReaderFactory::class);

        // when
        $reader = $factory->getReader(__DIR__.'/../../../resources/Blockuebersicht.csv');

        // then
        $this->assertInstanceOf(Csv::class, $reader);
        $this->assertEquals(true, $reader->getReadDataOnly());
        $this->assertEquals('CP1252', $reader->getInputEncoding());
    }

    public function test_getReaderDetectsEncoding_forCSVFile() {
        // given
        /** @var SpreadsheetReaderFactory $factory */
        $factory = $this->app->make(SpreadsheetReaderFactory::class);

        // when
        $reader = $factory->getReader(__DIR__.'/../../../resources/Blockuebersicht-utf8.csv');

        // then
        $this->assertEquals('UTF-8', $reader->getInputEncoding());
    }

    public function test_getReaderThrowsException_forInvalidFile() {
        // given
        /** @var SpreadsheetReaderFactory $factory */
        $factory = $this->app->make(SpreadsheetReaderFactory::class);

        // then
        $this->expectException(UnsupportedFormatException::class);

        // when
        $factory->getReader(__DIR__.'/../../../resources/invalid-format.xls');
    }
}
