<?php

namespace App\Services\Ai;

use App\Models\GeneratedPage;
use App\Models\WordpressSite;
use App\Support\AcfFieldMap;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Generates ACF field values for a page using OpenAI's chat completions API.
 *
 * The model is constrained with response_format=json_object and a system prompt
 * that lists exactly the logical field names from the site's field map, so the
 * returned JSON keys line up with what FieldMapper expects.
 */
class AcfPageGenerator
{
    /**
     * @return array<string, mixed> logical field name => generated value
     */
    public function generate(WordpressSite $site, GeneratedPage $brief): array
    {
        $key = config('services.openai.key');

        if (empty($key)) {
            throw new RuntimeException('OPENAI_API_KEY не настроен в .env');
        }

        $map = $site->field_map ?: AcfFieldMap::default();

        $response = Http::withToken($key)
            ->acceptJson()
            ->timeout(120)
            ->post(rtrim(config('services.openai.base_url'), '/').'/chat/completions', [
                'model' => config('services.openai.model'),
                'response_format' => ['type' => 'json_object'],
                'temperature' => 0.7,
                'messages' => [
                    ['role' => 'system', 'content' => $this->systemPrompt($map)],
                    ['role' => 'user', 'content' => $this->userPrompt($brief)],
                ],
            ]);

        if (! $response->successful()) {
            $detail = $response->json('error.message') ?? 'Unexpected response';
            throw new RuntimeException('OpenAI вернул ошибку ('.$response->status().'): '.$detail);
        }

        $content = $response->json('choices.0.message.content');
        $decoded = json_decode((string) $content, true);

        if (! is_array($decoded)) {
            throw new RuntimeException('OpenAI вернул некорректный JSON.');
        }

        // Keep only known logical names.
        return array_intersect_key($decoded, $map);
    }

    /**
     * @param  array<string, array{key: string, type: string, block: string}>  $map
     */
    private function systemPrompt(array $map): string
    {
        $lines = [];
        foreach ($map as $name => $def) {
            $hint = $def['type'] === 'wysiwyg'
                ? 'HTML (h2,h3,p,ul,ol,li,strong,em,a — без script/style/inline-стилей, без <html>/<body>)'
                : 'обычный текст без HTML';
            $lines[] = "- {$name} [{$def['block']}]: {$hint}";
        }
        $fieldList = implode("\n", $lines);

        return <<<PROMPT
            Ты — опытный SEO-копирайтер для сайтов на WordPress. Сгенерируй контент для ОДНОЙ страницы.
            Верни СТРОГО валидный JSON-объект. Ключи — РОВНО из списка ниже (используй только нужные, не выдумывай новых).
            Не добавляй markdown, не оборачивай в ```; только JSON.

            Доступные поля (ключ → формат значения):
            {$fieldList}

            Правила:
            - Текст на языке, указанном пользователем.
            - seo_title_injector ≤ 60 символов, seo_description_injector ≤ 160 символов.
            - Заголовки блоков короткие; описания — содержательные и уникальные.
            - НЕ упоминай поля изображений/галерей — их не существует в JSON.
            PROMPT;
    }

    private function userPrompt(GeneratedPage $brief): string
    {
        $parts = [
            'Тема/цель страницы: '.$brief->topic,
            'Тип страницы: '.$brief->page_type,
            'Язык: '.$brief->language,
        ];

        if ($brief->audience) {
            $parts[] = 'Аудитория: '.$brief->audience;
        }
        if ($brief->tone) {
            $parts[] = 'Тон: '.$brief->tone;
        }
        if ($brief->keywords) {
            $parts[] = 'Ключевые слова (SEO): '.$brief->keywords;
        }
        if ($brief->cta) {
            $parts[] = 'Призыв к действию (CTA): '.$brief->cta;
        }
        if ($brief->sections) {
            $parts[] = 'Желаемые секции: '.$brief->sections;
        }
        if ($brief->extra_instructions) {
            $parts[] = 'Доп. инструкции: '.$brief->extra_instructions;
        }

        return implode("\n", $parts);
    }
}
