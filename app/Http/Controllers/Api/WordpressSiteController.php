<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreWordpressSiteRequest;
use App\Http\Resources\WordpressSiteResource;
use App\Models\WordpressSite;
use App\Services\WordPress\WordPressConnector;
use App\Support\AcfFieldMap;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Throwable;

class WordpressSiteController extends Controller
{
    public function __construct(private WordPressConnector $connector) {}

    public function index(): AnonymousResourceCollection
    {
        return WordpressSiteResource::collection(
            WordpressSite::query()->latest()->get()
        );
    }

    /**
     * Create a site and immediately verify the connection via the mu-plugin.
     */
    public function store(StoreWordpressSiteRequest $request): JsonResponse
    {
        $site = new WordpressSite($request->validated());
        $site->field_map = AcfFieldMap::default();

        try {
            $ping = $this->connector->ping($site);
            $site->connector_version = $ping['connector_version'] ?? null;
            $site->last_connected_at = now();
        } catch (Throwable $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $site->save();

        return WordpressSiteResource::make($site)
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Re-test the connection.
     */
    public function test(WordpressSite $site): JsonResponse
    {
        try {
            $ping = $this->connector->ping($site);
            $site->update([
                'connector_version' => $ping['connector_version'] ?? $site->connector_version,
                'last_connected_at' => now(),
            ]);

            return response()->json(['ok' => true, 'ping' => $ping]);
        } catch (Throwable $e) {
            return response()->json(['ok' => false, 'message' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Proxy the site meta (pages, categories, tags, authors, languages) for the UI dropdowns.
     */
    public function meta(WordpressSite $site): JsonResponse
    {
        try {
            return response()->json($this->connector->fetchMeta($site));
        } catch (Throwable $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function destroy(WordpressSite $site): Response
    {
        $site->delete();

        return response()->noContent();
    }
}
