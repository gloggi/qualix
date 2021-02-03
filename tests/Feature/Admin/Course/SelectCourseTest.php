<?php

namespace Tests\Feature\Admin\Course;

use App\Models\Course;
use Illuminate\Testing\TestResponse;
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
        $response->assertRedirect('/course/' . $course2->id);
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $this->assertMatchesRegularExpression("%<b-form-select-option value=\"[^\"]*\">{$course1->name}</b-form-select-option>%", $response->content());
        $this->assertMatchesRegularExpression("%<b-form-select[^>]*id=\"global-course-select\"[^>]*value=\"([^\"]*)\"((?!</b-form-select>).)*<b-form-select-option value=\"\\1\">" . $course2->name . "</b-form-select-option>%s", $response->content());
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
        $response = $this->get('/course/' . $course1->id);

        // then
        $response->assertStatus(200);
        $this->assertMatchesRegularExpression("%<b-form-select[^>]*id=\"global-course-select\"[^>]*value=\"([^\"]*)\"((?!</b-form-select>).)*<b-form-select-option value=\"\\1\">" . $course1->name . "</b-form-select-option>%s", $response->content());
        $this->assertMatchesRegularExpression("%<b-form-select-option value=\"[^\"]*\">{$course2->name}</b-form-select-option>%", $response->content());
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
        $this->get('/course/' . $course2->id . '/admin');

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
        $response = $this->get('/course/' . $course->id);

        // then
        $response->assertStatus(404);
    }
}
