<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;
use App\Models\Department;
use App\Models\Role;

class Movement extends Model
{
    use SoftDeletes;

    /**
     * Nome da tabela
     */
    protected $table = 'movements';

    /**
     * Nome da chave primária
     */
    protected $primaryKey = 'movement_id';

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
        'worker_id',
        'old_department_id',
        'new_department_id',
        'old_role_id',
        'new_role_id',
        'status',
        'observation',
        'rejection_reason',
        'requested_by',
        'approved_by',
        'rejected_by',
        'approved_at',
        'rejected_at',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relacionamento: Uma movimentação pertence a um funcionário
     */
    public function worker(): BelongsTo
    {
        return $this->belongsTo(Worker::class, 'worker_id', 'worker_id');
    }

    /**
     * Relacionamento: Departamento antigo
     */
    public function oldDepartment(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'old_department_id', 'department_id');
    }

    /**
     * Relacionamento: Departamento novo
     */
    public function newDepartment(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'new_department_id', 'department_id');
    }

    /**
     * Relacionamento: Cargo antigo
     */
    public function oldRole(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'old_role_id', 'role_id');
    }

    /**
     * Relacionamento: Cargo novo
     */
    public function newRole(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'new_role_id', 'role_id');
    }

    /**
     * Relacionamento: Usuário que solicitou
     */
    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by', 'users_id');
    }

    /**
     * Relacionamento: Usuário que aprovou
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by', 'users_id');
    }

    /**
     * Relacionamento: Usuário que rejeitou
     */
    public function rejecter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by', 'users_id');
    }
}
