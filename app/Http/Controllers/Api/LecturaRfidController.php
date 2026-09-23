<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLecturaRfidRequest;
use App\Http\Requests\UpdateLecturaRfidRequest;
use App\Http\Resources\LecturaRfidResource;
use App\Models\LecturaRfid;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class LecturaRfidController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        $lecturas = LecturaRfid::with(['tarjetaRfid', 'usuario', 'locker', 'dispositivo'])
            ->latest('fecha_hora')
            ->get();

        return LecturaRfidResource::collection($lecturas);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLecturaRfidRequest $request): JsonResponse
    {
        $lectura = LecturaRfid::create($request->validated());

        return (new LecturaRfidResource($lectura->load(['tarjetaRfid', 'usuario', 'locker', 'dispositivo'])))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): LecturaRfidResource
    {
        $lectura = LecturaRfid::with(['tarjetaRfid', 'usuario', 'locker', 'dispositivo'])->findOrFail($id);

        return new LecturaRfidResource($lectura);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLecturaRfidRequest $request, string $id): LecturaRfidResource
    {
        $lectura = LecturaRfid::findOrFail($id);
        $lectura->update($request->validated());

        return new LecturaRfidResource($lectura->load(['tarjetaRfid', 'usuario', 'locker', 'dispositivo']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): Response
    {
        $lectura = LecturaRfid::findOrFail($id);
        $lectura->delete();

        return response()->noContent();
    }
}
