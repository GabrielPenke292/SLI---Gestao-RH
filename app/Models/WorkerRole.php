<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkerRole extends Model
{
    /**
     * Nome da tabela
     */
    protected $table = 'worker_roles';

    /**
     * Nome da chave primária
     */
    protected $primaryKey = 'worker_role_id';

    /**
     * Indica se o model deve usar timestamps automáticos
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'worker_id',
        'role_id',
        'worker_role_status',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'worker_id' => 'integer',
        'role_id' => 'integer',
        'worker_role_status' => 'integer',
        'created_at' => 'datetime',
    ];

    /**
     * Relacionamento: Um worker_role pertence a um worker
     */
    public function worker(): BelongsTo
    {
        return $this->belongsTo(Worker::class, 'worker_id', 'worker_id');
    }

    /**
     * Relacionamento: Um worker_role pertence a um role
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id', 'role_id');
    }
}
