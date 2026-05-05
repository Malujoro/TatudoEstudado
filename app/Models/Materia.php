<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model de matéria.
 *
 * @property string $id
 * @property string $nome
 * @property int $user_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class Materia extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'materias';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'nome',
        'user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assuntos(): HasMany
    {
        return $this->hasMany(Assunto::class);
    }
}
