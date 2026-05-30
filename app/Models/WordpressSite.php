<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WordpressSite extends Model
{
    /** @use HasFactory<\Database\Factories\WordpressSiteFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'base_url',
        'username',
        'app_password',
        'connector_version',
        'last_connected_at',
        'field_map',
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'app_password',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'app_password' => 'encrypted',
            'field_map' => 'array',
            'last_connected_at' => 'datetime',
        ];
    }

    /**
     * @return HasMany<GeneratedPage, $this>
     */
    public function generatedPages(): HasMany
    {
        return $this->hasMany(GeneratedPage::class);
    }
}
