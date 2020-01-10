<?php

namespace App\Services;

use App\Models\Course;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;

interface BlockListImporter
{


    /**
     * Set more data that might be needed for the conversion.
     *
     * @param array $data
     * @return $this for fluid calls
     */
    public function setSupplementaryData(array $data);

    /**
     * Imports blocks from an uploaded file and saves them to the database.
     * In case a full_block_number already exists in the database, the existing block will be updated instead.
     *
     * @param UploadedFile $blockList input file containing a description of some blocks
     * @param Course $course course into which the blocks are imported
     * @return Collection list of blocks that were imported to the database
     */
    public function import(UploadedFile $blockList, Course $course);
}
