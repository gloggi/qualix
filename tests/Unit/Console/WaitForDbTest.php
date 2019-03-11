<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class WaitForDbTest extends TestCase
{
    /**
     * When the DB is present from the beginning, should succeed immediately
     *
     * @return void
     */
    public function test_shouldHaltOnSuccessfulDbConnection()
    {
        // given
        $connectionMock = \Mockery::mock('\Illuminate\Database\ConnectionInterface');
        DB::shouldReceive('connection')->once()->andReturn($connectionMock);
        $connectionMock->shouldReceive('getPdo')->once()->andReturnNull();

        // when
        $this->artisan('db:wait')

            // then
            ->expectsOutput('DB connection successful!')
            ->assertExitCode(0);
    }

    /**
     * While the DB is not present, should loop until it is
     *
     * @return void
     */
    public function test_shouldLoopOnFailedDbConnection()
    {
        // given
        $connectionMock = \Mockery::mock('\Illuminate\Database\ConnectionInterface');
        // throw exception on the first two tries, then succeed
        DB::shouldReceive('connection')->twice()->andThrow(new \PDOException());
        DB::shouldReceive('connection')->once()->andReturn($connectionMock);
        $connectionMock->shouldReceive('getPdo')->once()->andReturnNull();

        // when
        $this->artisan('db:wait')

            // then
            ->expectsOutput('DB connection failed, still waiting for DB to come online...')
            ->expectsOutput('DB connection failed, still waiting for DB to come online...')
            ->expectsOutput('DB connection successful!')
            ->assertExitCode(0);
    }
}
