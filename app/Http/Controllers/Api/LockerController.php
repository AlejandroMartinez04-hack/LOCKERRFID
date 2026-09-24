<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLockerRequest;
use App\Http\Requests\UpdateLockerRequest;
use App\Http\Resources\LockerResource;
use App\Models\Locker;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpKernel\Exception\HttpException;

class LockerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        Gate::authorize('viewAny', Locker::class);

        $lockers = Locker::with('asignacionActiva.usuario')->get();

        return LockerResource::collection($lockers);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLockerRequest $request): JsonResponse
    {
        Gate::authorize('create', Locker::class);

        $data = $request->validated();
        if (($data['estado'] ?? 'disponible') === 'ocupado') {
            throw new HttpException(
                Response::HTTP_CONFLICT,
                'Un locker solo puede quedar ocupado mediante una asignación activa.',
            );
        }

        $locker = Locker::create($data);

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
        Gate::authorize('view', $locker);

        return new LockerResource($locker);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLockerRequest $request, string $id): LockerResource
    {
        $locker = Locker::findOrFail($id);
        Gate::authorize('update', $locker);
        $data = $request->validated();

        if (array_key_exists('estado', $data)) {
            $hasActiveAssignment = $locker->asignacionActiva()->exists();
            $state = $data['estado'];

            if ($hasActiveAssignment && $state !== 'ocupado') {
                throw new HttpException(
                    Response::HTTP_CONFLICT,
                    'Un locker con asignación activa debe permanecer ocupado.',
                );
            }

            if (! $hasActiveAssignment && $state === 'ocupado') {
                throw new HttpException(
                    Response::HTTP_CONFLICT,
                    'Un locker solo puede quedar ocupado mediante una asignación activa.',
                );
            }
        }

        $locker->update($data);

        return new LockerResource($locker->load('asignacionActiva.usuario'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): Response
    {
        $locker = Locker::findOrFail($id);
        Gate::authorize('delete', $locker);
        $locker->delete();

        return response()->noContent();
    }

    /**
     * Display the authenticated user's active locker.
     */
    public function miLocker(Request $request): LockerResource|JsonResponse
    {
        $asignacion = $request->user()
            ->asignacionesLockers()
            ->where('estado', 'activa')
            ->with('locker')
            ->first();

        if (! $asignacion) {
            return response()->json([
                'message' => 'No tienes un locker asignado.',
            ], Response::HTTP_NOT_FOUND);
        }

        $locker = $asignacion->locker;
        Gate::authorize('view', $locker);

        return new LockerResource($locker);
    }
}
