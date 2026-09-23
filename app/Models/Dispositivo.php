<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $nombre
 * @property string $ubicacion
 * @property string $token
 * @property string $estado
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Dispositivo extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'dispositivos';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nombre',
        'ubicacion',
        'token',
        'estado',
    ];

    /**
     * @return HasMany<LecturaRfid, $this>
     */
    public function lecturasRfid(): HasMany
    {
        return $this->hasMany(LecturaRfid::class, 'dispositivo_id');
    }
}
