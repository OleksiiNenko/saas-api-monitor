<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GeneratedPage extends Model
{
    /** @use HasFactory<\Database\Factories\GeneratedPageFactory> */
    use HasFactory;

    public const STATUS_GENERATED = 'generated';

    public const STATUS_PUSHED = 'pushed';

    public const STATUS_FAILED = 'failed';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'wordpress_site_id',
        'topic',
        'page_type',
        'audience',
        'tone',
        'language',
        'keywords',
        'sections',
        'cta',
        'extra_instructions',
        'fields',
        'title',
        'slug',
        'parent_id',
        'category_ids',
        'tags',
        'author_id',
        'menu_order',
        'status',
        'wp_post_id',
        'wp_edit_link',
        'wp_preview_link',
        'error',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'fields' => 'array',
            'category_ids' => 'array',
            'tags' => 'array',
            'parent_id' => 'integer',
            'author_id' => 'integer',
            'menu_order' => 'integer',
            'wp_post_id' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<WordpressSite, $this>
     */
    public function site(): BelongsTo
    {
        return $this->belongsTo(WordpressSite::class, 'wordpress_site_id');
    }
}
