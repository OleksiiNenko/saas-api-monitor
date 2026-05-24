<?php

namespace Tests\Feature;

use App\Models\Monitor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MonitorApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_lists_monitors_with_pagination(): void
    {
        Monitor::factory()->count(3)->create();

        $response = $this->getJson('/api/monitors');

        $response->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [['id', 'name', 'url', 'method', 'expected_status', 'interval_seconds', 'is_active']],
                'links',
                'meta',
            ]);
    }

    public function test_it_creates_a_monitor(): void
    {
        $payload = [
            'name' => 'Checkout API',
            'url' => 'https://api.example.com/health',
            'method' => 'GET',
            'expected_status' => 200,
            'interval_seconds' => 60,
        ];

        $response = $this->postJson('/api/monitors', $payload);

        $response->assertCreated()
            ->assertJsonPath('data.name', 'Checkout API')
            ->assertJsonPath('data.url', 'https://api.example.com/health')
            ->assertJsonPath('data.is_active', true);

        $this->assertDatabaseHas('monitors', [
            'url' => 'https://api.example.com/health',
            'interval_seconds' => 60,
        ]);
    }

    public function test_it_applies_defaults_on_create(): void
    {
        $response = $this->postJson('/api/monitors', [
            'name' => 'Minimal',
            'url' => 'https://example.com',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.method', 'GET')
            ->assertJsonPath('data.expected_status', 200)
            ->assertJsonPath('data.interval_seconds', 300);
    }

    public function test_it_validates_required_and_well_formed_url(): void
    {
        $this->postJson('/api/monitors', ['name' => 'No url'])
            ->assertUnprocessable()
            ->assertJsonValidationErrorFor('url');

        $this->postJson('/api/monitors', ['name' => 'Bad url', 'url' => 'not-a-url'])
            ->assertUnprocessable()
            ->assertJsonValidationErrorFor('url');
    }

    public function test_it_rejects_too_frequent_interval(): void
    {
        $this->postJson('/api/monitors', [
            'name' => 'Too fast',
            'url' => 'https://example.com',
            'interval_seconds' => 5,
        ])->assertUnprocessable()
            ->assertJsonValidationErrorFor('interval_seconds');
    }

    public function test_it_shows_a_monitor(): void
    {
        $monitor = Monitor::factory()->create();

        $this->getJson("/api/monitors/{$monitor->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $monitor->id);
    }

    public function test_it_returns_404_for_missing_monitor(): void
    {
        $this->getJson('/api/monitors/999')->assertNotFound();
    }

    public function test_it_updates_a_monitor(): void
    {
        $monitor = Monitor::factory()->create(['is_active' => true]);

        $this->patchJson("/api/monitors/{$monitor->id}", ['is_active' => false])
            ->assertOk()
            ->assertJsonPath('data.is_active', false);

        $this->assertDatabaseHas('monitors', [
            'id' => $monitor->id,
            'is_active' => false,
        ]);
    }

    public function test_it_deletes_a_monitor(): void
    {
        $monitor = Monitor::factory()->create();

        $this->deleteJson("/api/monitors/{$monitor->id}")->assertNoContent();

        $this->assertDatabaseMissing('monitors', ['id' => $monitor->id]);
    }
}
