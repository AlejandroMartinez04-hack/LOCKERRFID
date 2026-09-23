<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $numero
 * @property string $ubicacion
 * @property string $estado
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read AsignacionLocker|null $asignacionActiva
 */
class Locker extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'lockers';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'numero',
        'ubicacion',
        'estado',
    ];

    /**
     * @return HasMany<AsignacionLocker, $this>
     */
    public function asignaciones(): HasMany
    {
        return $this->hasMany(AsignacionLocker::class, 'locker_id');
    }

    /**
     * @return HasOne<AsignacionLocker, $this>
     */
    public function asignacionActiva(): HasOne
    {
        return $this->hasOne(AsignacionLocker::class, 'locker_id')->where('estado', 'activa');
    }

    /**
     * @return HasMany<LecturaRfid, $this>
     */
    public function lecturasRfid(): HasMany
    {
        return $this->hasMany(LecturaRfid::class, 'locker_id');
    }
}
