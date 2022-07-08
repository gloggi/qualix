<?php

namespace Database\Factories;

use App\Models\FeedbackContentNode;
use Illuminate\Database\Eloquent\Factories\Factory;

class FeedbackContentNodeFactory extends Factory {
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = FeedbackContentNode::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition() {
        $choices = [
            '{"type": "paragraph", "content": [{"text": "{{text}}", "type": "text"}]}',
            '{"type": "paragraph", "content": [{"text": "{{text}}", "type": "text"}]}',
            '{"type": "paragraph", "content": [{"text": "{{text}}", "type": "text"}]}',
            '{"type": "paragraph", "content": [{"text": "{{text}}", "type": "text"}]}',
            '{"type": "paragraph", "content": [{"text": "{{text}}", "type": "text"}]}',
            '{"type": "paragraph", "content": [{"text": "{{text}}", "type": "text"}]}',
            '{"type": "heading", "attrs": {"level": 3}, "content": [{"text": "{{name}}", "type": "text"}]}',
            '{"type": "heading", "attrs": {"level": 5}, "content": [{"text": "{{name}}", "type": "text"}]}',
            '{"type": "heading", "attrs": {"level": 6}, "content": [{"text": "{{name}}", "type": "text"}]}',
        ];
        return [
            'order' => $this->faker->biasedNumberBetween(0, 10),
            'json' => $this->faker->parse($this->faker->randomElement($choices)),
        ];
    }
}
