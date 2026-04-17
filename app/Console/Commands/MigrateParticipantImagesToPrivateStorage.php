<?php

namespace App\Console\Commands;

use App\Models\Participant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MigrateParticipantImagesToPrivateStorage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'qualix:migrate-participant-images {--dry-run}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Move participant images from public storage to private storage';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        $migrated = 0;
        $skipped = 0;
        $missing = 0;
        $failed = 0;

        Participant::query()
            ->whereNotNull('image_url')
            ->orderBy('id')
            ->chunkById(200, function ($participants) use ($dryRun, &$migrated, &$skipped, &$missing, &$failed) {
                foreach ($participants as $participant) {
                    $oldPath = $participant->image_url;

                    // skippe bereits migrierte
                    if (Str::startsWith($oldPath, 'participant-images/')) {
                        $skipped++;
                        $this->line("SKIP already private: participant {$participant->id} -> {$oldPath}");
                        continue;
                    }

                    // migriere nur bilder aus dem public folder
                    if (!Str::startsWith($oldPath, 'public/images/')) {
                        $skipped++;
                        $this->warn("SKIP unexpected path: participant {$participant->id} -> {$oldPath}");
                        continue;
                    }

                    if (!Storage::exists($oldPath)) {
                        $missing++;
                        $this->error("MISSING: participant {$participant->id} -> {$oldPath}");
                        continue;
                    }

                    $extension = pathinfo($oldPath, PATHINFO_EXTENSION);
                    $newFilename = Str::uuid()->toString() . ($extension ? ".{$extension}" : '');
                    $newPath = 'participant-images/' . $newFilename;

                    $this->line("MOVE participant {$participant->id}: {$oldPath} -> {$newPath}");

                    if ($dryRun) {
                        continue;
                    }

                    $stream = Storage::readStream($oldPath);

                    if ($stream === false) {
                        $failed++;
                        $this->error("FAILED READ: {$oldPath}");
                        continue;
                    }

                    try {
                        $written = Storage::put($newPath, $stream);
                    } finally {
                        if (is_resource($stream)) {
                            fclose($stream);
                        }
                    }

                    if (!$written) {
                        $failed++;
                        $this->error("FAILED WRITE: {$newPath}");
                        continue;
                    }

                    $participant->image_url = $newPath;
                    $participant->save();

                    if (!Storage::delete($oldPath)) {
                        $this->warn("WARN could not delete old file: {$oldPath}");
                    }

                    $migrated++;
                }
            });

        $this->info("Done. migrated={$migrated}, skipped={$skipped}, missing={$missing}, failed={$failed}");

        return self::SUCCESS;
    }
}
