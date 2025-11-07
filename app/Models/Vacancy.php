<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vacancy extends Model
{
    use SoftDeletes;

    /**
     * Nome da tabela
     */
    protected $table = 'vacancies';

    /**
     * Nome da chave primária
     */
    protected $primaryKey = 'vacancy_id';

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
        'vacancy_title',
        'vacancy_description',
        'urgency_level',
        'salary',
        'work_type',
        'work_schedule',
        'requirements',
        'benefits',
        'department_id',
        'status',
        'opening_date',
        'closing_date',
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
        'salary' => 'decimal:2',
        'opening_date' => 'date',
        'closing_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Relacionamento: Uma vaga pertence a um departamento
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id', 'department_id');
    }

    /**
     * Relacionamento: Uma vaga pode ter muitos processos seletivos
     * (será implementado quando os processos seletivos forem criados)
     */
    // public function selectionProcesses(): HasMany
    // {
    //     return $this->hasMany(SelectionProcess::class, 'vacancy_id', 'vacancy_id');
    // }

    /**
     * Scope para vagas abertas
     */
    public function scopeOpen($query)
    {
        return $query->where('status', 'aberta');
    }

    /**
     * Scope para vagas encerradas
     */
    public function scopeClosed($query)
    {
        return $query->where('status', 'encerrada');
    }

    /**
     * Scope para vagas pausadas
     */
    public function scopePaused($query)
    {
        return $query->where('status', 'pausada');
    }

    /**
     * Scope para filtrar por grau de urgência
     */
    public function scopeByUrgency($query, $urgency)
    {
        return $query->where('urgency_level', $urgency);
    }
}
