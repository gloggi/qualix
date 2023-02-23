<?php

namespace Tests;

use App\Services\Import\SpreadsheetReaderFactory;
use Illuminate\Http\UploadedFile;
use Mockery;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Xls;

trait ReadsSpreadsheets
{
    protected function setUpInputFile($filename, $reader = null) {
        $reader = $reader ?? new Xls();
        $reader->setReadDataOnly(true);
        if ($reader instanceof Csv) {
            $reader->setInputEncoding(Csv::guessEncoding(__DIR__.'/resources/'.$filename));
        }
        $spreadsheet = $reader->load(__DIR__.'/resources/' . $filename);

        $readerMock = Mockery::mock(Xls::class, function ($mock) use ($spreadsheet) {
            $mock->shouldReceive('load')->andReturn($spreadsheet);
        })->makePartial();

        $factoryMock = Mockery::mock(SpreadsheetReaderFactory::class, function ($mock) use ($readerMock) {
            $mock->shouldReceive('getReader')->andReturn($readerMock);
        });

        $this->instance(SpreadsheetReaderFactory::class, $factoryMock);

        return UploadedFile::fake()->create($filename);
    }
}
