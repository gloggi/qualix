<?php

namespace App\Services\Import\Participants\MiData;

use App\Exceptions\MiDataParticipantsListsParsingException;
use App\Exceptions\UnsupportedFormatException;
use App\Services\Import\Participants\ParticipantListParser;
use App\Services\Import\SpreadsheetReaderFactory;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Row;

class MiDataParticipantListParser implements ParticipantListParser {
    /** @var SpreadsheetReaderFactory */
    protected $readerFactory;

    protected $firstRowWithData = 2;
    protected $lastRowWithData = 0;

    protected $firstNameCol;
    protected $lastNameCol;
    protected $scoutNameCol;
    protected $groupCol;
    protected $firstRelevantCol = null;
    protected $lastRelevantCol = null;

    public function __construct(SpreadsheetReaderFactory $readerFactory) {
        $this->readerFactory = $readerFactory;
    }

    /**
     * Parse the MiData Participants in the given file.
     *
     * @param string $filePath
     * @return Collection
     * @throws UnsupportedFormatException
     */
    public function parse(string $filePath) {
        return $this->readRows($filePath)->map(function (Row $row) {

            [$scoutName, $firstName, $lastName, $group] = $this->readCellsInRow($row);
            return array_merge(
                $this->parseName($scoutName, $firstName, $lastName),
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
     * @throws UnsupportedFormatException
     * @throws MiDataParticipantsListsParsingException if no names are found
     */
    protected function readRows($filePath) {
        $reader = $this->readerFactory->getReader($filePath);
        $worksheet = $reader->load($filePath)->getActiveSheet();
        $row = $worksheet->getRowIterator(1,1);
        $colIterator = $row->current()->getCellIterator();
        $scoutNameColName = trans('t.views.admin.participant_import.MiData.column_names.scout_name');
        $firstNameColName = trans('t.views.admin.participant_import.MiData.column_names.first_name');
        $lastNameColName = trans('t.views.admin.participant_import.MiData.column_names.last_name');
        $groupColName = trans('t.views.admin.participant_import.MiData.column_names.group');
        foreach ($colIterator as $col){
            $name = $col->getValue() . '';
            if(empty($this->scoutNameCol) && $name === $scoutNameColName){
                $this->scoutNameCol = $col->getColumn();
                $this->firstRelevantCol ??= $col->getColumn();
                $this->lastRelevantCol = $col->getColumn();
            }
            if(empty($this->firstNameCol) && $name === $firstNameColName){
                $this->firstNameCol = $col->getColumn();
                $this->firstRelevantCol ??= $col->getColumn();
                $this->lastRelevantCol = $col->getColumn();
            }
            if(empty($this->lastNameCol) && $name === $lastNameColName){
                $this->lastNameCol = $col->getColumn();
                $this->firstRelevantCol ??= $col->getColumn();
                $this->lastRelevantCol = $col->getColumn();
            }
            if(empty($this->groupCol) && $name === $groupColName){
                $this->groupCol = $col->getColumn();
                $this->firstRelevantCol ??= $col->getColumn();
                $this->lastRelevantCol = $col->getColumn();
            }
        }

        if (empty($this->scoutNameCol) && empty($this->firstNameCol) && empty($this->lastNameCol)) {
            throw new MiDataParticipantsListsParsingException(trans('t.views.admin.participant_import.error_while_parsing', [
                'scout_name' => $scoutNameColName,
                'first_name' => $firstNameColName,
                'last_name' => $lastNameColName
            ]));
        }

        $this->lastRowWithData = max(
            $worksheet->getHighestRow($this->firstNameCol) ?? 0,
            $worksheet->getHighestRow($this->lastNameCol) ?? 0,
            $worksheet->getHighestRow($this->scoutNameCol) ?? 0,
            $worksheet->getHighestRow($this->groupCol) ?? 0
        );
        if ($this->lastRowWithData < $this->firstRowWithData) {
            return Collection::make();
        }

        return Collection::make($worksheet->getRowIterator($this->firstRowWithData, $this->lastRowWithData));
    }

    /**
     * Read the relevant cells in the given row and return them.
     *
     * @param Row $row
     * @return array
     */
    protected function readCellsInRow(Row $row) {
        $cells = Collection::make($row->getCellIterator($this->firstRelevantCol, $this->lastRelevantCol));

        return [
            $cells[$this->scoutNameCol] ?? null,
            $cells[$this->firstNameCol] ?? null,
            $cells[$this->lastNameCol] ?? null,
            $cells[$this->groupCol] ?? null
        ];
    }

    /**
     * Parse the data in the "Pfadiname", "Vorname" and "Nachname" columns
     *
     * @param PhpSpreadsheet\Cell\Cell|null $scoutName
     * @param PhpSpreadsheet\Cell\Cell|null $firstName
     * @param PhpSpreadsheet\Cell\Cell|null $lastName
     * @return array containing the extracted data
     */
    protected function parseName(?PhpSpreadsheet\Cell\Cell $scoutName, ?PhpSpreadsheet\Cell\Cell $firstName, ?PhpSpreadsheet\Cell\Cell $lastName) {
        $name = $scoutName ? $scoutName->getValue() . '' : '';
        if(empty($name)){
            $firstName = $firstName ? $firstName->getValue() . '' : '';
            $lastName = $lastName ? $lastName->getValue() . '' : '';
            $name = implode(' ', array_filter([$firstName, $lastName]));
        }
        return [
          'scout_name' => $name
        ];
    }

    /**
     * Parse the data in the "Hauptebene" column.
     *
     * @param PhpSpreadsheet\Cell\Cell|null $cell
     * @return array containing the extracted block date
     */
    protected function parseGroup(?PhpSpreadsheet\Cell\Cell $cell) {
        return [
            'group' => $cell ? $cell->getValue() . '' : ''
        ];
    }
}
