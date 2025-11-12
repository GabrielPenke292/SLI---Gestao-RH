<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Proposal extends Model
{
    /**
     * Nome da tabela
     */
    protected $table = 'proposals';

    /**
     * Nome da chave primária
     */
    protected $primaryKey = 'proposal_id';

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
        'selection_process_id',
        'candidate_id',
        'version',
        'parent_proposal_id',
        'salary',
        'contract_model',
        'workload',
        'benefits',
        'start_date',
        'additional_info',
        'proposal_file_path',
        'proposal_file_name',
        'status',
        'rejection_observation',
        'accepted_at',
        'rejected_at',
        'accepted_by',
        'rejected_by',
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
        'salary' => 'decimal:2',
        'start_date' => 'date',
        'accepted_at' => 'datetime',
        'rejected_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relacionamento: Uma proposta pertence a um processo seletivo
     */
    public function selectionProcess(): BelongsTo
    {
        return $this->belongsTo(SelectionProcess::class, 'selection_process_id', 'selection_process_id');
    }

    /**
     * Relacionamento: Uma proposta pertence a um candidato
     */
    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class, 'candidate_id', 'candidate_id');
    }

    /**
     * Relacionamento: Proposta pai (original)
     */
    public function parentProposal(): BelongsTo
    {
        return $this->belongsTo(Proposal::class, 'parent_proposal_id', 'proposal_id');
    }

    /**
     * Relacionamento: Contrapropostas (filhas)
     */
    public function counterProposals()
    {
        return $this->hasMany(Proposal::class, 'parent_proposal_id', 'proposal_id');
    }
}
