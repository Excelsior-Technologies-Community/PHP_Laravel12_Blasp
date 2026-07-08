<?php

namespace Tests\Feature;

use App\Models\BadWord;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModerationFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_profanity_attempt_reduces_reputation_and_mutes_user(): void
    {
        $user = User::factory()->create([
            'name' => 'Karan',
            'reputation_score' => 60,
            'muted_until' => null,
        ]);

        BadWord::create([
            'word' => 'gandu',
            'is_active' => true,
        ]);

        $response = $this->post('/check', [
            'message' => 'gandu',
            'user_name' => $user->name,
        ]);

        $response->assertRedirect('/');

        $user->refresh();

        $this->assertSame(50, $user->reputation_score);
        $this->assertNotNull($user->muted_until);
        $this->assertTrue($user->muted_until->isFuture());
    }

    public function test_admin_can_add_custom_bad_word(): void
    {
        $response = $this->post('/admin/moderation/words', [
            'word' => 'bewakoof',
        ]);

        $response->assertRedirect('/admin/moderation');
        $this->assertDatabaseHas('bad_words', [
            'word' => 'bewakoof',
            'is_active' => true,
        ]);
    }
}
