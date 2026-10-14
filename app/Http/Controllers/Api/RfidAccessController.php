<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Dispositivo;
use App\Models\LecturaRfid;
use App\Models\Locker;
use App\Models\TarjetaRfid;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class RfidAccessController extends Controller
{
    public function validateAccess(Request $request): JsonResponse
    {
        $deviceValidator = Validator::make($request->all(), [
            'dispositivo_id' => ['required', 'integer', 'exists:dispositivos,id'],
        ]);

        if ($deviceValidator->fails()) {
            return $this->response(
                false,
                'El identificador del dispositivo es obligatorio y debe ser válido.',
                null,
                null,
                Response::HTTP_UNPROCESSABLE_ENTITY,
                $deviceValidator->errors()->toArray(),
            );
        }

        $device = Dispositivo::findOrFail((int) $request->input('dispositivo_id'));
        $providedToken = $request->header('X-Device-Token');

        if (! is_string($providedToken) || $providedToken === '' || ! hash_equals($device->token, $providedToken)) {
            $reading = $this->recordReading(
                $device,
                null,
                null,
                null,
                false,
                'Token de dispositivo inválido.',
            );

            return $this->response(
                false,
                'El token del dispositivo no es válido.',
                $reading,
                null,
                Response::HTTP_UNAUTHORIZED,
            );
        }

        if ($device->estado !== 'activo') {
            $reading = $this->recordReading(
                $device,
                null,
                null,
                null,
                false,
                'Dispositivo inactivo.',
            );

            return $this->response(
                false,
                'El dispositivo no está activo.',
                $reading,
                null,
                Response::HTTP_FORBIDDEN,
            );
        }

        $inputValidator = Validator::make($request->all(), [
            'uid' => ['required', 'string', 'max:255'],
            'locker_id' => ['required', 'integer', 'exists:lockers,id'],
        ]);

        if ($inputValidator->fails()) {
            $uid = $request->input('uid');
            $card = is_string($uid) ? TarjetaRfid::where('uid', $uid)->first() : null;
            $lockerId = $request->input('locker_id');
            $locker = is_numeric($lockerId) ? Locker::find((int) $lockerId) : null;
            $reason = 'Datos incompletos o inválidos: '.implode(', ', array_keys($inputValidator->errors()->toArray()));
            $reading = $this->recordReading(
                $device,
                $card,
                $card?->usuario,
                $locker,
                false,
                $reason,
            );

            return $this->response(
                false,
                'Faltan datos válidos para evaluar el acceso.',
                $reading,
                $locker,
                Response::HTTP_UNPROCESSABLE_ENTITY,
                $inputValidator->errors()->toArray(),
            );
        }

        $uid = $request->string('uid')->toString();
        $locker = Locker::findOrFail((int) $request->input('locker_id'));

        return DB::transaction(function () use ($device, $uid, $locker): JsonResponse {
            $card = TarjetaRfid::with('usuario')->where('uid', $uid)->first();

            if (! $card) {
                return $this->denied($device, null, null, $locker, 'Tarjeta RFID no registrada.');
            }

            $user = $card->usuario;

            if ($card->estado !== 'activa') {
                return $this->denied($device, $card, $user, $locker, 'Tarjeta RFID inactiva.');
            }

            if ($user->estado !== 'activo') {
                return $this->denied($device, $card, $user, $locker, 'Usuario inactivo.');
            }

            if (! Gate::forUser($user)->allows('view', $locker)) {
                return $this->denied($device, $card, $user, $locker, 'Sin asignación activa para este locker.');
            }

            $reading = $this->recordReading(
                $device,
                $card,
                $user,
                $locker,
                true,
                'Acceso autorizado por asignación vigente.',
            );

            return $this->response(
                true,
                'Acceso autorizado.',
                $reading,
                $locker,
                Response::HTTP_OK,
            );
        });
    }

    private function denied(
        Dispositivo $device,
        ?TarjetaRfid $card,
        ?User $user,
        Locker $locker,
        string $reason,
    ): JsonResponse {
        $reading = $this->recordReading($device, $card, $user, $locker, false, $reason);

        return $this->response(false, $reason, $reading, $locker, Response::HTTP_FORBIDDEN);
    }

    private function recordReading(
        Dispositivo $device,
        ?TarjetaRfid $card,
        ?User $user,
        ?Locker $locker,
        bool $authorized,
        string $reason,
    ): LecturaRfid {
        return LecturaRfid::create([
            'tarjeta_rfid_id' => $card?->id,
            'usuario_id' => $user?->id,
            'locker_id' => $locker?->id,
            'dispositivo_id' => $device->id,
            'tipo_acceso' => 'apertura',
            'resultado' => $authorized ? 'permitido' : 'denegado',
            'motivo' => $reason,
            'fecha_hora' => now(),
        ]);
    }

    /**
     * @param  array<string, array<int, string>>  $errors
     */
    private function response(
        bool $authorized,
        string $message,
        ?LecturaRfid $reading,
        ?Locker $locker,
        int $status,
        array $errors = [],
    ): JsonResponse {
        $payload = [
            'autorizado' => $authorized,
            'mensaje' => $message,
            'data' => [
                'lectura_id' => $reading?->id,
                'locker_id' => $locker?->id ?? $reading?->locker_id,
                'locker_numero' => $locker?->numero,
            ],
        ];

        if ($errors !== []) {
            $payload['errores'] = $errors;
        }

        return response()->json($payload, $status);
    }
}
