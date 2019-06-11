<?php

namespace Tests\Feature\Admin\Course;

use App\Models\Course;
use Illuminate\Foundation\Testing\TestResponse;
use Tests\TestCase;

class SelectCourseTest extends TestCase {

    public function test_shouldAutoselectCourse() {
        // given
        /** @var Course $course1 */
        $course1 = Course::create(['name' => 'Kurs 1']);
        $course1->users()->attach($this->user()->id, ['last_accessed' => '2019-01-01 12:00:00']);
        $course1->save();

        /** @var Course $course2 */
        $course2 = Course::create(['name' => 'Kurs 2']);
        $course2->users()->attach($this->user()->id, ['last_accessed' => '2019-01-02 12:00:00']);
        $course2->save();

        // when
        $response = $this->get('/');

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/kurs/' . $course2->id);
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $this->assertRegExp("%<option value=\"[^\"]*\">{$course1->name}</option>%", $response->content());
        $this->assertRegExp("%<option value=\"[^\"]*\" selected>{$course2->name}</option>%", $response->content());
    }

    public function test_shouldShowSelectedCourse() {
        // given
        /** @var Course $course1 */
        $course1 = Course::create(['name' => 'Kurs 1']);
        $course1->users()->attach($this->user()->id, ['last_accessed' => '2019-01-01 12:00:00']);
        $course1->save();

        /** @var Course $course2 */
        $course2 = Course::create(['name' => 'Kurs 2']);
        $course2->users()->attach($this->user()->id, ['last_accessed' => '2019-01-02 12:00:00']);
        $course2->save();

        // when
        $response = $this->get('/kurs/' . $course1->id);

        // then
        $response->assertStatus(200);
        $this->assertRegExp("%<option value=\"[^\"]*\" selected>{$course1->name}</option>%", $response->content());
        $this->assertRegExp("%<option value=\"[^\"]*\">{$course2->name}</option>%", $response->content());
    }

    public function test_shouldNotUpdateLeiterLastAccessed_whenSameCourseWasLastViewedBefore() {
        // given
        $user = $this->user();

        /** @var Course $course1 */
        $course1 = Course::create(['name' => 'Kurs 1']);
        $course1->users()->attach($user->id, ['last_accessed' => '2019-01-01 12:00:00']);
        $course1->save();

        /** @var Course $course2 */
        $course2 = Course::create(['name' => 'Kurs 2']);
        $course2->users()->attach($user->id, ['last_accessed' => '2019-01-02 12:00:00']);
        $course2->save();

        // when
        $this->get('/kurs/' . $course2->id . '/admin');

        // then
        $course2FromDB = $user->courses()->withPivot('last_accessed')->first();
        $this->assertThat($course2FromDB->id, $this->equalTo($course2->id));
        $this->assertThat($course2FromDB->pivot->last_accessed, $this->equalTo('2019-01-02 12:00:00'));
    }

    public function test_shouldNotAllowSelectingCourse_whenUserIsNotInKurs() {
        // given
        /** @var Course $course */
        $course = Course::create(['name' => 'Fremder Kurs']);
        $course->save();

        // when
        $response = $this->get('/kurs/' . $course->id);

        // then
        $response->assertStatus(404);
    }
}
