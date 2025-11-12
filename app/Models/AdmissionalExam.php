<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdmissionalExam extends Model
{
    use SoftDeletes;

    /**
     * Nome da tabela
     */
    protected $table = 'admissional_exams';

    /**
     * Nome da chave primária
     */
    protected $primaryKey = 'admissional_exam_id';

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
        'candidate_id',
        'selection_process_id',
        'clinic_id',
        'exam_date',
        'exam_time',
        'status',
        'cancellation_reason',
        'exam_result',
        'notes',
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
        'exam_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relacionamento: Um exame pertence a um candidato
     */
    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class, 'candidate_id', 'candidate_id');
    }

    /**
     * Relacionamento: Um exame pertence a um processo seletivo
     */
    public function selectionProcess(): BelongsTo
    {
        return $this->belongsTo(SelectionProcess::class, 'selection_process_id', 'selection_process_id');
    }

    /**
     * Relacionamento: Um exame pertence a uma clínica
     */
    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class, 'clinic_id', 'clinic_id');
    }
}
