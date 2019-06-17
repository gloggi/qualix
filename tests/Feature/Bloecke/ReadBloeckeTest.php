<?php

namespace Tests\Feature\Bloecke;

use Tests\TestCaseWithCourse;

class ReadBloeckeTest extends TestCaseWithCourse {

    public function test_shouldOrderBloecke() {
        // given
        $this->createBlock('Block 1', '1.1', '01.01.2019');
        $this->createBlock('later date', '1.1', '02.01.2019');
        $this->createBlock('earlier date', '1.1', '31.12.2018');
        $this->createBlock('later day number', '2.1', '01.01.2019');
        $this->createBlock('earlier day number', '0.1', '01.01.2019');
        $this->createBlock('later block number', '1.2', '01.01.2019');
        $this->createBlock('earlier block number', '1.0', '01.01.2019');
        $this->createBlock('Block 2 later block name', '1.1', '01.01.2019');
        $this->createBlock('Block 0 earlier block name', '1.1', '01.01.2019');

        // when
        $response = $this->get('/course/' . $this->courseId . '/blocks');

        // then
        $response->assertOk();
        $this->assertSeeAllInOrder('a.list-group-item h5', [
          'earlier date',
          'earlier day number',
          'earlier block number',
          'Block 0 earlier block name',
          'Block 1',
          'Block 2 later block name',
          'later block number',
          'later day number',
          'later date',
        ]);
    }
}
