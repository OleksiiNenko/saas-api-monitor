<?php

namespace App\Services\WordPress;

use App\Models\WordpressSite;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Talks to the ai-page-connector mu-plugin installed on the target WordPress
 * site. All requests are authenticated with a WordPress Application Password
 * over HTTPS only.
 */
class WordPressConnector
{
    private const NAMESPACE = 'ai-page/v1';

    private function client(WordpressSite $site): PendingRequest
    {
        return Http::withBasicAuth($site->username, $site->app_password)
            ->acceptJson()
            ->timeout(20)
            ->connectTimeout(10);
    }

    private function endpoint(WordpressSite $site, string $path): string
    {
        $base = rtrim($site->base_url, '/');

        // Force HTTPS — never send credentials in the clear.
        $base = preg_replace('#^http://#i', 'https://', $base);
        if (! str_starts_with($base, 'https://')) {
            $base = 'https://'.$base;
        }

        return $base.'/wp-json/'.self::NAMESPACE.'/'.ltrim($path, '/');
    }

    /**
     * @return array<string, mixed>
     */
    public function ping(WordpressSite $site): array
    {
        $response = $this->client($site)->get($this->endpoint($site, 'ping'));

        if (! $response->successful()) {
            throw new RuntimeException($this->errorMessage($response->status(), $response->json()));
        }

        return $response->json();
    }

    /**
     * @return array<string, mixed>
     */
    public function fetchMeta(WordpressSite $site): array
    {
        $response = $this->client($site)->get($this->endpoint($site, 'meta'));

        if (! $response->successful()) {
            throw new RuntimeException($this->errorMessage($response->status(), $response->json()));
        }

        return $response->json();
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function createDraft(WordpressSite $site, array $payload): array
    {
        $response = $this->client($site)->post($this->endpoint($site, 'pages'), $payload);

        if (! $response->successful()) {
            throw new RuntimeException($this->errorMessage($response->status(), $response->json()));
        }

        return $response->json();
    }

    /**
     * @param  mixed  $body
     */
    private function errorMessage(int $status, $body): string
    {
        $detail = is_array($body) && isset($body['message']) ? $body['message'] : 'Unexpected response';

        return match (true) {
            $status === 401, $status === 403 => 'Ошибка авторизации WordPress: проверьте логин и Application Password ('.$status.').',
            $status === 404 => 'Не найден коннектор на сайте: установлен ли mu-plugin ai-page-connector? (404)',
            default => 'WordPress вернул ошибку ('.$status.'): '.$detail,
        };
    }
}
