<?php

namespace Database\Factories;

use App\Models\WordpressSite;
use App\Support\AcfFieldMap;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WordpressSite>
 */
class WordpressSiteFactory extends Factory
{
    /**
     * @var class-string<WordpressSite>
     */
    protected $model = WordpressSite::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'base_url' => 'https://'.fake()->domainName(),
            'username' => fake()->userName(),
            'app_password' => 'abcd EFGH ijkl MNOP qrst UVWX',
            'connector_version' => '1.0.0',
            'last_connected_at' => now(),
            'field_map' => AcfFieldMap::default(),
        ];
    }
}
