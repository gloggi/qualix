<?php

namespace App\Services;

use App\Exceptions\ECamp2BlockOverviewParsingException;
use App\Models\Block;
use App\Models\Course;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet;

class ECamp2BlockOverviewImporter implements BlockListImporter
{
    protected $reader;

    private $year = 2000;

    public static $FIRST_ROW_WITH_DATA = 6;
    public static $COL_WITH_BLOCK_DESCRIPTION = 'A';
    public static $COL_WITH_BLOCK_DATE = 'B';

    public function __construct(PhpSpreadsheet\Reader\Xls $reader) {
        $this->reader = $reader;
        $this->reader->setReadDataOnly(true);
    }

    /**
     * Set the year to use for block dates, since it is not included in the block overview document.
     *
     * @param array $data
     * @return $this for fluid calls
     */
    public function setSupplementaryData(array $data) {
        $this->year = $data['year'];
        return $this;
    }

    /**
     * Parse the eCamp2 block overview and import the described blocks into the database.
     *
     * @param string $filePath
     * @param Course $course
     * @return Collection
     */
    public function import(string $filePath, Course $course) {
        return DB::transaction(function () use($filePath, $course) {
            $parsedBlocks = Collection::make();

            $sheet = $this->reader->load($filePath->getRealPath())->getActiveSheet();
            foreach($sheet->getRowIterator(self::$FIRST_ROW_WITH_DATA) as $row) {
                $cells = Collection::make($row->getCellIterator(self::$COL_WITH_BLOCK_DESCRIPTION, self::$COL_WITH_BLOCK_DATE));
                $blockDescription = $cells[self::$COL_WITH_BLOCK_DESCRIPTION]->getValue();
                // remove redundant weekday from beginning of date
                $blockDate = $this->year . ' ' . substr($cells[self::$COL_WITH_BLOCK_DATE]->getValue(), 4);
                if (preg_match('/^\((?<full_block_number>(?<day_number>\d+)\.(?<block_number>\d+))\) (?<name>.*) \[[A-Za-z0-9.\/, ]*\]$/', $blockDescription, $matches) != 1) {
                    throw new ECamp2BlockOverviewParsingException(trans('t.views.admin.block_import.error_while_parsing'));
                }
                $parsedBlocks->add([
                    'name' => $matches['name'],
                    'day_number' => $matches['day_number'],
                    'block_number' => $matches['block_number'],
                    'full_block_number' => $matches['full_block_number'],
                    'block_date' => Carbon::createFromFormat('Y d.m., +', $blockDate)->startOfDay(),
                    'course_id' => $course->id,
                ]);
            }
            return $parsedBlocks->map(function ($parsedBlock) {
                return Block::updateOrCreate([
                    'course_id' => $parsedBlock['course_id'],
                    'day_number' => $parsedBlock['day_number'],
                    'block_number' => $parsedBlock['block_number']], $parsedBlock);
            });
        });
    }
}
