<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAsignacionLockerRequest;
use App\Http\Requests\UpdateAsignacionLockerRequest;
use App\Http\Resources\AsignacionLockerResource;
use App\Models\AsignacionLocker;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class AsignacionLockerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        $asignaciones = AsignacionLocker::with(['locker', 'usuario'])->get();

        return AsignacionLockerResource::collection($asignaciones);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAsignacionLockerRequest $request): JsonResponse
    {
        $asignacion = AsignacionLocker::create($request->validated());

        return (new AsignacionLockerResource($asignacion->load(['locker', 'usuario'])))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): AsignacionLockerResource
    {
        $asignacion = AsignacionLocker::with(['locker', 'usuario'])->findOrFail($id);

        return new AsignacionLockerResource($asignacion);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAsignacionLockerRequest $request, string $id): AsignacionLockerResource
    {
        $asignacion = AsignacionLocker::findOrFail($id);
        $asignacion->update($request->validated());

        return new AsignacionLockerResource($asignacion->load(['locker', 'usuario']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): Response
    {
        $asignacion = AsignacionLocker::findOrFail($id);
        $asignacion->delete();

        return response()->noContent();
    }
}
