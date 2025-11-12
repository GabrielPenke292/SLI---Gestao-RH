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
    const TYPE_CANDIDATE_APPROVED = 'candidate_approved';
    const TYPE_CANDIDATE_REJECTED = 'candidate_rejected';
    const TYPE_WORKER_MOVEMENT = 'worker_movement';
    const TYPE_WORKER_MOVEMENT_APPROVED = 'worker_movement_approved';
    const TYPE_WORKER_MOVEMENT_REJECTED = 'worker_movement_rejected';

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

    /**
     * Registrar aprovação de candidato no processo seletivo
     *
     * @param int $candidateId ID do candidato
     * @param int $processId ID do processo seletivo
     * @param string $step Etapa em que foi aprovado
     * @param string|null $observation Observação da aprovação
     * @return ActivityLog
     */
    public static function logCandidateApproved(int $candidateId, int $processId, string $step, ?string $observation = null): ActivityLog
    {
        $candidate = \App\Models\Candidate::find($candidateId);
        $process = \App\Models\SelectionProcess::find($processId);
        
        $description = sprintf(
            'Candidato "%s" foi aprovado na etapa "%s" do processo seletivo "%s"',
            $candidate->candidate_name ?? 'N/A',
            $step,
            $process->process_number ?? 'N/A'
        );
        
        if ($observation) {
            $description .= '. Observação: ' . $observation;
        }
        
        $metadata = [
            'candidate_id' => $candidateId,
            'candidate_name' => $candidate->candidate_name ?? null,
            'process_id' => $processId,
            'process_number' => $process->process_number ?? null,
            'step' => $step,
            'observation' => $observation,
        ];
        
        return self::log(
            self::TYPE_CANDIDATE_APPROVED,
            'Candidate',
            $candidateId,
            'Candidato aprovado no processo seletivo',
            $description,
            $metadata
        );
    }

    /**
     * Registrar reprovação de candidato no processo seletivo
     *
     * @param int $candidateId ID do candidato
     * @param int $processId ID do processo seletivo
     * @param string $step Etapa em que foi reprovado
     * @param string|null $observation Observação da reprovação
     * @return ActivityLog
     */
    public static function logCandidateRejected(int $candidateId, int $processId, string $step, ?string $observation = null): ActivityLog
    {
        $candidate = \App\Models\Candidate::find($candidateId);
        $process = \App\Models\SelectionProcess::find($processId);
        
        $description = sprintf(
            'Candidato "%s" foi reprovado na etapa "%s" do processo seletivo "%s"',
            $candidate->candidate_name ?? 'N/A',
            $step,
            $process->process_number ?? 'N/A'
        );
        
        if ($observation) {
            $description .= '. Observação: ' . $observation;
        }
        
        $metadata = [
            'candidate_id' => $candidateId,
            'candidate_name' => $candidate->candidate_name ?? null,
            'process_id' => $processId,
            'process_number' => $process->process_number ?? null,
            'step' => $step,
            'observation' => $observation,
        ];
        
        return self::log(
            self::TYPE_CANDIDATE_REJECTED,
            'Candidate',
            $candidateId,
            'Candidato reprovado no processo seletivo',
            $description,
            $metadata
        );
    }

    /**
     * Registrar movimentação de cargo de funcionário
     *
     * @param int $movementId ID da movimentação
     * @param int $workerId ID do funcionário
     * @param string|null $oldDepartment Nome do departamento antigo
     * @param string|null $newDepartment Nome do departamento novo
     * @param string|null $oldRole Nome do cargo antigo
     * @param string|null $newRole Nome do cargo novo
     * @param string|null $observation Observação
     * @return ActivityLog
     */
    public static function logWorkerMovement(int $movementId, int $workerId, ?string $oldDepartment = null, ?string $newDepartment = null, ?string $oldRole = null, ?string $newRole = null, ?string $observation = null): ActivityLog
    {
        $worker = \App\Models\Worker::find($workerId);
        
        $description = sprintf(
            'Movimentação de cargo solicitada para "%s"',
            $worker->worker_name ?? 'N/A'
        );
        
        if ($oldDepartment && $newDepartment) {
            $description .= sprintf(' - Departamento: %s → %s', $oldDepartment, $newDepartment);
        }
        
        if ($oldRole && $newRole) {
            $description .= sprintf(' - Cargo: %s → %s', $oldRole, $newRole);
        }
        
        if ($observation) {
            $description .= '. Observação: ' . $observation;
        }
        
        $metadata = [
            'movement_id' => $movementId,
            'worker_id' => $workerId,
            'worker_name' => $worker->worker_name ?? null,
            'old_department' => $oldDepartment,
            'new_department' => $newDepartment,
            'old_role' => $oldRole,
            'new_role' => $newRole,
            'observation' => $observation,
        ];
        
        return self::log(
            self::TYPE_WORKER_MOVEMENT,
            'Worker',
            $workerId,
            'Movimentação de cargo solicitada',
            $description,
            $metadata
        );
    }

    /**
     * Registrar aprovação de movimentação
     *
     * @param int $movementId ID da movimentação
     * @param int $workerId ID do funcionário
     * @param string|null $oldDepartment Nome do departamento antigo
     * @param string|null $newDepartment Nome do departamento novo
     * @param string|null $oldRole Nome do cargo antigo
     * @param string|null $newRole Nome do cargo novo
     * @return ActivityLog
     */
    public static function logWorkerMovementApproved(int $movementId, int $workerId, ?string $oldDepartment = null, ?string $newDepartment = null, ?string $oldRole = null, ?string $newRole = null): ActivityLog
    {
        $worker = \App\Models\Worker::find($workerId);
        
        $description = sprintf(
            'Movimentação de cargo aprovada para "%s"',
            $worker->worker_name ?? 'N/A'
        );
        
        if ($oldDepartment && $newDepartment) {
            $description .= sprintf(' - Departamento: %s → %s', $oldDepartment, $newDepartment);
        }
        
        if ($oldRole && $newRole) {
            $description .= sprintf(' - Cargo: %s → %s', $oldRole, $newRole);
        }
        
        $metadata = [
            'movement_id' => $movementId,
            'worker_id' => $workerId,
            'worker_name' => $worker->worker_name ?? null,
            'old_department' => $oldDepartment,
            'new_department' => $newDepartment,
            'old_role' => $oldRole,
            'new_role' => $newRole,
        ];
        
        return self::log(
            self::TYPE_WORKER_MOVEMENT_APPROVED,
            'Worker',
            $workerId,
            'Movimentação de cargo aprovada',
            $description,
            $metadata
        );
    }

    /**
     * Registrar rejeição de movimentação
     *
     * @param int $movementId ID da movimentação
     * @param int $workerId ID do funcionário
     * @param string|null $rejectionReason Motivo da rejeição
     * @return ActivityLog
     */
    public static function logWorkerMovementRejected(int $movementId, int $workerId, ?string $rejectionReason = null): ActivityLog
    {
        $worker = \App\Models\Worker::find($workerId);
        
        $description = sprintf(
            'Movimentação de cargo rejeitada para "%s"',
            $worker->worker_name ?? 'N/A'
        );
        
        if ($rejectionReason) {
            $description .= '. Motivo: ' . $rejectionReason;
        }
        
        $metadata = [
            'movement_id' => $movementId,
            'worker_id' => $workerId,
            'worker_name' => $worker->worker_name ?? null,
            'rejection_reason' => $rejectionReason,
        ];
        
        return self::log(
            self::TYPE_WORKER_MOVEMENT_REJECTED,
            'Worker',
            $workerId,
            'Movimentação de cargo rejeitada',
            $description,
            $metadata
        );
    }
}

