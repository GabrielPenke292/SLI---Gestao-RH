<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StepInteraction extends Model
{
    /**
     * Nome da tabela
     */
    protected $table = 'step_interactions';

    /**
     * Nome da chave primária
     */
    protected $primaryKey = 'step_interaction_id';

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
        'step',
        'interaction_type',
        'question',
        'answer',
        'observation',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relacionamento: Uma interação pertence a um processo seletivo
     */
    public function selectionProcess(): BelongsTo
    {
        return $this->belongsTo(SelectionProcess::class, 'selection_process_id', 'selection_process_id');
    }

    /**
     * Relacionamento: Uma interação pertence a um candidato
     */
    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class, 'candidate_id', 'candidate_id');
    }
}
