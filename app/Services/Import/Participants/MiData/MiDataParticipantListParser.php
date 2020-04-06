<?php

namespace App\Services\Import\Participants\MiData;

use App\Exceptions\MiDataParticipantsListsParsingException;
use App\Services\DateCalculator;
use App\Services\Import\Participants\ParticipantListParser;


use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Row;

class MiDataParticipantListParser implements ParticipantListParser {
    /** @var PhpSpreadsheet\Reader\Xlsx */
    protected $reader;


    public static $FIRST_ROW_WITH_DATA = 2;

    public static $COL_WITH_FIRST_NAME;
    public static $COL_WITH_LAST_NAME;
    public static $COL_WITH_SCOUT_NAME ;
    public static $COL_WITH_GROUP;




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
     * Dynamically set COLUMNS
     *
     * @param $filePath
     * @return Collection of rows
     * @throws PhpSpreadsheet\Exception
     * @throws PhpSpreadsheet\Reader\Exception
     * @throws MiDataParticipantsListsParsingException if no names are found
     */
    protected function readRows($filePath) {
        $worksheet = $this->reader->load($filePath)->getActiveSheet();
        $row = $worksheet->getRowIterator(1,1);
        $colIterator = $row->current()->getCellIterator();
        foreach ($colIterator as $col){
            $name = $col->getValue();
            if(empty(self::$COL_WITH_SCOUT_NAME)&&$name == trans('t.views.admin.participant_import.MiData.column_names.scout_name')){
                self::$COL_WITH_SCOUT_NAME=$col->getColumn();
            }
            if(empty(self::$COL_WITH_FIRST_NAME)&&$name == trans('t.views.admin.participant_import.MiData.column_names.first_name')){
                self::$COL_WITH_FIRST_NAME=$col->getColumn();
            }
            if(empty(self::$COL_WITH_LAST_NAME)&&$name == trans('t.views.admin.participant_import.MiData.column_names.last_name')){
                self::$COL_WITH_LAST_NAME=$col->getColumn();
            }
            if(empty(self::$COL_WITH_GROUP)&&$name == trans('t.views.admin.participant_import.MiData.column_names.group')){
                self::$COL_WITH_GROUP=$col->getColumn();
            }
        }

        if(empty(self::$COL_WITH_SCOUT_NAME)&&empty(self::$COL_WITH_FIRST_NAME)&&empty(self::$COL_WITH_LAST_NAME)){
            throw new MiDataParticipantsListsParsingException;
        }

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
