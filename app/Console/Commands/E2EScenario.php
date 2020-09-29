<?php

namespace App\Console\Commands;

use App\Models\Course;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

class E2EScenario extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'e2e:scenario {--user-id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup a scenario in the database for end-to-end testing';

    /**
     * Execute the console command.
     *
     * @return int
     * @throws \Exception
     */
    public function handle()
    {
        if (App::environment('production')) {
            $this->error("Not setting up e2e scenario in production mode");
            return 1;
        }

        $userId = null;

        if ($this->option('user-id')) {
            $userId = $this->option('user-id');
            if (!User::where('id', $userId)->exists()) {
                $this->error('Specified user ' . $this->option('user-id') . ' not found in database.');
                return 1;
            }
        } else {
            if (!User::exists()) {
                $this->error('No user found in database, please create a user first.');
                return 1;
            }
            $userId = User::first()->id;
        }

        /** @var Course $course */
        $course = factory(Course::class)->create();

        $course->users()->attach($userId);

        return 0;
    }
}
