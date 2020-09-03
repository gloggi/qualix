<?php

namespace Tests\Unit\Console;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Facades\DB;
use Mockery;
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
        $connectionMock = Mockery::mock(ConnectionInterface::class);
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
        $connectionMock = Mockery::mock(ConnectionInterface::class);
        // throw exception on the first two tries, then succeed
        DB::shouldReceive('connection')->twice()->andThrow(new \PDOException());
        DB::shouldReceive('connection')->once()->andReturn($connectionMock);
        $connectionMock->shouldReceive('getPdo')->once()->andReturnNull();
        // skip the waiting time to finish the test faster
        config([ 'console.db.wait.interval' => 0 ]);

        // when
        $this->artisan('db:wait')

            // then
            ->expectsOutput('DB connection failed, still waiting for DB to come online...')
            ->expectsOutput('DB connection failed, still waiting for DB to come online...')
            ->expectsOutput('DB connection successful!')
            ->assertExitCode(0);
    }
}
