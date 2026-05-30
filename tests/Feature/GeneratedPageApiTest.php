<?php

namespace Tests\Feature;

use App\Models\GeneratedPage;
use App\Models\WordpressSite;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GeneratedPageApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config()->set('services.openai.key', 'test-key');
        config()->set('services.openai.base_url', 'https://api.openai.com/v1');
        config()->set('services.openai.model', 'gpt-4o-mini');
    }

    public function test_it_generates_a_page_from_a_brief(): void
    {
        $site = WordpressSite::factory()->create();

        Http::fake([
            '*/chat/completions' => Http::response([
                'choices' => [[
                    'message' => [
                        'content' => json_encode([
                            'seo_title_injector' => 'Установка ГБО',
                            'block2_desc' => '<p>Текст <script>alert(1)</script></p>',
                            'unknown_field' => 'dropped',
                        ]),
                    ],
                ]],
            ], 200),
        ]);

        $response = $this->postJson('/api/generated-pages/generate', [
            'wordpress_site_id' => $site->id,
            'topic' => 'Установка ГБО в Киеве',
            'language' => 'ru',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.fields.seo_title_injector', 'Установка ГБО')
            // Unknown logical fields are filtered out.
            ->assertJsonMissingPath('data.fields.unknown_field');

        $this->assertDatabaseHas('generated_pages', ['topic' => 'Установка ГБО в Киеве']);
    }

    public function test_it_pushes_a_page_as_draft_with_mapped_acf_keys(): void
    {
        $site = WordpressSite::factory()->create();
        $page = GeneratedPage::factory()->for($site, 'site')->create([
            'fields' => [
                'seo_title_injector' => 'SEO',
                'block2_desc' => '<p>Привет <script>x</script></p>',
            ],
            'title' => 'Моя страница',
            'language' => 'ru',
            'category_ids' => [13],
        ]);

        Http::fake([
            '*/wp-json/ai-page/v1/pages' => Http::response([
                'ok' => true,
                'id' => 5950,
                'status' => 'draft',
                'edit_link' => 'https://injector.ua/wp-admin/post.php?post=5950&action=edit',
            ], 200),
        ]);

        $this->postJson("/api/generated-pages/{$page->id}/push")
            ->assertOk()
            ->assertJsonPath('data.status', 'pushed')
            ->assertJsonPath('data.wp_post_id', 5950);

        Http::assertSent(function ($request) {
            $body = $request->data();

            // Always a draft via mapped ACF field keys; rich text sanitized; taxonomy passed.
            return str_contains($request->url(), '/ai-page/v1/pages')
                && $body['acf']['field_5f7f04a24dfd8'] === 'SEO'
                && ! str_contains($body['acf']['field_61c305b730413'], '<script>')
                && $body['categories'] === [13]
                && $body['language'] === 'ru';
        });
    }

    public function test_it_validates_the_brief(): void
    {
        $this->postJson('/api/generated-pages/generate', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['wordpress_site_id', 'topic']);
    }
}
