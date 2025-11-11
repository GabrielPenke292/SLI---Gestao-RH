<?php

namespace App\Helpers;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLogger
{
    /**
     * Tipos de log disponíveis
     */
    const TYPE_CANDIDATE_LINKED = 'candidate_linked';
    const TYPE_CANDIDATE_UNLINKED = 'candidate_unlinked';
    const TYPE_CANDIDATE_STEP_MOVED = 'candidate_step_moved';
    const TYPE_PROCESS_CREATED = 'process_created';
    const TYPE_PROCESS_APPROVED = 'process_approved';
    const TYPE_PROCESS_REJECTED = 'process_rejected';
    const TYPE_PROCESS_UPDATED = 'process_updated';
    const TYPE_CANDIDATE_CREATED = 'candidate_created';
    const TYPE_CANDIDATE_UPDATED = 'candidate_updated';
    const TYPE_CANDIDATE_NOTE_ADDED = 'candidate_note_added';

    /**
     * Registrar uma atividade no log
     *
     * @param string $logType Tipo do log (constantes acima)
     * @param string $entityType Tipo da entidade (ex: 'Candidate', 'SelectionProcess')
     * @param int $entityId ID da entidade
     * @param string $action Ação realizada
     * @param string|null $description Descrição detalhada
     * @param array|null $metadata Dados adicionais em formato array
     * @return ActivityLog
     */
    public static function log(
        string $logType,
        string $entityType,
        int $entityId,
        string $action,
        ?string $description = null,
        ?array $metadata = null
    ): ActivityLog {
        $user = Auth::user();
        
        return ActivityLog::create([
            'log_type' => $logType,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'action' => $action,
            'description' => $description,
            'metadata' => $metadata,
            'user_id' => $user?->users_id,
            'user_name' => $user?->user_name ?? 'system',
            'ip_address' => Request::ip(),
            'created_at' => now(),
        ]);
    }

    /**
     * Registrar vinculação de candidato a processo seletivo
     *
     * @param int $candidateId ID do candidato
     * @param int $processId ID do processo seletivo
     * @param string|null $notes Observações adicionais
     * @return ActivityLog
     */
    public static function logCandidateLinked(int $candidateId, int $processId, ?string $notes = null): ActivityLog
    {
        $candidate = \App\Models\Candidate::find($candidateId);
        $process = \App\Models\SelectionProcess::find($processId);
        
        $description = sprintf(
            'Candidato "%s" foi vinculado ao processo seletivo "%s"',
            $candidate->candidate_name ?? 'N/A',
            $process->process_number ?? 'N/A'
        );
        
        $metadata = [
            'candidate_id' => $candidateId,
            'candidate_name' => $candidate->candidate_name ?? null,
            'process_id' => $processId,
            'process_number' => $process->process_number ?? null,
            'notes' => $notes,
        ];
        
        return self::log(
            self::TYPE_CANDIDATE_LINKED,
            'Candidate',
            $candidateId,
            'Candidato vinculado a processo seletivo',
            $description,
            $metadata
        );
    }

    /**
     * Registrar desvinculação de candidato de processo seletivo
     *
     * @param int $candidateId ID do candidato
     * @param int $processId ID do processo seletivo
     * @return ActivityLog
     */
    public static function logCandidateUnlinked(int $candidateId, int $processId): ActivityLog
    {
        $candidate = \App\Models\Candidate::find($candidateId);
        $process = \App\Models\SelectionProcess::find($processId);
        
        $description = sprintf(
            'Candidato "%s" foi desvinculado do processo seletivo "%s"',
            $candidate->candidate_name ?? 'N/A',
            $process->process_number ?? 'N/A'
        );
        
        $metadata = [
            'candidate_id' => $candidateId,
            'candidate_name' => $candidate->candidate_name ?? null,
            'process_id' => $processId,
            'process_number' => $process->process_number ?? null,
        ];
        
        return self::log(
            self::TYPE_CANDIDATE_UNLINKED,
            'Candidate',
            $candidateId,
            'Candidato desvinculado de processo seletivo',
            $description,
            $metadata
        );
    }

    /**
     * Registrar adição/atualização de observação sobre candidato no processo
     *
     * @param int $candidateId ID do candidato
     * @param int $processId ID do processo seletivo
     * @param string $note Observação adicionada
     * @return ActivityLog
     */
    public static function logCandidateNoteAdded(int $candidateId, int $processId, string $note): ActivityLog
    {
        $candidate = \App\Models\Candidate::find($candidateId);
        $process = \App\Models\SelectionProcess::find($processId);
        
        $description = sprintf(
            'Observação adicionada sobre o candidato "%s" no processo seletivo "%s"',
            $candidate->candidate_name ?? 'N/A',
            $process->process_number ?? 'N/A'
        );
        
        $metadata = [
            'candidate_id' => $candidateId,
            'candidate_name' => $candidate->candidate_name ?? null,
            'process_id' => $processId,
            'process_number' => $process->process_number ?? null,
            'note' => $note,
        ];
        
        return self::log(
            self::TYPE_CANDIDATE_NOTE_ADDED,
            'Candidate',
            $candidateId,
            'Observação adicionada sobre candidato',
            $description,
            $metadata
        );
    }

    /**
     * Registrar movimentação de candidato entre etapas do processo seletivo
     *
     * @param int $candidateId ID do candidato
     * @param int $processId ID do processo seletivo
     * @param string $fromStep Etapa de origem
     * @param string $toStep Etapa de destino
     * @return ActivityLog
     */
    public static function logCandidateStepMoved(int $candidateId, int $processId, string $fromStep, string $toStep): ActivityLog
    {
        $candidate = \App\Models\Candidate::find($candidateId);
        $process = \App\Models\SelectionProcess::find($processId);
        
        $description = sprintf(
            'Candidato "%s" foi movido da etapa "%s" para "%s" no processo seletivo "%s"',
            $candidate->candidate_name ?? 'N/A',
            $fromStep,
            $toStep,
            $process->process_number ?? 'N/A'
        );
        
        $metadata = [
            'candidate_id' => $candidateId,
            'candidate_name' => $candidate->candidate_name ?? null,
            'process_id' => $processId,
            'process_number' => $process->process_number ?? null,
            'from_step' => $fromStep,
            'to_step' => $toStep,
        ];
        
        return self::log(
            self::TYPE_CANDIDATE_STEP_MOVED,
            'Candidate',
            $candidateId,
            'Candidato movido entre etapas',
            $description,
            $metadata
        );
    }
}

