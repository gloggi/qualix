<?php

namespace Tests\Feature;

use App\Models\Kurs;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class SelectCourseTest extends TestCase {

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->post('/', ['kursId' => 1]);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldSelectCourse() {
        // given
        /** @var Kurs $kurs1 */
        $kurs1 = Kurs::create(['name' => 'Kurs 1']);
        $kurs1->users()->attach(Auth::user()->getAuthIdentifier(), ['last_accessed' => '2019-01-01 12:00:00']);
        $kurs1->save();

        /** @var Kurs $kurs2 */
        $kurs2 = Kurs::create(['name' => 'Kurs 2']);
        $kurs2->users()->attach(Auth::user()->getAuthIdentifier(), ['last_accessed' => '2019-01-02 12:00:00']);
        $kurs2->save();

        $payload = ['kursId' => $kurs1->id];

        // when
        $response = $this->post('/', $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $this->assertRegExp("%<option value=\"{$kurs1->id}\" selected>{$kurs1->name}</option>%", $response->content());
        $this->assertRegExp("%<option value=\"{$kurs2->id}\">{$kurs2->name}</option>%", $response->content());
    }

    public function test_shouldValidateSelectCourseData_noKursId() {
        // given

        // when
        $response = $this->post('/', ['kursId' => '']);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }
}
