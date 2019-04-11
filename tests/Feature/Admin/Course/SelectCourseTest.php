<?php

namespace Tests\Feature;

use App\Models\Kurs;
use App\Models\User;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class SelectCourseTest extends TestCase {

    public function test_shouldAutoselectCourse() {
        // given
        /** @var Kurs $kurs1 */
        $kurs1 = Kurs::create(['name' => 'Kurs 1']);
        $kurs1->users()->attach(Auth::user()->getAuthIdentifier(), ['last_accessed' => '2019-01-01 12:00:00']);
        $kurs1->save();

        /** @var Kurs $kurs2 */
        $kurs2 = Kurs::create(['name' => 'Kurs 2']);
        $kurs2->users()->attach(Auth::user()->getAuthIdentifier(), ['last_accessed' => '2019-01-02 12:00:00']);
        $kurs2->save();

        // when
        $response = $this->get('/');

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/kurs/' . $kurs2->id);
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $this->assertRegExp("%<option value=\"[^\"]*\">{$kurs1->name}</option>%", $response->content());
        $this->assertRegExp("%<option value=\"[^\"]*\" selected>{$kurs2->name}</option>%", $response->content());
    }

    public function test_shouldShowSelectedCourse() {
        // given
        /** @var Kurs $kurs1 */
        $kurs1 = Kurs::create(['name' => 'Kurs 1']);
        $kurs1->users()->attach(Auth::user()->getAuthIdentifier(), ['last_accessed' => '2019-01-01 12:00:00']);
        $kurs1->save();

        /** @var Kurs $kurs2 */
        $kurs2 = Kurs::create(['name' => 'Kurs 2']);
        $kurs2->users()->attach(Auth::user()->getAuthIdentifier(), ['last_accessed' => '2019-01-02 12:00:00']);
        $kurs2->save();

        // when
        $response = $this->get('/kurs/' . $kurs1->id);

        // then
        $response->assertStatus(200);
        $this->assertRegExp("%<option value=\"[^\"]*\" selected>{$kurs1->name}</option>%", $response->content());
        $this->assertRegExp("%<option value=\"[^\"]*\">{$kurs2->name}</option>%", $response->content());
    }

    public function test_shouldNotUpdateLeiterLastAccessed_whenSameCourseWasLastViewedBefore() {
        // given
        /** @var User $user */
        $user = Auth::user();

        /** @var Kurs $kurs1 */
        $kurs1 = Kurs::create(['name' => 'Kurs 1']);
        $kurs1->users()->attach($user->getAuthIdentifier(), ['last_accessed' => '2019-01-01 12:00:00']);
        $kurs1->save();

        /** @var Kurs $kurs2 */
        $kurs2 = Kurs::create(['name' => 'Kurs 2']);
        $kurs2->users()->attach($user->getAuthIdentifier(), ['last_accessed' => '2019-01-02 12:00:00']);
        $kurs2->save();

        // when
        $this->get('/kurs/' . $kurs2->id . '/admin');

        // then
        $kurs2FromDB = $user->kurse()->withPivot('last_accessed')->first();
        $this->assertThat($kurs2FromDB->id, $this->equalTo($kurs2->id));
        $this->assertThat($kurs2FromDB->pivot->last_accessed, $this->equalTo('2019-01-02 12:00:00'));
    }

    public function test_shouldNotAllowSelectingCourse_whenUserIsNotInKurs() {
        // given
        /** @var Kurs $kurs */
        $kurs = Kurs::create(['name' => 'Fremder Kurs']);
        $kurs->save();

        // when
        $response = $this->get('/kurs/' . $kurs->id);

        // then
        $response->assertStatus(404);
    }
}
