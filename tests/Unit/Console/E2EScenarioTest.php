<?php

namespace Tests\Unit\Console;

use App\Models\User;
use Tests\TestCase;

class E2EScenarioTest extends TestCase
{
    /**
     * When production mode, should refuse to execute
     *
     * @return void
     */
    public function test_shouldExit_inProductionMode()
    {
        // given
        app()->detectEnvironment(function() { return 'production'; });

        // when
        $this->artisan('e2e:scenario')

            // then
            ->expectsOutput('Not setting up e2e scenario in production mode')
            ->assertExitCode(1);
    }

    /**
     * When no user in DB, should say so and exit
     *
     * @return void
     */
    public function test_shouldExit_whenNoUserInDB()
    {
        // given
        User::query()->delete();

        // when
        $this->artisan('e2e:scenario')

            // then
            ->expectsOutput('No user found in database, please create a user first.')
            ->assertExitCode(1);
    }

    /**
     * When specified user not in DB, should say so and exit
     *
     * @return void
     */
    public function test_shouldExit_whenSpecifiedUserIdDoesNotExist()
    {
        // given
        User::query()->delete();

        // when
        $this->artisan('e2e:scenario --user-id=1234')

            // then
            ->expectsOutput('Specified user 1234 not found in database.')
            ->assertExitCode(1);
    }

    /**
     * Should work with default user
     *
     * @return void
     */
    public function test_shouldWork_whenDefaultUser()
    {
        // given
        User::query()->delete();
        $user = User::create([ 'name' => 'E2EScenario', 'email' => 'test@test.com' ]);

        // when
        $this->artisan('e2e:scenario')

            // then
            ->assertExitCode(0);
        $user = User::find($user->id);
        $this->assertEquals(1, $user->courses->count());
        $course = $user->courses->first();
        $this->assertNotEmpty($course->requirements);
        $this->assertNotEmpty($course->participants);
        $this->assertNotEmpty($course->blocks);
        $this->assertGreaterThanOrEqual(2, $course->users->count());
    }

    /**
     * Should work with user id specified by user id
     *
     * @return void
     */
    public function test_shouldWork_whenSpecificUser()
    {
        // given
        User::query()->delete();
        $user = User::create([ 'name' => 'E2EScenario', 'email' => 'test@test.com' ]);

        // when
        $this->artisan('e2e:scenario --user-id=' . $user->id)

            // then
            ->assertExitCode(0);
        $user = User::find($user->id);
        $this->assertEquals(1, $user->courses->count());
        $course = $user->courses->first();
        $this->assertNotEmpty($course->requirements);
        $this->assertNotEmpty($course->participants);
        $this->assertNotEmpty($course->blocks);
        $this->assertGreaterThanOrEqual(2, $course->users->count());
    }
}
