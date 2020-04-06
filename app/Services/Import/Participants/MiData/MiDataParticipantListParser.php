<?php

namespace App\Services\Import\Participants\MiData;

use App\Exceptions\MiDataParticipantsListsParsingException;
use App\Services\DateCalculator;
use App\Services\Import\Participants\ParticipantListParser;

use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Row;

class MiDataParticipantListParser implements ParticipantListParser {
    /** @var PhpSpreadsheet\Reader\Xlsx */
    protected $reader;
    /** @var DateCalculator */
    protected $dateCalculator;

    private $year = 2000;

    public static $FIRST_ROW_WITH_DATA = 2;
    public static $COL_WITH_BLOCK_DESCRIPTION = 'A';
    public static $COL_WITH_BLOCK_DATE = 'B';
    public static $COL_WITH_FIRST_NAME = 'A';
    public static $COL_WITH_LAST_NAME = 'B';
    public static $COL_WITH_SCOUT_NAME = 'C';
    public static $COL_WITH_GROUP = 'M';

    protected static $WEEKDAYS = ['Mo' => 0, 'Di' => 1, 'Mi' => 2, 'Do' => 3, 'Fr' => 4, 'Sa' => 5, 'So' => 6];

    public function __construct(PhpSpreadsheet\Reader\Xlsx $reader) {
        $this->reader = $reader;
        $this->reader->setReadDataOnly(true);

    }

    /**
     * Parse the MiData Participants in the given file.
     *
     * @param string $filePath
     * @return Collection
     * @throws PhpSpreadsheet\Reader\Exception
     * @throws PhpSpreadsheet\Exception
     */
    public function parse(string $filePath) {
        return $this->readRows($filePath)->map(function (Row $row) {

            [$scout_name, $group] = $this->readCellsInRow($row);
            return array_merge(
                $this->parseName($scout_name, $row),
                $this->parseGroup($group)
            );

        });
    }

    /**
     * Read the rows in the given participants list from MiData.
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
        $cells = Collection::make($row->getCellIterator(self::$COL_WITH_SCOUT_NAME, self::$COL_WITH_GROUP));

        return [$cells[self::$COL_WITH_SCOUT_NAME], $cells[self::$COL_WITH_GROUP]];
    }

    /**
     * Parse the data in the "Pfadiname" column, if empty: concat first and last name
     *
     * @param PhpSpreadsheet\Cell\Cell $cell
     * @return array containing the extracted data
     */
    protected function parseName(PhpSpreadsheet\Cell\Cell $cell, Row $row) {
        $newName = "";
        if(empty($cell->getValue())){
            $newCells = Collection::make($row->getCellIterator(self::$COL_WITH_FIRST_NAME, self::$COL_WITH_LAST_NAME));
            $firstName = $newCells[self::$COL_WITH_FIRST_NAME]->getValue();
            $lastName = $newCells[self::$COL_WITH_LAST_NAME]->getValue();
            $newName =$firstName." ".$lastName;
        } else $newName=  $cell->getValue();
        return [
          'scout_name' => $newName
        ];
        /*
         *
         *         $name =$cells[self::$COL_WITH_SCOUT_NAME];
        dd($name->getValue());
        $regex = '/^\((?<full_block_number>(?<day_number>\d+)\.(?<block_number>\d+))\) (?<name>.*) \[[A-Za-z0-9.\/, ]*\]$/';
        if (preg_match($regex, $cell->getValue(), $matches) != 1) {
            throw new MiDataParticipantsListsParsingException(trans('t.views.admin.block_import.error_while_parsing'));
        }
        return [
            'name' => $matches['name'],
            'day_number' => $matches['day_number'],
            'block_number' => $matches['block_number'],
            'full_block_number' => $matches['full_block_number'],
        ];
        */
    }

    /**
     * Parse the data in the "Hauptebene" column.

     * @param PhpSpreadsheet\Cell\Cell $cell
     * @return array containing the extracted block date
     */
    protected function parseGroup(PhpSpreadsheet\Cell\Cell $cell) {
        return [
            'group' => $cell->getValue()
        ];
    }
}
