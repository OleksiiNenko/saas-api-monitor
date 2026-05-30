<?php

namespace Tests\Feature;

use App\Models\WordpressSite;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class WordpressSiteApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_connects_a_site_when_ping_succeeds(): void
    {
        Http::fake([
            '*/wp-json/ai-page/v1/ping' => Http::response([
                'ok' => true,
                'connector_version' => '1.0.0',
                'acf_active' => true,
                'wpml_active' => true,
            ], 200),
        ]);

        $response = $this->postJson('/api/wordpress-sites', [
            'name' => 'Injector',
            'base_url' => 'https://injector.ua',
            'username' => 'webmaster',
            'app_password' => 'abcd EFGH ijkl MNOP',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.name', 'Injector')
            ->assertJsonMissingPath('data.app_password');

        $this->assertDatabaseHas('wordpress_sites', ['base_url' => 'https://injector.ua']);
        // Password is stored encrypted, not in plain text.
        $this->assertNotSame('abcd EFGH ijkl MNOP', WordpressSite::first()->getRawOriginal('app_password'));
    }

    public function test_it_rejects_a_site_when_ping_fails(): void
    {
        Http::fake([
            '*/wp-json/ai-page/v1/ping' => Http::response(['message' => 'bad creds'], 401),
        ]);

        $this->postJson('/api/wordpress-sites', [
            'name' => 'Bad',
            'base_url' => 'https://example.com',
            'username' => 'x',
            'app_password' => 'y',
        ])->assertStatus(422);

        $this->assertDatabaseCount('wordpress_sites', 0);
    }

    public function test_it_proxies_site_meta(): void
    {
        $site = WordpressSite::factory()->create();

        Http::fake([
            '*/wp-json/ai-page/v1/meta' => Http::response([
                'pages' => [['id' => 2, 'title' => 'Услуги']],
                'categories' => [['id' => 13, 'name' => 'Услуги']],
                'authors' => [],
                'languages' => [['code' => 'ru', 'name' => 'Русский']],
            ], 200),
        ]);

        $this->getJson("/api/wordpress-sites/{$site->id}/meta")
            ->assertOk()
            ->assertJsonPath('categories.0.id', 13);
    }
}
