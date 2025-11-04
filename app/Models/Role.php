<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    use SoftDeletes;

    /**
     * Nome da tabela
     */
    protected $table = 'roles';

    /**
     * Nome da chave primária
     */
    protected $primaryKey = 'role_id';

    /**
     * Indica se o model deve usar timestamps automáticos
     */
    public $timestamps = false;

    /**
     * Nome da coluna de soft delete
     */
    const DELETED_AT = 'deleted_at';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'role_name',
        'role_status',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'role_status' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Relacionamento many-to-many com workers
     */
    public function workers(): BelongsToMany
    {
        return $this->belongsToMany(
            Worker::class,
            'worker_roles',
            'role_id',
            'worker_id',
            'role_id',
            'worker_id'
        )->withPivot('worker_role_status', 'created_at', 'created_by');
    }
}
