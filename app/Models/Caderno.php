<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Caderno extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'cadernos';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'conteudo',
        'assunto_id',
    ];

    public function assunto(): BelongsTo
    {
        return $this->belongsTo(Assunto::class);
    }
}
