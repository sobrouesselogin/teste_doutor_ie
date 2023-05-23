<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Indice;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Livro extends Model
{
    use HasFactory;

    protected $fillable = ['usuario_publicador_id', 'titulo'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'id',
        'usuario_publicador_id',
        'created_at',
        'updated_at'
    ];

    /**
     * One to many relation with index (Indice)
     */
    public function indices(): HasMany
    {
        return $this->hasMany(Indice::class);
    }

    /**
     * Get the user that published the book.
     */
    public function usuarioPublicador(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
