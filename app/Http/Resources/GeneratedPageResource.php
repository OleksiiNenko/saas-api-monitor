<?php

namespace App\Http\Resources;

use App\Models\GeneratedPage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin GeneratedPage
 */
class GeneratedPageResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'wordpress_site_id' => $this->wordpress_site_id,
            'topic' => $this->topic,
            'page_type' => $this->page_type,
            'audience' => $this->audience,
            'tone' => $this->tone,
            'language' => $this->language,
            'keywords' => $this->keywords,
            'sections' => $this->sections,
            'cta' => $this->cta,
            'extra_instructions' => $this->extra_instructions,
            'fields' => $this->fields,
            'title' => $this->title,
            'slug' => $this->slug,
            'parent_id' => $this->parent_id,
            'category_ids' => $this->category_ids,
            'tags' => $this->tags,
            'author_id' => $this->author_id,
            'menu_order' => $this->menu_order,
            'status' => $this->status,
            'wp_post_id' => $this->wp_post_id,
            'wp_edit_link' => $this->wp_edit_link,
            'wp_preview_link' => $this->wp_preview_link,
            'error' => $this->error,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
