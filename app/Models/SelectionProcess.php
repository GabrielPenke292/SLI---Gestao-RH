<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SelectionProcess extends Model
{
    use SoftDeletes;

    /**
     * Nome da tabela
     */
    protected $table = 'selection_processes';

    /**
     * Nome da chave primária
     */
    protected $primaryKey = 'selection_process_id';

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
        'process_number',
        'vacancy_id',
        'reason',
        'approver_id',
        'budget',
        'status',
        'start_date',
        'end_date',
        'observations',
        'steps',
        'approval_notes',
        'approval_date',
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
        'budget' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'approval_date' => 'date',
        'steps' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Relacionamento: Um processo seletivo pertence a uma vaga
     */
    public function vacancy(): BelongsTo
    {
        return $this->belongsTo(Vacancy::class, 'vacancy_id', 'vacancy_id');
    }

    /**
     * Relacionamento: Um processo seletivo tem um aprovador
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_id', 'users_id');
    }

    /**
     * Relacionamento: Um processo seletivo pode ter muitos candidatos (many-to-many)
     */
    public function candidates(): BelongsToMany
    {
        return $this->belongsToMany(Candidate::class, 'selection_process_candidates', 'selection_process_id', 'candidate_id')
            ->withPivot('step', 'status', 'notes', 'created_at', 'created_by', 'updated_at', 'updated_by');
    }

    /**
     * Scope para processos aguardando aprovação
     */
    public function scopeAwaitingApproval($query)
    {
        return $query->where('status', 'aguardando_aprovacao');
    }

    /**
     * Scope para processos em andamento
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', 'em_andamento');
    }

    /**
     * Scope para processos encerrados
     */
    public function scopeFinished($query)
    {
        return $query->whereIn('status', ['encerrado', 'congelado', 'reprovado']);
    }
}
