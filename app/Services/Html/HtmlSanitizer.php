<?php

namespace App\Services\Html;

use HTMLPurifier;
use HTMLPurifier_Config;

/**
 * Sanitizes AI-generated HTML before it is sent to WordPress.
 *
 * Allow-lists only semantic, content-level tags. Strips script/style/iframe,
 * inline event handlers, and inline styles. This is defense-in-depth on top of
 * WordPress' own wp_kses on the receiving end.
 */
class HtmlSanitizer
{
    private HTMLPurifier $purifier;

    public function __construct()
    {
        $config = HTMLPurifier_Config::createDefault();
        $config->set('HTML.Allowed', 'p,br,strong,b,em,i,u,ul,ol,li,h2,h3,h4,blockquote,a[href|title|rel],span');
        $config->set('AutoFormat.RemoveEmpty', true);
        $config->set('Attr.AllowedRel', ['nofollow', 'noopener', 'noreferrer']);

        // No custom HTML definition is used, so the on-disk definition cache is
        // unnecessary — disabling it avoids needing a writable cache directory.
        $config->set('Cache.DefinitionImpl', null);

        $this->purifier = new HTMLPurifier($config);
    }

    public function clean(string $html): string
    {
        return trim($this->purifier->purify($html));
    }
}
