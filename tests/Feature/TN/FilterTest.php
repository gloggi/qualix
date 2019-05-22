<?php

namespace Tests\Feature\TN;

use Tests\TestCaseWithBasicData;

class FilterTest extends TestCaseWithBasicData {

    protected $qkId;
    protected $qkId2;
    protected $maId;
    protected $maId2;

    public function setUp(): void {
        parent::setUp();

        $this->post('/kurs/' . $this->kursId . '/admin/qk', ['quali_kategorie' => 'Qualikategorie 1']);
        $this->post('/kurs/' . $this->kursId . '/admin/qk', ['quali_kategorie' => 'Qualikategorie 2']);
        $this->post('/kurs/' . $this->kursId . '/admin/ma', ['anforderung' => 'Mindestanforderung 1', 'killer' => '1']);
        $this->post('/kurs/' . $this->kursId . '/admin/ma', ['anforderung' => 'Mindestanforderung 2', 'killer' => '1']);
        $user = $this->user();
        $this->qkId = $user->lastAccessedKurs->qks()->first()->id;
        $this->qkId2 = $user->lastAccessedKurs->qks()->get()[1]->id;
        $this->maId = $user->lastAccessedKurs->mas()->first()->id;
        $this->maId2 = $user->lastAccessedKurs->mas()->get()[1]->id;

        $this->post('/kurs/' . $this->kursId . '/beobachtungen/neu', ['tn_ids' => '' . $this->tnId, 'kommentar' => 'hat QK und MA', 'bewertung' => '1', 'block_id' => '' . $this->blockId, 'ma_ids' => '' . $this->maId, 'qk_ids' => '' . $this->qkId]);
        $this->post('/kurs/' . $this->kursId . '/beobachtungen/neu', ['tn_ids' => '' . $this->tnId, 'kommentar' => 'nur QK', 'bewertung' => '1', 'block_id' => '' . $this->blockId, 'ma_ids' => '', 'qk_ids' => '' . $this->qkId]);
        $this->post('/kurs/' . $this->kursId . '/beobachtungen/neu', ['tn_ids' => '' . $this->tnId, 'kommentar' => 'nur MA', 'bewertung' => '1', 'block_id' => '' . $this->blockId, 'ma_ids' => '' . $this->maId, 'qk_ids' => '']);
        $this->post('/kurs/' . $this->kursId . '/beobachtungen/neu', ['tn_ids' => '' . $this->tnId, 'kommentar' => 'ohne QK oder MA', 'bewertung' => '1', 'block_id' => '' . $this->blockId, 'ma_ids' => '', 'qk_ids' => '']);
        $this->post('/kurs/' . $this->kursId . '/beobachtungen/neu', ['tn_ids' => '' . $this->tnId, 'kommentar' => 'andere QK', 'bewertung' => '1', 'block_id' => '' . $this->blockId, 'ma_ids' => '', 'qk_ids' => '' . $this->qkId2]);
        $this->post('/kurs/' . $this->kursId . '/beobachtungen/neu', ['tn_ids' => '' . $this->tnId, 'kommentar' => 'andere MA', 'bewertung' => '1', 'block_id' => '' . $this->blockId, 'ma_ids' => '' . $this->maId2, 'qk_ids' => '']);
        $this->post('/kurs/' . $this->kursId . '/beobachtungen/neu', ['tn_ids' => '' . $this->tnId, 'kommentar' => 'alle QK', 'bewertung' => '1', 'block_id' => '' . $this->blockId, 'ma_ids' => '', 'qk_ids' => '' . $this->qkId . ',' . $this->qkId2]);
        $this->post('/kurs/' . $this->kursId . '/beobachtungen/neu', ['tn_ids' => '' . $this->tnId, 'kommentar' => 'alle MA', 'bewertung' => '1', 'block_id' => '' . $this->blockId, 'ma_ids' => '' . $this->maId . ',' . $this->maId2, 'qk_ids' => '']);
    }

    public function test_shouldDisplayAllObservations_whenNoFilter() {
        // given

        // when
        $response = $this->get('/kurs/' . $this->kursId . '/tn/' . $this->tnId);

        // then
        $response->assertStatus(200);
        $response->assertSee('hat QK und MA');
        $response->assertSee('nur QK');
        $response->assertSee('nur MA');
        $response->assertSee('ohne QK oder MA');
        $response->assertSee('andere QK');
        $response->assertSee('andere MA');
        $response->assertSee('alle QK');
        $response->assertSee('alle MA');
    }

    public function test_shouldFilterByQK() {
        // given

        // when
        $response = $this->get('/kurs/' . $this->kursId . '/tn/' . $this->tnId . '?qk=' . $this->qkId);

        // then
        $response->assertStatus(200);
        $response->assertSee('hat QK und MA');
        $response->assertSee('nur QK');
        $response->assertDontSee('nur MA');
        $response->assertDontSee('ohne QK oder MA');
        $response->assertDontSee('andere QK');
        $response->assertDontSee('andere MA');
        $response->assertSee('alle QK');
        $response->assertDontSee('alle MA');
    }

    public function test_shouldFilterByNoQK() {
        // given

        // when
        $response = $this->get('/kurs/' . $this->kursId . '/tn/' . $this->tnId . '?qk=0');

        // then
        $response->assertStatus(200);
        $response->assertDontSee('hat QK und MA');
        $response->assertDontSee('nur QK');
        $response->assertSee('nur MA');
        $response->assertSee('ohne QK oder MA');
        $response->assertDontSee('andere QK');
        $response->assertSee('andere MA');
        $response->assertDontSee('alle QK');
        $response->assertSee('alle MA');
    }

    public function test_shouldFilterByMA() {
        // given

        // when
        $response = $this->get('/kurs/' . $this->kursId . '/tn/' . $this->tnId . '?ma=' . $this->maId);

        // then
        $response->assertStatus(200);
        $response->assertSee('hat QK und MA');
        $response->assertDontSee('nur QK');
        $response->assertSee('nur MA');
        $response->assertDontSee('ohne QK oder MA');
        $response->assertDontSee('andere QK');
        $response->assertDontSee('andere MA');
        $response->assertDontSee('alle QK');
        $response->assertSee('alle MA');
    }

    public function test_shouldFilterByNoMA() {
        // given

        // when
        $response = $this->get('/kurs/' . $this->kursId . '/tn/' . $this->tnId . '?ma=0');

        // then
        $response->assertStatus(200);
        $response->assertDontSee('hat QK und MA');
        $response->assertSee('nur QK');
        $response->assertDontSee('nur MA');
        $response->assertSee('ohne QK oder MA');
        $response->assertSee('andere QK');
        $response->assertDontSee('andere MA');
        $response->assertSee('alle QK');
        $response->assertDontSee('alle MA');
    }
}
