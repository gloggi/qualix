<?php

namespace App\Services\Import\Blocks;

use App\Models\Course;
use Illuminate\Support\Collection;

interface BlockListParser {

    /**
     * Parses blocks from an uploaded file and returns the data in an array.
     *
     * @param string $filePath path to input file containing a description of some blocks
     * @return Collection list of blocks that were imported to the database
     */
    public function parse(string $filePath);
}
