<?php

namespace Database\Factories;

use App\Models\Participant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class ParticipantFactory extends Factory {
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Participant::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition() {
        return [
            'scout_name' => ucfirst($this->faker->unique()->word),
            'group' => $this->faker->parse('{{lastName}} {{city}}'),
        ];
    }

    public function withImage($withImage) {
        if (!$withImage) return $this;

        return $this->state(function (array $state) {
            // Download metadata about the image
            $response = Http::get('https://fakeface.rest/face/json?minimum_age=17&maximum_age=24')->json();
            $filename = $response['filename'];
            $imageUrl = $response['image_url'];

            // Download the actual image into a temp file
            $stream = @fopen($imageUrl, 'r');
            $tempFile = tempnam(sys_get_temp_dir(), 'url-file-');
            file_put_contents($tempFile, $stream);

            // Store the image into laravel storage
            $stored = Storage::putFileAs('public/images', $tempFile, $filename);

            return [
                'image_url' => $stored,
            ];
        });
    }

}
