<?php

namespace Database\Factories;

use App\Models\GeneratedPage;
use App\Models\WordpressSite;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GeneratedPage>
 */
class GeneratedPageFactory extends Factory
{
    /**
     * @var class-string<GeneratedPage>
     */
    protected $model = GeneratedPage::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'wordpress_site_id' => WordpressSite::factory(),
            'topic' => fake()->sentence(4),
            'page_type' => 'landing',
            'audience' => 'car owners',
            'tone' => 'продающий',
            'language' => 'ru',
            'keywords' => 'диагностика, ремонт',
            'cta' => 'Заказать',
            'fields' => [
                'seo_title_injector' => fake()->sentence(3),
                'block2_title' => fake()->sentence(2),
            ],
            'title' => fake()->sentence(3),
            'slug' => fake()->slug(),
            'status' => GeneratedPage::STATUS_GENERATED,
        ];
    }
}
