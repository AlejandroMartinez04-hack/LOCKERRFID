<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAsignacionLockerRequest;
use App\Http\Requests\UpdateAsignacionLockerRequest;
use App\Http\Resources\AsignacionLockerResource;
use App\Models\AsignacionLocker;
use App\Models\Locker;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AsignacionLockerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        Gate::authorize('viewAny', AsignacionLocker::class);

        $asignaciones = AsignacionLocker::with(['locker', 'usuario'])->get();

        return AsignacionLockerResource::collection($asignaciones);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAsignacionLockerRequest $request): JsonResponse
    {
        Gate::authorize('create', AsignacionLocker::class);

        $data = $request->validated();
        $asignacion = DB::transaction(function () use ($data): AsignacionLocker {
            $locker = Locker::query()
                ->lockForUpdate()
                ->findOrFail($data['locker_id']);

            $this->ensureLockerCanBeAssigned($locker);

            $data['estado'] = 'activa';
            $data['fecha_fin'] = null;
            $asignacion = AsignacionLocker::create($data);

            $locker->update(['estado' => 'ocupado']);

            return $asignacion;
        });

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
        Gate::authorize('view', $asignacion);

        return new AsignacionLockerResource($asignacion);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAsignacionLockerRequest $request, string $id): AsignacionLockerResource
    {
        $data = $request->validated();
        $asignacion = DB::transaction(function () use ($data, $id): AsignacionLocker {
            $asignacion = AsignacionLocker::query()
                ->lockForUpdate()
                ->findOrFail($id);
            Gate::authorize('update', $asignacion);
            $lockerAnterior = Locker::query()
                ->lockForUpdate()
                ->findOrFail($asignacion->locker_id);
            $lockerIdNuevo = $data['locker_id'] ?? $asignacion->locker_id;
            $lockerNuevo = $lockerAnterior;

            if ((int) $lockerIdNuevo !== (int) $asignacion->locker_id) {
                $lockerNuevo = Locker::query()
                    ->lockForUpdate()
                    ->findOrFail($lockerIdNuevo);
            }

            $estadoNuevo = $data['estado'] ?? $asignacion->estado;

            if ($estadoNuevo === 'activa') {
                if ($asignacion->estado !== 'activa' || $lockerNuevo->id !== $lockerAnterior->id) {
                    $this->ensureLockerCanBeAssigned($lockerNuevo, $asignacion->id);
                }

                if ($lockerNuevo->id !== $lockerAnterior->id) {
                    $this->releaseLockerIfAvailable($lockerAnterior, $asignacion->id);
                }

                $data['estado'] = 'activa';
                $data['fecha_fin'] = null;
                $lockerNuevo->update(['estado' => 'ocupado']);
            } else {
                $data['estado'] = $estadoNuevo;
                $data['fecha_fin'] = now();
                $this->releaseLockerIfAvailable($lockerAnterior, $asignacion->id);
            }

            $asignacion->update($data);

            return $asignacion;
        });

        return new AsignacionLockerResource($asignacion->load(['locker', 'usuario']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): Response
    {
        DB::transaction(function () use ($id): void {
            $asignacion = AsignacionLocker::query()
                ->lockForUpdate()
                ->findOrFail($id);
            Gate::authorize('delete', $asignacion);
            $locker = Locker::query()
                ->lockForUpdate()
                ->findOrFail($asignacion->locker_id);

            $asignacion->delete();
            $this->releaseLockerIfAvailable($locker, $asignacion->id);
        });

        return response()->noContent();
    }

    private function ensureLockerCanBeAssigned(Locker $locker, ?int $exceptAssignmentId = null): void
    {
        if ($locker->estado !== 'disponible') {
            throw new HttpException(
                Response::HTTP_CONFLICT,
                'El locker no está disponible para asignación.',
            );
        }

        $activeAssignment = $locker->asignaciones()
            ->where('estado', 'activa')
            ->when($exceptAssignmentId, fn ($query) => $query->whereKeyNot($exceptAssignmentId))
            ->exists();

        if ($activeAssignment) {
            throw new HttpException(
                Response::HTTP_CONFLICT,
                'El locker ya tiene una asignación activa.',
            );
        }
    }

    private function releaseLockerIfAvailable(Locker $locker, int $assignmentId): void
    {
        if ($locker->estado !== 'ocupado') {
            return;
        }

        $hasOtherActiveAssignment = $locker->asignaciones()
            ->where('estado', 'activa')
            ->whereKeyNot($assignmentId)
            ->exists();

        if (! $hasOtherActiveAssignment) {
            $locker->update(['estado' => 'disponible']);
        }
    }
}
