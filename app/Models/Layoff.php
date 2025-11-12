<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Layoff extends Model
{
    use SoftDeletes;

    /**
     * Nome da tabela
     */
    protected $table = 'layoffs';

    /**
     * Nome da chave primária
     */
    protected $primaryKey = 'layoff_id';

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
        'layoff_date',
        'layoff_type',
        'reason',
        'observations',
        'has_notice_period',
        'notice_period_days',
        'severance_pay',
        'severance_details',
        'returned_equipment',
        'equipment_details',
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
        'layoff_date' => 'date',
        'has_notice_period' => 'boolean',
        'returned_equipment' => 'boolean',
        'severance_pay' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relacionamento: Um desligamento pertence a um funcionário
     */
    public function worker(): BelongsTo
    {
        return $this->belongsTo(Worker::class, 'worker_id', 'worker_id');
    }
}
