<?php

namespace Tests\Unit\Console;

use App\Models\Participant;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Tests\TestCaseWithBasicData;

class MigrateParticipantImagesToPrivateStorageTest extends TestCaseWithBasicData
{
    public function test_shouldMigrateImageFromPublicToPrivateStorage()
    {
        // given
        Storage::fake();
        $oldPath = 'public/images/legacy-participant.png';
        Storage::put($oldPath, 'legacy-image-content');

        $participant = Participant::findOrFail($this->participantId);
        $participant->update(['image_url' => $oldPath]);

        // when
        $exitCode = Artisan::call('qualix:migrate-participant-images');

        // then
        $this->assertSame(0, $exitCode);
        $participant = Participant::findOrFail($this->participantId);
        $this->assertNotEquals($oldPath, $participant->image_url);
        $this->assertStringStartsWith('participant-images/', $participant->image_url);
        Storage::assertMissing($oldPath);
        Storage::assertExists($participant->image_url);
        $this->assertStringContainsString('migrated=1', Artisan::output());
    }

    public function test_shouldNotModifyFilesOnDryRun()
    {
        // given
        Storage::fake();
        $oldPath = 'public/images/dry-run-participant.png';
        Storage::put($oldPath, 'legacy-image-content');

        $participant = Participant::findOrFail($this->participantId);
        $participant->update(['image_url' => $oldPath]);

        // when
        $exitCode = Artisan::call('qualix:migrate-participant-images', ['--dry-run' => true]);

        // then
        $this->assertSame(0, $exitCode);
        $participant = Participant::findOrFail($this->participantId);
        $this->assertEquals($oldPath, $participant->image_url);
        Storage::assertExists($oldPath);
        $this->assertCount(0, Storage::files('participant-images'));
        $this->assertStringContainsString('migrated=0', Artisan::output());
    }

    public function test_shouldCountSkippedMissingAndMigratedParticipants()
    {
        // given
        Storage::fake();

        Participant::create([
            'course_id' => $this->courseId,
            'scout_name' => 'Already Private',
            'image_url' => 'participant-images/already-private.png',
        ]);
        Participant::create([
            'course_id' => $this->courseId,
            'scout_name' => 'Unexpected Path',
            'image_url' => 'images/unexpected.png',
        ]);
        Participant::create([
            'course_id' => $this->courseId,
            'scout_name' => 'Missing File',
            'image_url' => 'public/images/missing-file.png',
        ]);
        $migratedParticipantId = Participant::create([
            'course_id' => $this->courseId,
            'scout_name' => 'Needs Migration',
            'image_url' => 'public/images/needs-migration.png',
        ])->id;

        Storage::put('participant-images/already-private.png', 'private-image-content');
        Storage::put('public/images/needs-migration.png', 'legacy-image-content');

        // when
        $exitCode = Artisan::call('qualix:migrate-participant-images');

        // then
        $this->assertSame(0, $exitCode);
        $migratedParticipant = Participant::findOrFail($migratedParticipantId);
        $this->assertStringStartsWith('participant-images/', $migratedParticipant->image_url);
        $this->assertNotEquals('public/images/needs-migration.png', $migratedParticipant->image_url);
        Storage::assertMissing('public/images/needs-migration.png');
        Storage::assertExists($migratedParticipant->image_url);

        $output = Artisan::output();
        $this->assertStringContainsString('migrated=1', $output);
        $this->assertStringContainsString('skipped=2', $output);
        $this->assertStringContainsString('missing=1', $output);
        $this->assertStringContainsString('failed=0', $output);
    }
}
