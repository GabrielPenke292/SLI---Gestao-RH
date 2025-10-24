<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permission extends Model
{
    use SoftDeletes;

    /**
     * Nome da chave primária
     */
    protected $primaryKey = 'permissio_id';

    /**
     * Nome da tabela
     */
    protected $table = 'permissions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'permission_name',
        'permission_status',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected function casts(): array
    {
        return [
            'permission_status' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * Relacionamento many-to-many com users
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_permissions', 'permission_id', 'user_id');
    }

    /**
     * Scope para permissões ativas
     */
    public function scopeActive($query)
    {
        return $query->where('permission_status', 1);
    }

    /**
     * Scope para permissões inativas
     */
    public function scopeInactive($query)
    {
        return $query->where('permission_status', 0);
    }
}
