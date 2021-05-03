<?php

namespace App\Services\Import;

use App\Exceptions\UnsupportedFormatException;
use PhpOffice\PhpSpreadsheet;

class SpreadsheetReaderFactory {

    /**
     * This method wraps the static PhpSpreadsheet\IOFactory::createReaderForFile method
     * so that it can be more easily mocked in tests.
     *
     * @param string $filePath
     * @return PhpSpreadsheet\Reader\IReader
     * @throws UnsupportedFormatException
     */
    public function getReader(string $filePath) {
        try {
            $reader = PhpSpreadsheet\IOFactory::createReaderForFile($filePath);
        } catch (PhpSpreadsheet\Reader\Exception $e) {
            throw new UnsupportedFormatException();
        }

        $reader->setReadDataOnly(true);

        if ($reader instanceof PhpSpreadsheet\Reader\Csv) {
            $reader->setInputEncoding(PhpSpreadsheet\Reader\Csv::guessEncoding($filePath));
        }

        return $reader;
    }
}
