<?php

namespace Tests\Feature\User;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class UpdateUserTest extends TestCase {

    private $payload;

    public function setUp(): void {
        parent::setUp();

        $this->payload = ['name' => 'Räuchli', 'group' => 'Wanderfalken'];
    }

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->post('/user', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldUpdateUser() {
        // when
        $response = $this->post('/user', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee($this->payload['name']);
    }

    public function test_shouldRemoveImage() {
        // given
        Storage::fake();
        $imagePath = UploadedFile::fake()->create('avatar.jpg', 10, 'image/jpeg')->store('public/images');
        $userId = $this->user()->id;
        User::find($userId)->update(['image_url' => $imagePath]);
        // Auth::user() is cached, so it must be refreshed to see the image_url set above
        $this->refreshUser();

        // when
        $response = $this->post('/user', $this->payload + ['remove_image' => '1']);

        // then
        $response->assertStatus(302);
        $this->assertNull(User::find($userId)->image_url);
        Storage::assertMissing($imagePath);
    }
}
