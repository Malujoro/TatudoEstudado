<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Assunto extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'assuntos';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'nome',
        'materia_id',
    ];

    public function materia(): BelongsTo
    {
        return $this->belongsTo(Materia::class);
    }

    public function sessoesEstudo(): HasMany
    {
        return $this->hasMany(SessaoEstudo::class);
    }

    public function caderno(): HasOne
    {
        return $this->hasOne(Caderno::class);
    }

    public function metrica(): HasOne
    {
        return $this->hasOne(Metrica::class);
    }
}
