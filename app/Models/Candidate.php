<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Candidate extends Model
{
    use SoftDeletes;

    /**
     * Nome da tabela
     */
    protected $table = 'candidates';

    /**
     * Nome da chave primária
     */
    protected $primaryKey = 'candidate_id';

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
        'candidate_name',
        'candidate_email',
        'candidate_phone',
        'candidate_document',
        'candidate_rg',
        'candidate_birth_date',
        'candidate_address',
        'candidate_city',
        'candidate_state',
        'candidate_zipcode',
        'candidate_experience',
        'candidate_education',
        'candidate_skills',
        'candidate_resume_text',
        'candidate_resume_pdf',
        'candidate_notes',
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
        'candidate_birth_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Acessor para obter a URL do PDF do currículo
     */
    public function getResumePdfUrlAttribute(): ?string
    {
        if (!$this->candidate_resume_pdf) {
            return null;
        }
        return asset('storage/' . $this->candidate_resume_pdf);
    }

    /**
     * Acessor para verificar se tem PDF
     * Retorna true se houver um caminho cadastrado no banco
     */
    public function getHasResumePdfAttribute(): bool
    {
        return !empty($this->candidate_resume_pdf);
    }

    /**
     * Relacionamento: Um candidato pode estar em muitos processos seletivos (many-to-many)
     */
    public function selectionProcesses(): BelongsToMany
    {
        return $this->belongsToMany(SelectionProcess::class, 'selection_process_candidates', 'candidate_id', 'selection_process_id')
            ->withPivot('status', 'notes', 'created_at', 'created_by', 'updated_at', 'updated_by');
    }
}
