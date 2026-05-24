<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMonitorRequest;
use App\Http\Requests\UpdateMonitorRequest;
use App\Http\Resources\MonitorResource;
use App\Models\Monitor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class MonitorController extends Controller
{
    /**
     * Display a paginated list of monitors.
     */
    public function index(): AnonymousResourceCollection
    {
        return MonitorResource::collection(
            Monitor::query()->latest()->paginate()
        );
    }

    /**
     * Store a newly created monitor.
     */
    public function store(StoreMonitorRequest $request): JsonResponse
    {
        $monitor = Monitor::create($request->validated());
        $monitor->refresh();

        return MonitorResource::make($monitor)
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified monitor.
     */
    public function show(Monitor $monitor): MonitorResource
    {
        return MonitorResource::make($monitor);
    }

    /**
     * Update the specified monitor.
     */
    public function update(UpdateMonitorRequest $request, Monitor $monitor): MonitorResource
    {
        $monitor->update($request->validated());

        return MonitorResource::make($monitor);
    }

    /**
     * Remove the specified monitor.
     */
    public function destroy(Monitor $monitor): Response
    {
        $monitor->delete();

        return response()->noContent();
    }
}
