<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\GeneratePageRequest;
use App\Http\Requests\UpdateGeneratedPageRequest;
use App\Http\Resources\GeneratedPageResource;
use App\Models\GeneratedPage;
use App\Models\WordpressSite;
use App\Services\Ai\AcfPageGenerator;
use App\Services\WordPress\FieldMapper;
use App\Services\WordPress\WordPressConnector;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Throwable;

class GeneratedPageController extends Controller
{
    public function __construct(
        private AcfPageGenerator $generator,
        private FieldMapper $mapper,
        private WordPressConnector $connector,
    ) {}

    public function index(): AnonymousResourceCollection
    {
        return GeneratedPageResource::collection(
            GeneratedPage::query()->latest()->paginate()
        );
    }

    public function show(GeneratedPage $generatedPage): GeneratedPageResource
    {
        return GeneratedPageResource::make($generatedPage);
    }

    /**
     * Run the brief through the AI and persist the result. Nothing is sent to WordPress here.
     */
    public function generate(GeneratePageRequest $request): JsonResponse
    {
        $site = WordpressSite::findOrFail($request->validated('wordpress_site_id'));

        $page = new GeneratedPage($request->validated());
        $page->status = GeneratedPage::STATUS_GENERATED;

        try {
            $fields = $this->generator->generate($site, $page);
        } catch (Throwable $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $page->fields = $fields;
        $page->title = $fields['seo_h1_injector'] ?? $page->topic;
        $page->save();

        return GeneratedPageResource::make($page)
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Save user edits to the generated fields / meta before pushing.
     */
    public function update(UpdateGeneratedPageRequest $request, GeneratedPage $generatedPage): GeneratedPageResource
    {
        $generatedPage->update($request->validated());

        return GeneratedPageResource::make($generatedPage);
    }

    /**
     * Push the generated page to WordPress as a draft via the mu-plugin.
     */
    public function push(GeneratedPage $generatedPage): JsonResponse
    {
        $site = $generatedPage->site;

        $payload = [
            'title' => $generatedPage->title ?: $generatedPage->topic,
            'slug' => $generatedPage->slug,
            'parent' => $generatedPage->parent_id,
            'author' => $generatedPage->author_id,
            'menu_order' => $generatedPage->menu_order,
            'language' => $generatedPage->language,
            'categories' => $generatedPage->category_ids ?? [],
            'tags' => $generatedPage->tags ?? [],
            'acf' => $this->mapper->toAcfPayload($site, $generatedPage->fields ?? []),
        ];

        try {
            $result = $this->connector->createDraft($site, $payload);
        } catch (Throwable $e) {
            $generatedPage->update([
                'status' => GeneratedPage::STATUS_FAILED,
                'error' => $e->getMessage(),
            ]);

            return response()->json(['message' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $generatedPage->update([
            'status' => GeneratedPage::STATUS_PUSHED,
            'wp_post_id' => $result['id'] ?? null,
            'wp_edit_link' => $result['edit_link'] ?? null,
            'wp_preview_link' => $result['preview_link'] ?? null,
            'error' => null,
        ]);

        return GeneratedPageResource::make($generatedPage->refresh())->response();
    }
}
