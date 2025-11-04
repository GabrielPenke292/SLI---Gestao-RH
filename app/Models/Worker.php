<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Role;
use App\Models\Department;

class Worker extends Model
{
    use SoftDeletes;

    /**
     * Nome da tabela
     */
    protected $table = 'workers';

    /**
     * Nome da chave primária
     */
    protected $primaryKey = 'worker_id';

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
        'worker_name',
        'worker_email',
        'worker_document',
        'worker_rg',
        'worker_birth_date',
        'worker_start_date',
        'worker_status',
        'worker_salary',
        'department_id',
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
        'worker_birth_date' => 'date',
        'worker_start_date' => 'date',
        'worker_status' => 'integer',
        'worker_salary' => 'decimal:2',
        'department_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Relacionamento: Um funcionário pertence a um departamento
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id', 'department_id');
    }

    /**
     * Relacionamento many-to-many com roles
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            Role::class,
            'worker_roles',
            'worker_id',
            'role_id'
        )->withPivot('worker_role_status', 'created_at', 'created_by');
    }
}
