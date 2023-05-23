<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Indice extends Model
{
    use HasFactory;

    protected $fillable = ['livro_id', 'indice_pai_id', 'titulo', 'pagina'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'livro_id',
        'indice_pai_id',
        'created_at',
        'updated_at'
    ];

    /**
     * One to many relation with index (Indice)
     */
    public function indices(): HasMany
    {
        return $this->hasMany(Indice::class, 'indice_pai_id');
    }

    /**
     * Get all levels of index (Indice)
     */
    public function subIndices()
    {
        return $this->hasMany(Indice::class, 'indice_pai_id')->with('indices');
    }
}
