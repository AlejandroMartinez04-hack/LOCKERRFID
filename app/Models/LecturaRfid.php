<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int|null $tarjeta_rfid_id
 * @property int|null $usuario_id
 * @property int|null $locker_id
 * @property int $dispositivo_id
 * @property string $tipo_acceso
 * @property string $resultado
 * @property string $motivo
 * @property Carbon $fecha_hora
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read TarjetaRfid|null $tarjetaRfid
 * @property-read User|null $usuario
 * @property-read Locker|null $locker
 * @property-read Dispositivo $dispositivo
 */
class LecturaRfid extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'lecturas_rfid';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'tarjeta_rfid_id',
        'usuario_id',
        'locker_id',
        'dispositivo_id',
        'tipo_acceso',
        'resultado',
        'motivo',
        'fecha_hora',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'fecha_hora' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<TarjetaRfid, $this>
     */
    public function tarjetaRfid(): BelongsTo
    {
        return $this->belongsTo(TarjetaRfid::class, 'tarjeta_rfid_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    /**
     * @return BelongsTo<Locker, $this>
     */
    public function locker(): BelongsTo
    {
        return $this->belongsTo(Locker::class, 'locker_id');
    }

    /**
     * @return BelongsTo<Dispositivo, $this>
     */
    public function dispositivo(): BelongsTo
    {
        return $this->belongsTo(Dispositivo::class, 'dispositivo_id');
    }
}
