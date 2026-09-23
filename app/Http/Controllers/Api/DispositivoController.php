<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDispositivoRequest;
use App\Http\Requests\UpdateDispositivoRequest;
use App\Http\Resources\DispositivoResource;
use App\Models\Dispositivo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class DispositivoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        return DispositivoResource::collection(Dispositivo::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDispositivoRequest $request): JsonResponse
    {
        $dispositivo = Dispositivo::create($request->validated());

        return (new DispositivoResource($dispositivo))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): DispositivoResource
    {
        $dispositivo = Dispositivo::findOrFail($id);

        return new DispositivoResource($dispositivo);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDispositivoRequest $request, string $id): DispositivoResource
    {
        $dispositivo = Dispositivo::findOrFail($id);
        $dispositivo->update($request->validated());

        return new DispositivoResource($dispositivo);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): Response
    {
        $dispositivo = Dispositivo::findOrFail($id);
        $dispositivo->delete();

        return response()->noContent();
    }
}
