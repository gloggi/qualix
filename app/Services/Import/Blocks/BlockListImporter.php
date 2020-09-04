<?php

namespace App\Services\Import\Blocks;

use App\Models\Block;
use App\Models\Course;
use App\Services\Import\Blocks\BlockListParser;
use Illuminate\Support\Collection;

abstract class BlockListImporter {

    /** @var BlockListParser */
    protected $parser;

    public function __construct(BlockListParser $parser) {
        $this->parser = $parser;
    }

    /**
     * Reads blocks from an uploaded file and saves them to the database.
     * In case a full_block_number already exists in the database, the existing block will be updated instead.
     *
     * @param string $filePath path to input file containing a description of some blocks
     * @param Course $course course into which the blocks are imported
     * @return Collection list of blocks that were imported to the database
     */
    public function import(string $filePath, Course $course) {
        $parsedBlocks = $this->parser->parse($filePath);

        return $parsedBlocks->map(function ($parsedBlock) use($course) {
            $parsedBlock['name'] = mb_substr($parsedBlock['name'], 0, 255);
            return Block::updateOrCreate([
                'course_id' => $course->id,
                'day_number' => $parsedBlock['day_number'],
                'block_number' => $parsedBlock['block_number']], $parsedBlock);
        });
    }
}
