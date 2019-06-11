<?php

namespace Tests\Feature\Welcome;

use Tests\TestCaseWithBasicData;

class ReadWelcomePageTest extends TestCaseWithBasicData {

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->get('/kurs/' . $this->courseId);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldDisplayWelcomePage() {
        // given

        // when
        $response = $this->get('/kurs/' . $this->courseId);

        // then
        $response->assertOk();
        $response->assertSee('Willkommä bim Qualix');
    }
}
