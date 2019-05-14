<?php

namespace Tests\Feature\Bloecke;

use Tests\TestCaseWithKurs;

class ReadBloeckeTest extends TestCaseWithKurs {

    public function test_shouldOrderBloecke() {
        // given
        $this->post('/kurs/' . $this->kursId . '/admin/bloecke', ['full_block_number' => '1.1', 'blockname' => 'Block 1', 'datum' => '01.01.2019', 'ma_ids' => null]);
        $this->post('/kurs/' . $this->kursId . '/admin/bloecke', ['full_block_number' => '1.1', 'blockname' => 'later date', 'datum' => '02.01.2019', 'ma_ids' => null]);
        $this->post('/kurs/' . $this->kursId . '/admin/bloecke', ['full_block_number' => '1.1', 'blockname' => 'earlier date', 'datum' => '31.12.2018', 'ma_ids' => null]);
        $this->post('/kurs/' . $this->kursId . '/admin/bloecke', ['full_block_number' => '2.1', 'blockname' => 'later day number', 'datum' => '01.01.2019', 'ma_ids' => null]);
        $this->post('/kurs/' . $this->kursId . '/admin/bloecke', ['full_block_number' => '0.1', 'blockname' => 'earlier day number', 'datum' => '01.01.2019', 'ma_ids' => null]);
        $this->post('/kurs/' . $this->kursId . '/admin/bloecke', ['full_block_number' => '1.2', 'blockname' => 'later block number', 'datum' => '01.01.2019', 'ma_ids' => null]);
        $this->post('/kurs/' . $this->kursId . '/admin/bloecke', ['full_block_number' => '1.0', 'blockname' => 'earlier block number', 'datum' => '01.01.2019', 'ma_ids' => null]);
        $this->post('/kurs/' . $this->kursId . '/admin/bloecke', ['full_block_number' => '1.1', 'blockname' => 'Block 2 later block name', 'datum' => '01.01.2019', 'ma_ids' => null]);
        $this->post('/kurs/' . $this->kursId . '/admin/bloecke', ['full_block_number' => '1.1', 'blockname' => 'Block 0 earlier block name', 'datum' => '01.01.2019', 'ma_ids' => null]);

        // when
        $response = $this->get('/kurs/' . $this->kursId . '/bloecke');

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
