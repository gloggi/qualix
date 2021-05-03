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

            [$scoutName, $group] = $this->readCellsInRow($row);
            return array_merge(
                $this->parseName($scoutName, $row),
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
        foreach ($colIterator as $col){
            $name = $col->getValue() . '';
            if(empty($this->scoutNameCol) && $name === trans('t.views.admin.participant_import.MiData.column_names.scout_name')){
                $this->scoutNameCol = $col->getColumn();
            }
            if(empty($this->firstNameCol) && $name === trans('t.views.admin.participant_import.MiData.column_names.first_name')){
                $this->firstNameCol = $col->getColumn();
            }
            if(empty($this->lastNameCol) && $name === trans('t.views.admin.participant_import.MiData.column_names.last_name')){
                $this->lastNameCol = $col->getColumn();
            }
            if(empty($this->groupCol) && $name === trans('t.views.admin.participant_import.MiData.column_names.group')){
                $this->groupCol = $col->getColumn();
            }
        }

        if(empty($this->scoutNameCol) && empty($this->firstNameCol) && empty($this->lastNameCol)){
            throw new MiDataParticipantsListsParsingException(trans('t.views.admin.participant_import.error_while_parsing'));
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
        $cells = Collection::make($row->getCellIterator($this->scoutNameCol, $this->groupCol));

        return [$cells[$this->scoutNameCol], $cells[$this->groupCol]];
    }

    /**
     * Parse the data in the "Pfadiname" column, if empty: concat first and last name
     *
     * @param PhpSpreadsheet\Cell\Cell $cell
     * @param Row $row
     * @return array containing the extracted data
     */
    protected function parseName(PhpSpreadsheet\Cell\Cell $cell, Row $row) {
        $name = $cell->getValue() . '';
        if(empty($name)){
            $firstName = $this->extractCellFromRow($row, $this->firstNameCol);
            $lastName = $this->extractCellFromRow($row, $this->lastNameCol);
            $name = implode(' ', array_filter([$firstName, $lastName]));
        }
        return [
          'scout_name' => $name
        ];
    }

    protected function extractCellFromRow(Row $row, $colName = null) {
        return $colName === null ? null : '' . $row->getCellIterator($colName, $colName)->current()->getValue();
    }

    /**
     * Parse the data in the "Hauptebene" column.
     *
     * @param PhpSpreadsheet\Cell\Cell $cell
     * @return array containing the extracted block date
     */
    protected function parseGroup(PhpSpreadsheet\Cell\Cell $cell) {
        return [
            'group' => $cell->getValue() . ''
        ];
    }
}
