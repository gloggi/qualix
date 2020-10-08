<?php

namespace App\Services\Import\Blocks\ECamp2;

use App\Exceptions\ECamp2BlockOverviewParsingException;
use App\Services\DateCalculator;
use App\Services\Import\Blocks\BlockListParser;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Row;

class ECamp2BlockOverviewParser implements BlockListParser {
    /** @var PhpSpreadsheet\Reader\Xls */
    protected $reader;
    /** @var DateCalculator */
    protected $dateCalculator;

    private $year = 2000;

    public static $FIRST_ROW_WITH_DATA = 6;
    public static $COL_WITH_BLOCK_DESCRIPTION = 'A';
    public static $COL_WITH_BLOCK_DATE = 'B';

    protected static $WEEKDAYS = ['Mo' => 0, 'Di' => 1, 'Mi' => 2, 'Do' => 3, 'Fr' => 4, 'Sa' => 5, 'So' => 6];

    public function __construct(PhpSpreadsheet\Reader\Xls $reader, DateCalculator $dateCalculator) {
        $this->reader = $reader;
        $this->reader->setReadDataOnly(true);

        // Since the eCamp2 block overview doesn't mention the year, we have to guess it using the
        // weekdays and dates and the fact that the blocks are exported in chronological order.
        // For the guess, assume the user imports blocks from courses not older than last year.
        $this->year = Carbon::now()->year - 1;

        $this->dateCalculator = $dateCalculator;
    }

    /**
     * Parse the eCamp2 block overview in the given file.
     *
     * @param string $filePath
     * @return Collection
     * @throws PhpSpreadsheet\Reader\Exception
     * @throws PhpSpreadsheet\Exception
     */
    public function parse(string $filePath) {
        return $this->readRows($filePath)->map(function (Row $row) {

            [$bezeichnung, $datumUndZeit] = $this->readCellsInRow($row);

            return array_merge(
                $this->parseBezeichnung($bezeichnung),
                $this->parseDatumUndZeit($datumUndZeit)
            );
        });
    }

    /**
     * Read the rows in the given block overview file from eCamp2.
     *
     * @param $filePath
     * @return Collection of rows
     * @throws PhpSpreadsheet\Exception
     * @throws PhpSpreadsheet\Reader\Exception
     */
    protected function readRows($filePath) {
        $worksheet = $this->reader->load($filePath)->getActiveSheet();
        if ($worksheet->getHighestRow() < self::$FIRST_ROW_WITH_DATA) {
            return Collection::make();
        }
        return Collection::make($worksheet->getRowIterator(self::$FIRST_ROW_WITH_DATA));
    }

    /**
     * Read the relevant cells in the given row and return them.
     *
     * @param Row $row
     * @return array
     */
    protected function readCellsInRow(Row $row) {
        $cells = Collection::make($row->getCellIterator(self::$COL_WITH_BLOCK_DESCRIPTION, self::$COL_WITH_BLOCK_DATE));
        return [$cells[self::$COL_WITH_BLOCK_DESCRIPTION], $cells[self::$COL_WITH_BLOCK_DATE]];
    }

    /**
     * Parse the data in the "Bezeichnung" column
     *
     * @param PhpSpreadsheet\Cell\Cell $cell
     * @return array containing the extracted data
     */
    protected function parseBezeichnung(PhpSpreadsheet\Cell\Cell $cell) {
        $regex = '/^\((?<full_block_number>(?<day_number>\d+)\.(?<block_number>\d+))\) (?<name>.*) \[[A-Za-z0-9.\/, ]*\]$/';
        if (preg_match($regex, $cell->getValue(), $matches) != 1) {
            throw new ECamp2BlockOverviewParsingException(trans('t.views.admin.block_import.error_while_parsing'));
        }
        return [
            'name' => $matches['name'],
            'day_number' => $matches['day_number'],
            'block_number' => $matches['block_number'],
            'full_block_number' => $matches['full_block_number'],
        ];
    }

    /**
     * Parse the data in the "Datum und Zeit" column.
     * The year for the block will be guessed based on the year of the previous blocks and the weekday and date.
     * This is necessary because the year is not part of the imported file.
     *
     * @param PhpSpreadsheet\Cell\Cell $cell
     * @return array containing the extracted block date
     */
    protected function parseDatumUndZeit(PhpSpreadsheet\Cell\Cell $cell) {
        $regex = '/^(?<weekday>Mo|Di|Mi|Do|Fr|Sa|So), (?<day>[0-3]?[0-9])\.(?<month>[0-1]?[0-9])\./';
        if (preg_match($regex, $cell->getValue(), $matches) != 1 || !Arr::has(self::$WEEKDAYS, $matches['weekday'])) {
            throw new ECamp2BlockOverviewParsingException(trans('t.views.admin.block_import.error_while_parsing'));
        }

        $weekday = self::$WEEKDAYS[$matches['weekday']];
        // The blocks are in chronological order, so all later blocks will have at least the same year number
        $this->year = $this->dateCalculator->calculateYearFromWeekdayAndDate($this->year, $weekday, $matches['month'], $matches['day']);

        return [
            'block_date' => Carbon::create($this->year, $matches['month'], $matches['day']),
        ];
    }
}
