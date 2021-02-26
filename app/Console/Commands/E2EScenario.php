<?php

namespace App\Console\Commands;

use App\Models\Block;
use App\Models\Course;
use App\Models\Observation;
use App\Models\Quali;
use App\Models\QualiData;
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
        $course = Course::factory()
            ->hasUsers(3)
            ->hasRequirements(4)
            ->hasParticipants(10)
            ->has(Block::factory()
                ->count(10)
                ->has(Observation::factory()
                    ->count(5)
                    ->fromRandomUser()
                    ->withRequirements()
                    ->maybeMultiParticipant()
                )
            )
            ->has(QualiData::factory()
                ->has(Quali::factory()
                    ->count(10)
                    ->forParticipants()
                    ->withContents()
                    ->withRequirements()
                    ->withObservations()
                ), 'quali_datas'
            )
            ->create();

        $course->users()->attach($userId);

        $this->info('Created a new course:');
        $this->info('Id ' . $course->id);

        return 0;
    }
}
