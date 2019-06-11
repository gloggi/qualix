<?php

namespace Tests;

use App\Models\Block;
use App\Models\Requirement;
use App\Models\Category;
use App\Models\Participant;

abstract class TestCaseWithKurs extends TestCase
{
    protected $courseId;

    public function setUp(): void {
        parent::setUp();

        $this->courseId = $this->createKurs();
    }

    protected function createTN($scout_name = 'Pflock', $courseId = null) {
        return Participant::create(['course_id' => ($courseId !== null ? $courseId : $this->courseId), 'scout_name' => $scout_name])->id;
    }

    protected function createBlock($blockname = 'Block 1', $fullBlockNumber = '1.1', $date = '01.01.2019', $ma_ids = null, $courseId = null) {
        return Block::create(['course_id' => ($courseId !== null ? $courseId : $this->courseId), 'full_block_number' => $fullBlockNumber, 'name' => $blockname, 'block_date' => $date, 'requirement_ids' => $ma_ids])->id;
    }

    protected function createCategory($name = 'Qualikategorie 1', $courseId = null) {
        return Category::create(['course_id' => ($courseId !== null ? $courseId : $this->courseId), 'name' => $name])->id;
    }

    protected function createMA($anforderung = 'Mindestanforderung 1', $killer = true, $courseId = null) {
        return Requirement::create(['course_id' => ($courseId !== null ? $courseId : $this->courseId), 'content' => $anforderung, 'mandatory' => $killer])->id;
    }
}
