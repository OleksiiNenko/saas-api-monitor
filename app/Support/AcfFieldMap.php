<?php

namespace App\Support;

/**
 * The ACF field map for the injector.ua template.
 *
 * Maps a stable logical name (what the AI returns and what the UI groups by) to
 * the concrete ACF field key on the target site. We write by KEY because ACF's
 * update_field() is reliable with keys, and names can collide across groups.
 *
 * Each entry: logical => ['key' => 'field_xxx', 'type' => 'text|wysiwyg|textarea', 'block' => 'Label']
 */
class AcfFieldMap
{
    /**
     * @return array<string, array{key: string, type: string, block: string}>
     */
    public static function default(): array
    {
        return [
            // SEO group
            'seo_title_injector' => ['key' => 'field_5f7f04a24dfd8', 'type' => 'text', 'block' => 'SEO'],
            'seo_description_injector' => ['key' => 'field_5f7f05724dfd9', 'type' => 'text', 'block' => 'SEO'],
            'seo_keywords_injector' => ['key' => 'field_5f7f082477ff0', 'type' => 'text', 'block' => 'SEO'],
            'seo_h1_injector' => ['key' => 'field_5f7f084077ff1', 'type' => 'text', 'block' => 'SEO'],

            // Блок #2 Заказать
            'block2_title' => ['key' => 'field_61c3057630412', 'type' => 'text', 'block' => 'Блок #2 Заказать'],
            'block2_desc' => ['key' => 'field_61c305b730413', 'type' => 'wysiwyg', 'block' => 'Блок #2 Заказать'],
            'block2_button' => ['key' => 'field_61c3104f043d7', 'type' => 'text', 'block' => 'Блок #2 Заказать'],

            // Блок #3 Цены
            'block3_title' => ['key' => 'field_61c3464e629fb', 'type' => 'text', 'block' => 'Блок #3 Цены'],
            'block3_desc' => ['key' => 'field_61c34669629fc', 'type' => 'wysiwyg', 'block' => 'Блок #3 Цены'],

            // Блок #4 УТП
            'block4_title1' => ['key' => 'field_61c34f12014bf', 'type' => 'text', 'block' => 'Блок #4 УТП'],
            'block4_desc1' => ['key' => 'field_61c34f49014c0', 'type' => 'text', 'block' => 'Блок #4 УТП'],
            'block4_title2' => ['key' => 'field_61c34f80014c1', 'type' => 'text', 'block' => 'Блок #4 УТП'],
            'block4_desc2' => ['key' => 'field_61c34f9c014c2', 'type' => 'text', 'block' => 'Блок #4 УТП'],
            'block4_title3' => ['key' => 'field_61c34fca014c3', 'type' => 'text', 'block' => 'Блок #4 УТП'],
            'block4_desc3' => ['key' => 'field_61c34ffa014c4', 'type' => 'text', 'block' => 'Блок #4 УТП'],

            // Блок #5 Преимущества
            'block5_title' => ['key' => 'field_61c443b7c8cfe', 'type' => 'text', 'block' => 'Блок #5 Преимущества'],
            'block5_title1' => ['key' => 'field_61c443e37f3ed', 'type' => 'text', 'block' => 'Блок #5 Преимущества'],
            'block5_desc1' => ['key' => 'field_61c444217f3ee', 'type' => 'text', 'block' => 'Блок #5 Преимущества'],
            'block5_title2' => ['key' => 'field_61c444727f3ef', 'type' => 'text', 'block' => 'Блок #5 Преимущества'],
            'block5_desc2' => ['key' => 'field_61c4448c7f3f0', 'type' => 'text', 'block' => 'Блок #5 Преимущества'],
            'block5_title3' => ['key' => 'field_61c444a67f3f1', 'type' => 'text', 'block' => 'Блок #5 Преимущества'],
            'block5_desc3' => ['key' => 'field_61c444ba7f3f2', 'type' => 'text', 'block' => 'Блок #5 Преимущества'],
            'block5_title4' => ['key' => 'field_61c444fd72fa7', 'type' => 'text', 'block' => 'Блок #5 Преимущества'],
            'block5_desc4' => ['key' => 'field_61c4451b72fa8', 'type' => 'text', 'block' => 'Блок #5 Преимущества'],
            'block5_title5' => ['key' => 'field_61c4453f72fa9', 'type' => 'text', 'block' => 'Блок #5 Преимущества'],
            'block5_desc5' => ['key' => 'field_61c4454f72faa', 'type' => 'text', 'block' => 'Блок #5 Преимущества'],
            'block5_title6' => ['key' => 'field_61c4457172fab', 'type' => 'text', 'block' => 'Блок #5 Преимущества'],
            'block5_desc6' => ['key' => 'field_61c4458172fac', 'type' => 'text', 'block' => 'Блок #5 Преимущества'],

            // Блок #6 Текст
            'block6_title' => ['key' => 'field_61c4475497e8c', 'type' => 'text', 'block' => 'Блок #6 Текст'],
            'block6_desc' => ['key' => 'field_61c4476497e8d', 'type' => 'wysiwyg', 'block' => 'Блок #6 Текст'],

            // Блок #7 Обратный звонок
            'block7_title' => ['key' => 'field_61c4487d47b8c', 'type' => 'text', 'block' => 'Блок #7 Обратный звонок'],
            'block7_desc' => ['key' => 'field_61c449bc47b8d', 'type' => 'text', 'block' => 'Блок #7 Обратный звонок'],
            'block7_button' => ['key' => 'field_61c449ce47b8e', 'type' => 'text', 'block' => 'Блок #7 Обратный звонок'],

            // Блок #8 Текст
            'block8_title' => ['key' => 'field_61c44b514fc5c', 'type' => 'text', 'block' => 'Блок #8 Текст'],
            'block8_desc' => ['key' => 'field_61c44b6f4fc5d', 'type' => 'wysiwyg', 'block' => 'Блок #8 Текст'],

            // Блок #9 Заказать
            'block9_title' => ['key' => 'field_61c44d20d2443', 'type' => 'text', 'block' => 'Блок #9 Заказать'],
            'block9_desc' => ['key' => 'field_61c44d40d2444', 'type' => 'textarea', 'block' => 'Блок #9 Заказать'],
            'block9_button' => ['key' => 'field_61c44d92d2446', 'type' => 'text', 'block' => 'Блок #9 Заказать'],

            // Блок #10 Отзывы
            'block10_title' => ['key' => 'field_61c44f7aa4217', 'type' => 'text', 'block' => 'Блок #10 Отзывы'],

            // Блок #10 Отзывы Google
            'block101_title' => ['key' => 'field_61c4652268a7f', 'type' => 'text', 'block' => 'Блок #10 Отзывы Google'],

            // Блок #11 Форма
            'block11_title' => ['key' => 'field_61c450e5f2da4', 'type' => 'text', 'block' => 'Блок #11 Форма'],
            'block11_desc' => ['key' => 'field_61c450fef2da5', 'type' => 'text', 'block' => 'Блок #11 Форма'],
        ];
    }

    /**
     * Logical names whose value is rich HTML (must be sanitized before push).
     *
     * @param  array<string, array{key: string, type: string, block: string}>  $map
     * @return list<string>
     */
    public static function wysiwygNames(array $map): array
    {
        return array_keys(array_filter($map, fn (array $f): bool => $f['type'] === 'wysiwyg'));
    }
}
