<?php

namespace App\Services\WordPress;

use App\Models\WordpressSite;
use App\Services\Html\HtmlSanitizer;
use App\Support\AcfFieldMap;

/**
 * Translates AI output (keyed by logical field names) into the ACF payload the
 * mu-plugin expects (keyed by concrete ACF field keys), sanitizing rich text.
 *
 * Unknown logical names are silently dropped — this protects the target site
 * from the AI inventing fields that do not exist.
 */
class FieldMapper
{
    public function __construct(private HtmlSanitizer $sanitizer) {}

    /**
     * @param  array<string, mixed>  $aiFields  logical name => value
     * @return array<string, string> ACF field key => sanitized value
     */
    public function toAcfPayload(WordpressSite $site, array $aiFields): array
    {
        $map = $site->field_map ?: AcfFieldMap::default();
        $payload = [];

        foreach ($aiFields as $logicalName => $value) {
            if (! isset($map[$logicalName]) || $value === null || $value === '') {
                continue;
            }

            $definition = $map[$logicalName];
            $stringValue = is_string($value) ? $value : (string) $value;

            $payload[$definition['key']] = $definition['type'] === 'wysiwyg'
                ? $this->sanitizer->clean($stringValue)
                : strip_tags($stringValue);
        }

        return $payload;
    }
}
