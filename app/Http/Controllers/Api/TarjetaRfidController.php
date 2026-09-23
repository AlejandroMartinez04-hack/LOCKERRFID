<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTarjetaRfidRequest;
use App\Http\Requests\UpdateTarjetaRfidRequest;
use App\Http\Resources\TarjetaRfidResource;
use App\Models\TarjetaRfid;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class TarjetaRfidController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        $tarjetas = TarjetaRfid::with('usuario')->get();

        return TarjetaRfidResource::collection($tarjetas);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTarjetaRfidRequest $request): JsonResponse
    {
        $tarjeta = TarjetaRfid::create($request->validated());

        return (new TarjetaRfidResource($tarjeta->load('usuario')))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): TarjetaRfidResource
    {
        $tarjeta = TarjetaRfid::with('usuario')->findOrFail($id);

        return new TarjetaRfidResource($tarjeta);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTarjetaRfidRequest $request, string $id): TarjetaRfidResource
    {
        $tarjeta = TarjetaRfid::findOrFail($id);
        $tarjeta->update($request->validated());

        return new TarjetaRfidResource($tarjeta->load('usuario'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): Response
    {
        $tarjeta = TarjetaRfid::findOrFail($id);
        $tarjeta->delete();

        return response()->noContent();
    }
}
