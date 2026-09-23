<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLockerRequest;
use App\Http\Requests\UpdateLockerRequest;
use App\Http\Resources\LockerResource;
use App\Models\Locker;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class LockerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        $lockers = Locker::with('asignacionActiva.usuario')->get();

        return LockerResource::collection($lockers);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLockerRequest $request): JsonResponse
    {
        $locker = Locker::create($request->validated());

        return (new LockerResource($locker))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): LockerResource
    {
        $locker = Locker::with('asignacionActiva.usuario')->findOrFail($id);

        return new LockerResource($locker);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLockerRequest $request, string $id): LockerResource
    {
        $locker = Locker::findOrFail($id);
        $locker->update($request->validated());

        return new LockerResource($locker->load('asignacionActiva.usuario'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): Response
    {
        $locker = Locker::findOrFail($id);
        $locker->delete();

        return response()->noContent();
    }
}
