@extends('template.layout')

@push('styles')
<style>
    .form-label {
        font-weight: 500;
        margin-bottom: 0.5rem;
    }

    .required-field::after {
        content: " *";
        color: #dc3545;
    }

    .alert {
        border-radius: 0.375rem;
    }

    .candidates-section {
        border-top: 2px solid #dee2e6;
        margin-top: 2rem;
        padding-top: 2rem;
    }
    
    /* Estilos para drag and drop de etapas */
    #stepsContainer .badge {
        user-select: none;
        transition: opacity 0.2s;
    }
    
    #stepsContainer .badge:hover {
        opacity: 0.9;
        transform: scale(1.02);
    }
    
    #stepsContainer .badge.sortable-ghost {
        opacity: 0.4;
    }
    
    #stepsContainer .badge.sortable-drag {
        opacity: 0.8;
    }
    
    .opacity-50 {
        opacity: 0.5 !important;
    }
</style>
@endpush

@section('content')

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                            <h4 class="mb-0"><i class="fas fa-edit me-2"></i>Editar Processo Seletivo</h4>
                            <a href="{{ route('selections.index') }}" class="btn btn-light btn-sm">
                                <i class="fas fa-arrow-left me-1"></i>Voltar
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-12 mt-3">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            @if(session('error'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            @if($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="fas fa-exclamation-circle me-2"></i>
                                    <strong>Erro!</strong> Por favor, corrija os seguintes erros:
                                    <ul class="mb-0 mt-2">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            <form action="{{ route('selections.update', $process->selection_process_id) }}" method="POST" id="selectionForm">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="process_number" class="form-label required-field">Número do Processo</label>
                                        <input type="text" class="form-control @error('process_number') is-invalid @enderror" 
                                               id="process_number" name="process_number" 
                                               value="{{ old('process_number', $process->process_number) }}" 
                                               placeholder="Ex: PS-2025-001" 
                                               maxlength="50" required>
                                        @error('process_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="vacancy_id" class="form-label required-field">Vaga</label>
                                        <select class="form-select @error('vacancy_id') is-invalid @enderror" 
                                                id="vacancy_id" name="vacancy_id" required>
                                            <option value="">Selecione uma vaga...</option>
                                            @foreach($vacancies as $vacancy)
                                                <option value="{{ $vacancy->vacancy_id }}" {{ old('vacancy_id', $process->vacancy_id) == $vacancy->vacancy_id ? 'selected' : '' }}>
                                                    {{ $vacancy->vacancy_title }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('vacancy_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="reason" class="form-label required-field">Motivo do Processo</label>
                                        <select class="form-select @error('reason') is-invalid @enderror" 
                                                id="reason" name="reason" required>
                                            <option value="">Selecione...</option>
                                            <option value="substituicao" {{ old('reason', $process->reason) == 'substituicao' ? 'selected' : '' }}>Substituição</option>
                                            <option value="aumento_quadro" {{ old('reason', $process->reason) == 'aumento_quadro' ? 'selected' : '' }}>Aumento de Quadro</option>
                                        </select>
                                        @error('reason')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="status" class="form-label required-field">Status</label>
                                        <select class="form-select @error('status') is-invalid @enderror" 
                                                id="status" name="status" required>
                                            <option value="aguardando_aprovacao" {{ old('status', $process->status) == 'aguardando_aprovacao' ? 'selected' : '' }}>Aguardando Aprovação</option>
                                            <option value="em_andamento" {{ old('status', $process->status) == 'em_andamento' ? 'selected' : '' }}>Em Andamento</option>
                                            <option value="encerrado" {{ old('status', $process->status) == 'encerrado' ? 'selected' : '' }}>Encerrado</option>
                                            <option value="congelado" {{ old('status', $process->status) == 'congelado' ? 'selected' : '' }}>Congelado</option>
                                            <option value="reprovado" {{ old('status', $process->status) == 'reprovado' ? 'selected' : '' }}>Reprovado</option>
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="approver_id" class="form-label required-field">Aprovador</label>
                                        <select class="form-select @error('approver_id') is-invalid @enderror" 
                                                id="approver_id" name="approver_id" required>
                                            <option value="">Selecione um aprovador...</option>
                                            @foreach($approvers as $approver)
                                                <option value="{{ $approver->users_id }}" {{ old('approver_id', $process->approver_id) == $approver->users_id ? 'selected' : '' }}>
                                                    {{ $approver->user_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="form-text text-muted">Usuário que aprovará o processo (admin, diretoria ou gerente RH)</small>
                                        @error('approver_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="budget" class="form-label">Verba para Contratação</label>
                                        <div class="input-group">
                                            <span class="input-group-text">R$</span>
                                            <input type="number" 
                                                   class="form-control @error('budget') is-invalid @enderror" 
                                                   id="budget" 
                                                   name="budget" 
                                                   value="{{ old('budget', $process->budget) }}" 
                                                   placeholder="0.00" 
                                                   step="0.01" 
                                                   min="0">
                                        </div>
                                        @error('budget')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="start_date" class="form-label">Data de Início</label>
                                        <input type="date" 
                                               class="form-control @error('start_date') is-invalid @enderror" 
                                               id="start_date" 
                                               name="start_date" 
                                               value="{{ old('start_date', $process->start_date?->format('Y-m-d')) }}">
                                        @error('start_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="end_date" class="form-label">Data de Encerramento</label>
                                        <input type="date" 
                                               class="form-control @error('end_date') is-invalid @enderror" 
                                               id="end_date" 
                                               name="end_date" 
                                               value="{{ old('end_date', $process->end_date?->format('Y-m-d')) }}">
                                        @error('end_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="approval_date" class="form-label">Data de Aprovação</label>
                                        <input type="date" 
                                               class="form-control @error('approval_date') is-invalid @enderror" 
                                               id="approval_date" 
                                               name="approval_date" 
                                               value="{{ old('approval_date', $process->approval_date?->format('Y-m-d')) }}"
                                               readonly>
                                        <small class="form-text text-muted">Preenchida automaticamente ao aprovar</small>
                                        @error('approval_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <label for="process_steps" class="form-label">Etapas do Processo</label>
                                        <div class="input-group">
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="process_steps_input" 
                                                   placeholder="Digite o nome da etapa e pressione Enter...">
                                            <button type="button" class="btn btn-outline-secondary" id="btnAddStep">
                                                <i class="fas fa-plus"></i> Adicionar
                                            </button>
                                        </div>
                                        <small class="form-text text-muted">Adicione as etapas do processo seletivo (ex: Triagem, Entrevista, Teste Técnico, etc.)</small>
                                        <div id="stepsContainer" class="mt-2 d-flex flex-wrap gap-2">
                                            <!-- Badges das etapas serão inseridos aqui -->
                                        </div>
                                        <small class="form-text text-muted d-block mt-1">
                                            <i class="fas fa-info-circle me-1"></i>Você pode arrastar os badges para reordenar as etapas.
                                        </small>
                                        <input type="hidden" name="steps" id="stepsInput" value="{{ old('steps', $process->steps ? json_encode($process->steps) : '[]') }}">
                                        @error('steps')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <label for="observations" class="form-label">Observações</label>
                                        <textarea class="form-control @error('observations') is-invalid @enderror" 
                                                  id="observations" 
                                                  name="observations" 
                                                  rows="4" 
                                                  placeholder="Adicione observações sobre o processo seletivo...">{{ old('observations', $process->observations) }}</textarea>
                                        @error('observations')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <label for="approval_notes" class="form-label">Notas de Aprovação</label>
                                        <textarea class="form-control @error('approval_notes') is-invalid @enderror" 
                                                  id="approval_notes" 
                                                  name="approval_notes" 
                                                  rows="3" 
                                                  placeholder="Notas sobre a aprovação do processo...">{{ old('approval_notes', $process->approval_notes) }}</textarea>
                                        @error('approval_notes')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Seção de Etapas (Tabs) -->
                                @if($process->steps && count($process->steps) > 0 && $process->status === 'em_andamento')
                                <div class="steps-section mt-4 mb-4">
                                    <h5 class="mb-3"><i class="fas fa-list-ol me-2"></i>Etapas do Processo</h5>
                                    <ul class="nav nav-tabs" id="processStepsTabs" role="tablist">
                                        @foreach($process->steps as $index => $step)
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link {{ $index === 0 ? 'active' : '' }}" 
                                                        id="step-{{ $index }}-tab" 
                                                        data-bs-toggle="tab" 
                                                        data-bs-target="#step-{{ $index }}" 
                                                        type="button" 
                                                        role="tab" 
                                                        aria-controls="step-{{ $index }}" 
                                                        aria-selected="{{ $index === 0 ? 'true' : 'false' }}">
                                                    {{ $step }}
                                                </button>
                                            </li>
                                        @endforeach
                                    </ul>
                                    <div class="tab-content" id="processStepsTabContent">
                                        @foreach($process->steps as $index => $step)
                                            <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" 
                                                 id="step-{{ $index }}" 
                                                 role="tabpanel" 
                                                 aria-labelledby="step-{{ $index }}-tab"
                                                 data-step-name="{{ $step }}">
                                                <div class="card border-top-0 rounded-top-0">
                                                    <div class="card-body">
                                                        <!-- Seção de Candidatos da Etapa -->
                                                        @if($process->status === 'em_andamento')
                                                        <div class="candidates-section">
                                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                                <h6 class="mb-0"><i class="fas fa-users me-2"></i>Candidatos da Etapa: {{ $step }}</h6>
                                                                <div class="btn-group">
                                                                    <button type="button" class="btn btn-success btn-sm btn-manage-interactions" data-step="{{ $step }}" data-process-id="{{ $process->selection_process_id }}">
                                                                        <i class="fas fa-comments me-2"></i>Gerenciar Interações
                                                                    </button>
                                                                    <button type="button" class="btn btn-primary btn-sm btn-link-candidate-to-step" data-step="{{ $step }}" data-bs-toggle="modal" data-bs-target="#searchCandidateModal">
                                                                        <i class="fas fa-plus me-2"></i>Vincular Candidato
                                                                    </button>
                                                                </div>
                                                            </div>
                                                            
                                                            <div class="candidates-table-container" data-step="{{ $step }}">
                                                                <table class="table table-striped table-hover candidates-table">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Nome</th>
                                                                            <th>E-mail</th>
                                                                            <th>Telefone</th>
                                                                            <th>Status</th>
                                                                            <th>Data de Vinculação</th>
                                                                            <th>Ações</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @php
                                                                            $stepCandidates = $process->candidates->filter(function($candidate) use ($step) {
                                                                                return ($candidate->pivot->step ?? '') === $step;
                                                                            });
                                                                        @endphp
                                                                        @forelse($stepCandidates as $candidate)
                                                                            <tr data-candidate-id="{{ $candidate->candidate_id }}" data-step="{{ $candidate->pivot->step ?? '' }}">
                                                                                <td>{{ $candidate->candidate_name }}</td>
                                                                                <td>{{ $candidate->candidate_email ?? '-' }}</td>
                                                                                <td>{{ $candidate->candidate_phone ?? '-' }}</td>
                                                                                <td>
                                                                                    @php
                                                                                        $status = $candidate->pivot->status ?? 'pendente';
                                                                                        $badges = [
                                                                                            'pendente' => '<span class="badge bg-warning">Pendente</span>',
                                                                                            'aprovado' => '<span class="badge bg-success">Aprovado</span>',
                                                                                            'reprovado' => '<span class="badge bg-danger">Reprovado</span>',
                                                                                            'contratado' => '<span class="badge bg-info">Contratado</span>',
                                                                                        ];
                                                                                    @endphp
                                                                                    {!! $badges[$status] ?? '<span class="badge bg-secondary">' . $status . '</span>' !!}
                                                                                </td>
                                                                                <td>{{ $candidate->pivot->created_at?->format('d/m/Y H:i') ?? '-' }}</td>
                                                                                <td>
                                                                                    <div class="btn-group" role="group">
                                                                                        <a href="{{ route('candidates.show', $candidate->candidate_id) }}" target="_blank" class="btn btn-sm btn-info" title="Ver Perfil">
                                                                                            <i class="fas fa-eye"></i>
                                                                                        </a>
                                                                                        <button type="button" class="btn btn-sm btn-warning btn-add-note" data-candidate-id="{{ $candidate->candidate_id }}" data-candidate-name="{{ $candidate->candidate_name }}" data-current-notes="{{ $candidate->pivot->notes ?? '' }}" title="Adicionar Observação">
                                                                                            <i class="fas fa-sticky-note"></i>
                                                                                        </button>
                                                                                        @php
                                                                                            $currentStepIndex = array_search($step, $process->steps);
                                                                                            $hasPreviousStep = $currentStepIndex > 0;
                                                                                            $hasNextStep = $currentStepIndex < count($process->steps) - 1;
                                                                                            $isLastStep = $currentStepIndex === count($process->steps) - 1;
                                                                                            $candidateStatus = $candidate->pivot->status ?? 'pendente';
                                                                                            $canApprove = $isLastStep && $candidateStatus === 'pendente';
                                                                                            $canReject = $candidateStatus === 'pendente';
                                                                                        @endphp
                                                                                        @if($hasPreviousStep)
                                                                                            <button type="button" class="btn btn-sm btn-secondary btn-move-candidate" 
                                                                                                    data-candidate-id="{{ $candidate->candidate_id }}" 
                                                                                                    data-current-step="{{ $step }}"
                                                                                                    data-target-step="{{ $process->steps[$currentStepIndex - 1] }}"
                                                                                                    data-direction="left"
                                                                                                    title="Mover para {{ $process->steps[$currentStepIndex - 1] }}">
                                                                                                <i class="fas fa-arrow-left"></i>
                                                                                            </button>
                                                                                        @endif
                                                                                        @if($hasNextStep)
                                                                                            <button type="button" class="btn btn-sm btn-secondary btn-move-candidate" 
                                                                                                    data-candidate-id="{{ $candidate->candidate_id }}" 
                                                                                                    data-current-step="{{ $step }}"
                                                                                                    data-target-step="{{ $process->steps[$currentStepIndex + 1] }}"
                                                                                                    data-direction="right"
                                                                                                    title="Mover para {{ $process->steps[$currentStepIndex + 1] }}">
                                                                                                <i class="fas fa-arrow-right"></i>
                                                                                            </button>
                                                                                        @endif
                                                                                        @if($canApprove)
                                                                                            <button type="button" class="btn btn-sm btn-success btn-approve-candidate" 
                                                                                                    data-candidate-id="{{ $candidate->candidate_id }}" 
                                                                                                    data-candidate-name="{{ $candidate->candidate_name }}"
                                                                                                    data-step="{{ $step }}"
                                                                                                    title="Aprovar Candidato">
                                                                                                <i class="fas fa-check-circle"></i>
                                                                                            </button>
                                                                                        @endif
                                                                                        @if($canReject)
                                                                                            <button type="button" class="btn btn-sm btn-danger btn-reject-candidate" 
                                                                                                    data-candidate-id="{{ $candidate->candidate_id }}" 
                                                                                                    data-candidate-name="{{ $candidate->candidate_name }}"
                                                                                                    data-step="{{ $step }}"
                                                                                                    title="Reprovar Candidato">
                                                                                                <i class="fas fa-times-circle"></i>
                                                                                            </button>
                                                                                        @endif
                                                                                        <button type="button" class="btn btn-sm btn-danger btn-detach-candidate" data-candidate-id="{{ $candidate->candidate_id }}" data-step="{{ $candidate->pivot->step ?? '' }}" title="Desvincular da Etapa">
                                                                                            <i class="fas fa-unlink"></i>
                                                                                        </button>
                                                                                    </div>
                                                                                </td>
                                                                            </tr>
                                                                        @empty
                                                                            <tr>
                                                                                <td colspan="6" class="text-center text-muted">Nenhum candidato vinculado a esta etapa ainda.</td>
                                                                            </tr>
                                                                        @endforelse
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                        @else
                                                        <p class="text-muted">O processo precisa estar em andamento para gerenciar candidatos.</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endif

                                <div class="row mt-4">
                                    <div class="col-12 d-flex justify-content-end gap-2">
                                        <a href="{{ route('selections.index') }}" class="btn btn-secondary">
                                            <i class="fas fa-times me-2"></i>Cancelar
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>Atualizar Processo Seletivo
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Adicionar Observação -->
<div class="modal fade" id="addNoteModal" tabindex="-1" aria-labelledby="addNoteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="addNoteModalLabel">
                    <i class="fas fa-sticky-note me-2"></i>Adicionar Observação
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="noteCandidateName" class="form-label">Candidato:</label>
                    <input type="text" class="form-control" id="noteCandidateName" readonly>
                </div>
                <div class="mb-3">
                    <label for="candidateNote" class="form-label">Observação:</label>
                    <textarea class="form-control" id="candidateNote" rows="5" placeholder="Digite a observação sobre o candidato neste processo seletivo..."></textarea>
                    <small class="form-text text-muted">Máximo de 5000 caracteres.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancelar
                </button>
                <button type="button" class="btn btn-warning" id="btnSaveNote">
                    <i class="fas fa-save me-1"></i>Salvar Observação
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Aprovar Candidato -->
<div class="modal fade" id="approveCandidateModal" tabindex="-1" aria-labelledby="approveCandidateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="approveCandidateModalLabel">
                    <i class="fas fa-check-circle me-2"></i>Aprovar Candidato
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="approveCandidateName" class="form-label">Candidato:</label>
                    <input type="text" class="form-control" id="approveCandidateName" readonly>
                </div>
                <div class="mb-3">
                    <label for="approveStep" class="form-label">Etapa:</label>
                    <input type="text" class="form-control" id="approveStep" readonly>
                </div>
                <div class="mb-3">
                    <label for="approvalObservation" class="form-label">Observação (opcional):</label>
                    <textarea class="form-control" id="approvalObservation" rows="4" placeholder="Digite uma observação sobre a aprovação do candidato..."></textarea>
                    <small class="form-text text-muted">Máximo de 1000 caracteres.</small>
                </div>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Atenção:</strong> Esta ação só pode ser realizada quando o candidato está na última etapa do processo seletivo.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancelar
                </button>
                <button type="button" class="btn btn-success" id="btnConfirmApprove">
                    <i class="fas fa-check-circle me-1"></i>Confirmar Aprovação
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Reprovar Candidato -->
<div class="modal fade" id="rejectCandidateModal" tabindex="-1" aria-labelledby="rejectCandidateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="rejectCandidateModalLabel">
                    <i class="fas fa-times-circle me-2"></i>Reprovar Candidato
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="rejectCandidateName" class="form-label">Candidato:</label>
                    <input type="text" class="form-control" id="rejectCandidateName" readonly>
                </div>
                <div class="mb-3">
                    <label for="rejectStep" class="form-label">Etapa:</label>
                    <input type="text" class="form-control" id="rejectStep" readonly>
                </div>
                <div class="mb-3">
                    <label for="rejectionObservation" class="form-label">Observação (opcional):</label>
                    <textarea class="form-control" id="rejectionObservation" rows="4" placeholder="Digite uma observação sobre a reprovação do candidato..."></textarea>
                    <small class="form-text text-muted">Máximo de 1000 caracteres.</small>
                </div>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Atenção:</strong> Esta ação pode ser realizada em qualquer etapa do processo seletivo.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancelar
                </button>
                <button type="button" class="btn btn-danger" id="btnConfirmReject">
                    <i class="fas fa-times-circle me-1"></i>Confirmar Reprovação
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Busca de Candidatos -->
<div class="modal fade" id="searchCandidateModal" tabindex="-1" aria-labelledby="searchCandidateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="searchCandidateModalLabel">
                    <i class="fas fa-search me-2"></i>Buscar e Vincular Candidato
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="candidateStepSelect" class="form-label">Etapa do Processo <span class="text-danger">*</span></label>
                    <select class="form-select" id="candidateStepSelect" required>
                        <option value="">Selecione uma etapa...</option>
                        @if($process->steps && count($process->steps) > 0)
                            @foreach($process->steps as $step)
                                <option value="{{ $step }}">{{ $step }}</option>
                            @endforeach
                        @endif
                    </select>
                    <small class="form-text text-muted">Selecione a etapa em que o candidato será vinculado.</small>
                </div>
                
                <div class="mb-3">
                    <label for="candidateSearch" class="form-label">Buscar Candidato</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="candidateSearch" placeholder="Digite o nome, e-mail, telefone, CPF ou busque no texto do currículo...">
                        <button class="btn btn-primary" type="button" id="btnSearchCandidateAction">
                            <i class="fas fa-search me-1"></i>Buscar
                        </button>
                    </div>
                </div>
                
                <div id="candidatesSearchResults" class="mt-3">
                    <p class="text-muted text-center">Selecione uma etapa e digite um termo de busca para encontrar candidatos.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Fechar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Gerenciar Interações da Etapa -->
<div class="modal fade" id="manageInteractionsModal" tabindex="-1" aria-labelledby="manageInteractionsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen-lg-down modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="manageInteractionsModalLabel">
                    <i class="fas fa-comments me-2"></i>Gerenciar Interações da Etapa
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Informações do Processo e Etapa -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card border-primary">
                            <div class="card-header bg-primary text-white">
                                <i class="fas fa-user me-2"></i>Dados do Candidato
                            </div>
                            <div class="card-body">
                                <div id="interactionCandidateInfo">
                                    <p class="text-muted mb-0">Selecione um candidato</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-info">
                            <div class="card-header bg-info text-white">
                                <i class="fas fa-briefcase me-2"></i>Dados do Processo e da Vaga
                            </div>
                            <div class="card-body">
                                <p class="mb-1"><strong>Processo:</strong> {{ $process->process_number }}</p>
                                <p class="mb-1"><strong>Vaga:</strong> {{ $process->vacancy->vacancy_title ?? 'N/A' }}</p>
                                <p class="mb-0"><strong>Etapa:</strong> <span id="currentStepName"></span></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Seletor de Candidato -->
                <div class="card mb-4">
                    <div class="card-body">
                        <label for="interactionCandidateSelect" class="form-label"><strong>Selecione o Candidato:</strong></label>
                        <select class="form-select" id="interactionCandidateSelect">
                            <option value="">Selecione um candidato...</option>
                        </select>
                    </div>
                </div>

                <!-- Cadastrar Interação -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Cadastrar:</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <button type="button" class="btn btn-primary w-100 btn-add-question">
                                    <i class="fas fa-question-circle me-2"></i>Pergunta
                                </button>
                            </div>
                            <div class="col-md-6 mb-3">
                                <button type="button" class="btn btn-warning w-100 btn-add-observation">
                                    <i class="fas fa-sticky-note me-2"></i>Observação
                                </button>
                            </div>
                        </div>

                        <!-- Formulário de Pergunta -->
                        <div id="questionForm" style="display: none;" class="mt-3">
                            <div class="card border-primary">
                                <div class="card-body">
                                    <h6 class="card-title"><i class="fas fa-question-circle me-2"></i>Pergunta</h6>
                                    <div class="mb-3">
                                        <label for="interactionQuestion" class="form-label">Pergunta:</label>
                                        <textarea class="form-control" id="interactionQuestion" rows="2" placeholder="Digite a pergunta..."></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="interactionAnswer" class="form-label">Resposta:</label>
                                        <textarea class="form-control" id="interactionAnswer" rows="3" placeholder="Digite a resposta do candidato..."></textarea>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-primary btn-save-interaction" data-type="pergunta">
                                            <i class="fas fa-save me-1"></i>Salvar Pergunta
                                        </button>
                                        <button type="button" class="btn btn-secondary btn-cancel-interaction">
                                            <i class="fas fa-times me-1"></i>Cancelar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Formulário de Observação -->
                        <div id="observationForm" style="display: none;" class="mt-3">
                            <div class="card border-warning">
                                <div class="card-body">
                                    <h6 class="card-title"><i class="fas fa-sticky-note me-2"></i>Observação</h6>
                                    <div class="mb-3">
                                        <label for="interactionObservation" class="form-label">Observação:</label>
                                        <textarea class="form-control" id="interactionObservation" rows="4" placeholder="Digite a observação..."></textarea>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-warning btn-save-interaction" data-type="observacao">
                                            <i class="fas fa-save me-1"></i>Salvar Observação
                                        </button>
                                        <button type="button" class="btn btn-secondary btn-cancel-interaction">
                                            <i class="fas fa-times me-1"></i>Cancelar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Lista de Interações -->
                <div class="card">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="fas fa-list me-2"></i>Interações Registradas</h6>
                    </div>
                    <div class="card-body">
                        <div id="interactionsList">
                            <p class="text-muted text-center">Selecione um candidato para ver as interações.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Fechar
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        let vacancyOpeningDate = null;
        let vacancyClosingDate = null;
        
        // ========== GERENCIAMENTO DE ETAPAS ==========
        let steps = [];
        
        // Carregar etapas existentes ou do old()
        @if(old('steps'))
            try {
                const oldSteps = @json(old('steps'));
                if (Array.isArray(oldSteps)) {
                    oldSteps.forEach(function(step) {
                        if (typeof step === 'string') {
                            addStepBadge(step);
                        }
                    });
                } else if (typeof oldSteps === 'string') {
                    const parsed = JSON.parse(oldSteps);
                    if (Array.isArray(parsed)) {
                        parsed.forEach(function(step) {
                            addStepBadge(step);
                        });
                    }
                }
            } catch(e) {
                console.error('Erro ao carregar etapas:', e);
            }
        @elseif($process->steps && count($process->steps) > 0)
            @foreach($process->steps as $step)
                addStepBadge('{{ addslashes($step) }}');
            @endforeach
        @endif
        
        // Função para adicionar badge de etapa
        function addStepBadge(stepName) {
            if (!stepName || stepName.trim() === '') {
                return;
            }
            
            stepName = stepName.trim();
            
            // Verificar se já existe
            if (steps.includes(stepName)) {
                return;
            }
            
            steps.push(stepName);
            updateStepsInput();
            
            const badge = $(`
                <span class="badge bg-primary d-inline-flex align-items-center gap-1" data-step="${stepName.replace(/"/g, '&quot;')}" style="cursor: move;">
                    <i class="fas fa-grip-vertical me-1" style="opacity: 0.7;"></i>
                    ${stepName.replace(/</g, '&lt;').replace(/>/g, '&gt;')}
                    <button type="button" class="btn-close btn-close-white btn-close-sm" aria-label="Remover" style="cursor: pointer;"></button>
                </span>
            `);
            
            $('#stepsContainer').append(badge);
            
            // Reinicializar SortableJS após adicionar novo badge
            if (typeof Sortable !== 'undefined') {
                initStepsSortable();
            }
            
            // Event listener para remover
            badge.find('.btn-close').on('click', function(e) {
                e.stopPropagation(); // Prevenir que o drag seja ativado
                const stepToRemove = $(this).closest('.badge').data('step');
                const closeButton = $(this);
                
                // Verificar se há candidatos nesta etapa antes de remover
                $.ajax({
                    url: '{{ route("selections.check.step.candidates", $process->selection_process_id) }}',
                    method: 'GET',
                    data: {
                        step: stepToRemove
                    },
                    success: function(response) {
                        if (response.success && response.has_candidates) {
                            alert(`Não é possível excluir a etapa "${stepToRemove}" pois há ${response.candidates_count} candidato(s) vinculado(s) a ela. Por favor, mova ou desvincule os candidatos antes de excluir a etapa.`);
                            return;
                        }
                        
                        // Se não há candidatos, permitir remoção
                        steps = steps.filter(s => s !== stepToRemove);
                        updateStepsInput();
                        closeButton.closest('.badge').remove();
                    },
                    error: function() {
                        alert('Erro ao verificar candidatos na etapa. Tente novamente.');
                    }
                });
            });
        }
        
        // Função para atualizar o input hidden
        function updateStepsInput() {
            $('#stepsInput').val(JSON.stringify(steps));
        }
        
        // Adicionar etapa ao pressionar Enter ou clicar no botão
        function addStep() {
            const stepName = $('#process_steps_input').val().trim();
            if (stepName) {
                addStepBadge(stepName);
                $('#process_steps_input').val('').focus();
            }
        }
        
        $('#process_steps_input').on('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                addStep();
            }
        });
        
        $('#btnAddStep').on('click', function() {
            addStep();
        });

        // Função para atualizar as restrições de data baseadas na vaga selecionada
        function updateDateRestrictions(vacancyId) {
            if (!vacancyId) {
                // Se nenhuma vaga estiver selecionada, remover restrições
                $('#end_date').attr('min', '');
                $('#end_date').attr('max', '');
                $('#start_date').attr('min', '');
                $('#start_date').attr('max', '');
                vacancyOpeningDate = null;
                vacancyClosingDate = null;
                return;
            }

            // Buscar dados da vaga
            $.ajax({
                url: '{{ route("selections.vacancy.dates", ":id") }}'.replace(':id', vacancyId),
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        vacancyOpeningDate = response.opening_date;
                        vacancyClosingDate = response.closing_date;

                        // Atualizar restrições do campo end_date
                        if (vacancyOpeningDate) {
                            $('#end_date').attr('min', vacancyOpeningDate);
                            $('#start_date').attr('min', vacancyOpeningDate);
                        } else {
                            $('#end_date').attr('min', '');
                            $('#start_date').attr('min', '');
                        }

                        if (vacancyClosingDate) {
                            $('#end_date').attr('max', vacancyClosingDate);
                            $('#start_date').attr('max', vacancyClosingDate);
                        } else {
                            $('#end_date').attr('max', '');
                            $('#start_date').attr('max', '');
                        }

                        // Validar data atual se já estiver preenchida
                        validateEndDate();
                    }
                },
                error: function() {
                    console.error('Erro ao buscar dados da vaga');
                }
            });
        }

        // Função para validar a data de encerramento
        function validateEndDate() {
            const startDate = $('#start_date').val();
            const endDate = $('#end_date').val();
            
            // Remover validações anteriores
            $('#end_date').removeClass('is-invalid');
            $('#end_date').next('.invalid-feedback').remove();

            if (!endDate) {
                return;
            }

            let hasError = false;
            let errorMessage = '';

            // Validar se end_date está dentro do intervalo da vaga
            if (vacancyOpeningDate && new Date(endDate) < new Date(vacancyOpeningDate)) {
                hasError = true;
                errorMessage = `A data de encerramento deve ser igual ou posterior à data de abertura da vaga (${formatDate(vacancyOpeningDate)}).`;
            }

            if (vacancyClosingDate && new Date(endDate) > new Date(vacancyClosingDate)) {
                hasError = true;
                errorMessage = `A data de encerramento deve ser igual ou anterior à data de fechamento da vaga (${formatDate(vacancyClosingDate)}).`;
            }

            // Validar se end_date é posterior ou igual a start_date
            if (startDate && new Date(endDate) < new Date(startDate)) {
                hasError = true;
                errorMessage = 'A data de encerramento deve ser igual ou posterior à data de início.';
            }

            if (hasError) {
                $('#end_date').addClass('is-invalid');
                $('#end_date').after('<div class="invalid-feedback">' + errorMessage + '</div>');
            }
        }

        // Função auxiliar para formatar data
        function formatDate(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            return date.toLocaleDateString('pt-BR');
        }

        // ========== FUNCIONALIDADE DE CANDIDATOS ==========
        const processId = {{ $process->selection_process_id }};

        // Buscar candidatos
        function searchCandidates() {
            const step = $('#candidateStepSelect').val();
            if (!step) {
                alert('Por favor, selecione uma etapa antes de buscar candidatos.');
                $('#candidateStepSelect').focus();
                return;
            }
            
            const search = $('#candidateSearch').val().trim();
            const resultsContainer = $('#candidatesSearchResults');
            
            resultsContainer.html('<div class="text-center"><div class="spinner-border" role="status"><span class="visually-hidden">Buscando...</span></div></div>');
            
            $.ajax({
                url: '{{ route("selections.candidates.search") }}',
                method: 'GET',
                data: {
                    search: search,
                    process_id: processId,
                    step: step
                },
                success: function(response) {
                    if (response.success && response.data.length > 0) {
                        let html = '<div class="row g-3">';
                        response.data.forEach(function(candidate) {
                            html += `
                                <div class="col-md-6">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h6 class="card-title">${candidate.name}</h6>
                                            <p class="card-text small mb-2">
                                                <strong>E-mail:</strong> ${candidate.email}<br>
                                                <strong>Telefone:</strong> ${candidate.phone}<br>
                                                <strong>CPF:</strong> ${candidate.document}
                                            </p>
                                            ${candidate.experience !== '-' ? `<p class="card-text small"><strong>Experiência:</strong> ${candidate.experience}</p>` : ''}
                                            ${candidate.education !== '-' ? `<p class="card-text small"><strong>Formação:</strong> ${candidate.education}</p>` : ''}
                                            ${candidate.skills !== '-' ? `<p class="card-text small"><strong>Habilidades:</strong> ${candidate.skills}</p>` : ''}
                                        </div>
                                        <div class="card-footer bg-transparent">
                                            ${candidate.has_pdf ? `<a href="${candidate.resume_pdf_url}" target="_blank" class="btn btn-sm btn-danger me-2"><i class="fas fa-file-pdf me-1"></i>Ver PDF</a>` : ''}
                                            ${candidate.is_linked ? 
                                                (candidate.is_in_current_step ? 
                                                    '<span class="badge bg-success me-2">Já nesta etapa</span>' : 
                                                    `<button type="button" class="btn btn-sm btn-primary btn-attach-candidate" data-candidate-id="${candidate.id}" title="Mover de '${candidate.current_step}' para esta etapa">
                                                        <i class="fas fa-exchange-alt me-1"></i>Mover de "${candidate.current_step}"
                                                    </button>`
                                                ) : 
                                                `<button type="button" class="btn btn-sm btn-primary btn-attach-candidate" data-candidate-id="${candidate.id}">
                                                    <i class="fas fa-link me-1"></i>Vincular
                                                </button>`
                                            }
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                        html += '</div>';
                        resultsContainer.html(html);
                    } else {
                        resultsContainer.html('<p class="text-muted text-center">Nenhum candidato encontrado.</p>');
                    }
                },
                error: function() {
                    resultsContainer.html('<div class="alert alert-danger">Erro ao buscar candidatos. Tente novamente.</div>');
                }
            });
        }

        // Event listener para busca
        $('#btnSearchCandidateAction').on('click', searchCandidates);
        $('#candidateSearch').on('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                searchCandidates();
            }
        });

        // Vincular candidato
        $(document).on('click', '.btn-attach-candidate', function() {
            const candidateId = $(this).data('candidate-id');
            const step = $('#candidateStepSelect').val();
            const button = $(this);
            
            if (!step) {
                alert('Por favor, selecione uma etapa antes de vincular o candidato.');
                $('#candidateStepSelect').focus();
                return;
            }
            
            button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Vinculando...');
            
            $.ajax({
                url: '{{ route("selections.attach.candidate", ":id") }}'.replace(':id', processId),
                method: 'POST',
                data: {
                    candidate_id: candidateId,
                    step: step,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        location.reload();
                    } else {
                        alert(response.message || 'Erro ao vincular candidato.');
                        button.prop('disabled', false).html('<i class="fas fa-link me-1"></i>Vincular');
                    }
                },
                error: function(xhr) {
                    const message = xhr.responseJSON?.message || 'Erro ao vincular candidato.';
                    alert(message);
                    button.prop('disabled', false).html('<i class="fas fa-link me-1"></i>Vincular');
                }
            });
        });
        
        // Quando o botão "Vincular Candidato" dentro de uma aba for clicado, definir a etapa automaticamente
        $(document).on('click', '.btn-link-candidate-to-step', function() {
            const step = $(this).data('step');
            if (step) {
                $('#candidateStepSelect').val(step);
            }
        });
        
        // Limpar campos quando o modal for fechado
        $('#searchCandidateModal').on('hidden.bs.modal', function() {
            // Não limpar a etapa se foi definida por um botão de aba
            // Apenas limpar se o modal foi fechado sem ação
            $('#candidateSearch').val('');
            $('#candidatesSearchResults').html('<p class="text-muted text-center">Selecione uma etapa e digite um termo de busca para encontrar candidatos.</p>');
        });

        // Adicionar observação
        let currentCandidateIdForNote = null;
        
        $(document).on('click', '.btn-add-note', function() {
            const candidateId = $(this).data('candidate-id');
            const candidateName = $(this).data('candidate-name');
            const currentNotes = $(this).data('current-notes') || '';
            
            currentCandidateIdForNote = candidateId;
            $('#noteCandidateName').val(candidateName);
            $('#candidateNote').val(currentNotes);
            
            $('#addNoteModal').modal('show');
        });
        
        $('#btnSaveNote').on('click', function() {
            if (!currentCandidateIdForNote) {
                return;
            }
            
            const notes = $('#candidateNote').val().trim();
            
            if (!notes) {
                alert('Por favor, digite uma observação.');
                return;
            }
            
            if (notes.length > 5000) {
                alert('A observação não pode ter mais de 5000 caracteres.');
                return;
            }
            
            const button = $(this);
            button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Salvando...');
            
            $.ajax({
                url: '{{ route("selections.add.note", ":id") }}'.replace(':id', processId),
                method: 'POST',
                data: {
                    candidate_id: currentCandidateIdForNote,
                    notes: notes,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        $('#addNoteModal').modal('hide');
                        location.reload();
                    } else {
                        alert(response.message || 'Erro ao salvar observação.');
                        button.prop('disabled', false).html('<i class="fas fa-save me-1"></i>Salvar Observação');
                    }
                },
                error: function(xhr) {
                    const message = xhr.responseJSON?.message || 'Erro ao salvar observação.';
                    alert(message);
                    button.prop('disabled', false).html('<i class="fas fa-save me-1"></i>Salvar Observação');
                }
            });
        });

        // Mover candidato entre etapas
        $(document).on('click', '.btn-move-candidate', function() {
            const candidateId = $(this).data('candidate-id');
            const currentStep = $(this).data('current-step');
            const targetStep = $(this).data('target-step');
            const direction = $(this).data('direction');
            const button = $(this);
            const row = button.closest('tr');
            
            if (!targetStep) {
                alert('Erro: etapa de destino não encontrada.');
                return;
            }
            
            const directionText = direction === 'left' ? 'anterior' : 'próxima';
            if (!confirm(`Tem certeza que deseja mover este candidato para a etapa "${targetStep}"?`)) {
                return;
            }
            
            button.prop('disabled', true);
            
            $.ajax({
                url: '{{ route("selections.move.candidate", ":id") }}'.replace(':id', processId),
                method: 'POST',
                data: {
                    candidate_id: candidateId,
                    target_step: targetStep,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        // Remover a linha da tabela atual
                        const table = row.closest('.candidates-table');
                        row.fadeOut(300, function() {
                            $(this).remove();
                            if (table.find('tbody tr').length === 0) {
                                table.find('tbody').html('<tr><td colspan="6" class="text-center text-muted">Nenhum candidato vinculado a esta etapa ainda.</td></tr>');
                            }
                        });
                        
                        // Recarregar a página para atualizar todas as abas
                        setTimeout(function() {
                            location.reload();
                        }, 300);
                    } else {
                        alert(response.message || 'Erro ao mover candidato.');
                        button.prop('disabled', false);
                    }
                },
                error: function(xhr) {
                    const message = xhr.responseJSON?.message || 'Erro ao mover candidato.';
                    alert(message);
                    button.prop('disabled', false);
                }
            });
        });

        // Desvincular candidato
        // ========== APROVAR CANDIDATO ==========
        let currentApproveCandidateId = null;
        let currentApproveStep = null;

        $(document).on('click', '.btn-approve-candidate', function() {
            currentApproveCandidateId = $(this).data('candidate-id');
            const candidateName = $(this).data('candidate-name');
            currentApproveStep = $(this).data('step');
            
            $('#approveCandidateName').val(candidateName);
            $('#approveStep').val(currentApproveStep);
            $('#approvalObservation').val('');
            
            $('#approveCandidateModal').modal('show');
        });

        $('#btnConfirmApprove').on('click', function() {
            if (!currentApproveCandidateId) {
                alert('Erro: ID do candidato não encontrado.');
                return;
            }
            
            const observation = $('#approvalObservation').val().trim();
            
            if (observation.length > 1000) {
                alert('A observação não pode ter mais de 1000 caracteres.');
                return;
            }
            
            const btn = $(this);
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Aprovando...');
            
            $.ajax({
                url: '{{ route("selections.candidate.approve", $process->selection_process_id) }}',
                method: 'POST',
                data: {
                    candidate_id: currentApproveCandidateId,
                    observation: observation,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        $('#approveCandidateModal').modal('hide');
                        alert('Candidato aprovado com sucesso!');
                        location.reload(); // Recarregar para atualizar a tabela
                    } else {
                        alert('Erro: ' + (response.message || 'Não foi possível aprovar o candidato.'));
                        btn.prop('disabled', false).html('<i class="fas fa-check-circle me-1"></i>Confirmar Aprovação');
                    }
                },
                error: function(xhr) {
                    const message = xhr.responseJSON?.message || 'Erro ao aprovar candidato.';
                    alert('Erro: ' + message);
                    btn.prop('disabled', false).html('<i class="fas fa-check-circle me-1"></i>Confirmar Aprovação');
                }
            });
        });

        // ========== REPROVAR CANDIDATO ==========
        let currentRejectCandidateId = null;
        let currentRejectStep = null;

        $(document).on('click', '.btn-reject-candidate', function() {
            currentRejectCandidateId = $(this).data('candidate-id');
            const candidateName = $(this).data('candidate-name');
            currentRejectStep = $(this).data('step');
            
            $('#rejectCandidateName').val(candidateName);
            $('#rejectStep').val(currentRejectStep);
            $('#rejectionObservation').val('');
            
            $('#rejectCandidateModal').modal('show');
        });

        $('#btnConfirmReject').on('click', function() {
            if (!currentRejectCandidateId) {
                alert('Erro: ID do candidato não encontrado.');
                return;
            }
            
            const observation = $('#rejectionObservation').val().trim();
            
            if (observation.length > 1000) {
                alert('A observação não pode ter mais de 1000 caracteres.');
                return;
            }
            
            const btn = $(this);
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Reprovando...');
            
            $.ajax({
                url: '{{ route("selections.candidate.reject", $process->selection_process_id) }}',
                method: 'POST',
                data: {
                    candidate_id: currentRejectCandidateId,
                    observation: observation,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        $('#rejectCandidateModal').modal('hide');
                        alert('Candidato reprovado com sucesso!');
                        location.reload(); // Recarregar para atualizar a tabela
                    } else {
                        alert('Erro: ' + (response.message || 'Não foi possível reprovar o candidato.'));
                        btn.prop('disabled', false).html('<i class="fas fa-times-circle me-1"></i>Confirmar Reprovação');
                    }
                },
                error: function(xhr) {
                    const message = xhr.responseJSON?.message || 'Erro ao reprovar candidato.';
                    alert('Erro: ' + message);
                    btn.prop('disabled', false).html('<i class="fas fa-times-circle me-1"></i>Confirmar Reprovação');
                }
            });
        });

        $(document).on('click', '.btn-detach-candidate', function() {
            const candidateId = $(this).data('candidate-id');
            const step = $(this).data('step');
            
            if (!step) {
                alert('Erro: etapa não encontrada.');
                return;
            }
            
            if (!confirm(`Tem certeza que deseja desvincular este candidato da etapa "${step}"?`)) {
                return;
            }
            
            const button = $(this);
            const row = button.closest('tr');
            
            button.prop('disabled', true);
            
            $.ajax({
                url: '{{ route("selections.detach.candidate", ":id") }}'.replace(':id', processId),
                method: 'POST',
                data: {
                    candidate_id: candidateId,
                    step: step,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        const table = row.closest('.candidates-table');
                        row.fadeOut(300, function() {
                            $(this).remove();
                            if (table.find('tbody tr').length === 0) {
                                table.find('tbody').html('<tr><td colspan="6" class="text-center text-muted">Nenhum candidato vinculado a esta etapa ainda.</td></tr>');
                            }
                        });
                    } else {
                        alert(response.message || 'Erro ao desvincular candidato.');
                        button.prop('disabled', false);
                    }
                },
                error: function(xhr) {
                    const message = xhr.responseJSON?.message || 'Erro ao desvincular candidato.';
                    alert(message);
                    button.prop('disabled', false);
                }
            });
        });

        // Quando a vaga for selecionada ou alterada
        $('#vacancy_id').on('change', function() {
            const vacancyId = $(this).val();
            updateDateRestrictions(vacancyId);
            
            // Validar data atual se necessário
            if (vacancyId) {
                validateEndDate();
            }
        });

        // Validação quando as datas mudarem
        $('#start_date, #end_date').on('change', function() {
            validateEndDate();
        });

        // Inicializar restrições com a vaga atual do processo
        const initialVacancyId = $('#vacancy_id').val();
        if (initialVacancyId) {
            updateDateRestrictions(initialVacancyId);
        }
        
        // ========== DRAG AND DROP PARA REORDENAR ETAPAS ==========
        let stepsSortable = null;
        
        // Carregar SortableJS
        function loadSortableJS() {
            return new Promise(function(resolve, reject) {
                if (typeof Sortable !== 'undefined') {
                    resolve();
                    return;
                }
                
                const script = document.createElement('script');
                script.src = 'https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js';
                script.onload = function() {
                    resolve();
                };
                script.onerror = function() {
                    reject('Erro ao carregar SortableJS');
                };
                document.head.appendChild(script);
            });
        }
        
        function initStepsSortable() {
            const stepsContainer = document.getElementById('stepsContainer');
            if (stepsContainer && typeof Sortable !== 'undefined') {
                // Destruir instância anterior se existir
                if (stepsSortable) {
                    stepsSortable.destroy();
                }
                
                stepsSortable = new Sortable(stepsContainer, {
                    animation: 150,
                    filter: '.btn-close', // Não permitir arrastar quando clicar no botão de fechar
                    preventOnFilter: true, // Prevenir comportamento padrão quando clicar no filtro
                    ghostClass: 'opacity-50',
                    dragClass: 'sortable-drag',
                    onEnd: function(evt) {
                        // Atualizar o array steps baseado na nova ordem
                        steps = [];
                        $('#stepsContainer .badge').each(function() {
                            const stepName = $(this).data('step');
                            if (stepName) {
                                steps.push(stepName);
                            }
                        });
                        updateStepsInput();
                    }
                });
            }
        }
        
        // Carregar e inicializar SortableJS
        loadSortableJS().then(function() {
            initStepsSortable();
        }).catch(function(error) {
            console.error('Erro ao carregar SortableJS:', error);
        });

        // ========== GERENCIAMENTO DE INTERAÇÕES ==========
        let currentStepForInteractions = '';
        let currentProcessIdForInteractions = {{ $process->selection_process_id }};
        let currentCandidateIdForInteractions = null;

        // Abrir modal de interações
        $(document).on('click', '.btn-manage-interactions', function() {
            currentStepForInteractions = $(this).data('step');
            currentProcessIdForInteractions = $(this).data('process-id');
            
            $('#currentStepName').text(currentStepForInteractions);
            $('#interactionCandidateSelect').val('').trigger('change');
            $('#interactionCandidateInfo').html('<p class="text-muted mb-0">Selecione um candidato</p>');
            $('#interactionsList').html('<p class="text-muted text-center">Selecione um candidato para ver as interações.</p>');
            $('#questionForm, #observationForm').hide();
            
            // Carregar candidatos da etapa
            loadCandidatesForInteractions();
            
            $('#manageInteractionsModal').modal('show');
        });

        // Carregar candidatos da etapa no select
        function loadCandidatesForInteractions() {
            $.ajax({
                url: '{{ route("selections.candidates", $process->selection_process_id) }}',
                method: 'GET',
                success: function(response) {
                    if (response.success && response.data) {
                        let candidates = response.data.filter(c => c.step === currentStepForInteractions);
                        let select = $('#interactionCandidateSelect');
                        select.empty().append('<option value="">Selecione um candidato...</option>');
                        
                        candidates.forEach(function(candidate) {
                            if (candidate.candidate_id && candidate.candidate_name) {
                                select.append(`<option value="${candidate.candidate_id}">${candidate.candidate_name}</option>`);
                            }
                        });
                    }
                },
                error: function() {
                    alert('Erro ao carregar candidatos.');
                }
            });
        }

        // Quando um candidato é selecionado
        $('#interactionCandidateSelect').on('change', function() {
            currentCandidateIdForInteractions = $(this).val();
            
            if (currentCandidateIdForInteractions) {
                // Carregar dados do candidato
                $.ajax({
                    url: '{{ route("selections.candidates", $process->selection_process_id) }}',
                    method: 'GET',
                    success: function(response) {
                        if (response.success && response.data) {
                            let candidate = response.data.find(c => c.candidate_id == currentCandidateIdForInteractions);
                            if (candidate && candidate.candidate_name) {
                                let age = 'N/A';
                                if (candidate.candidate_birth_date) {
                                    let birthDate = new Date(candidate.candidate_birth_date);
                                    let today = new Date();
                                    age = Math.floor((today - birthDate) / (365.25 * 24 * 60 * 60 * 1000));
                                }
                                
                                $('#interactionCandidateInfo').html(`
                                    <p class="mb-1"><strong>${candidate.candidate_name}</strong></p>
                                    <p class="mb-0 text-muted">${age !== 'N/A' ? age + ' anos' : 'Idade não informada'}</p>
                                `);
                            } else {
                                $('#interactionCandidateInfo').html('<p class="text-muted mb-0">Dados do candidato não encontrados.</p>');
                            }
                        }
                    }
                });
                
                // Carregar interações
                loadInteractions();
            } else {
                $('#interactionCandidateInfo').html('<p class="text-muted mb-0">Selecione um candidato</p>');
                $('#interactionsList').html('<p class="text-muted text-center">Selecione um candidato para ver as interações.</p>');
            }
        });

        // Carregar interações
        function loadInteractions() {
            if (!currentCandidateIdForInteractions || !currentStepForInteractions) return;
            
            $.ajax({
                url: '{{ route("selections.step.interactions", $process->selection_process_id) }}',
                method: 'GET',
                data: {
                    step: currentStepForInteractions,
                    candidate_id: currentCandidateIdForInteractions
                },
                success: function(response) {
                    if (response.success && response.data) {
                        renderInteractions(response.data);
                    } else {
                        $('#interactionsList').html('<p class="text-muted text-center">Nenhuma interação registrada ainda.</p>');
                    }
                },
                error: function() {
                    $('#interactionsList').html('<p class="text-danger text-center">Erro ao carregar interações.</p>');
                }
            });
        }

        // Renderizar interações
        function renderInteractions(interactions) {
            if (!interactions || interactions.length === 0) {
                $('#interactionsList').html('<p class="text-muted text-center">Nenhuma interação registrada ainda.</p>');
                return;
            }
            
            let html = '<div class="list-group">';
            
            interactions.forEach(function(interaction, index) {
                if (interaction.interaction_type === 'pergunta') {
                    html += `
                        <div class="list-group-item mb-3 border-start border-primary border-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <span class="badge bg-primary me-2">① Pergunta</span>
                                    <small class="text-muted">${interaction.created_at} - ${interaction.created_by}</small>
                                </div>
                                <button class="btn btn-sm btn-danger btn-delete-interaction" data-interaction-id="${interaction.id}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                            <div class="mb-2">
                                <strong>Pergunta:</strong>
                                <p class="mb-0">"${interaction.question || ''}"</p>
                            </div>
                            <div class="mb-2">
                                <strong>Resposta:</strong>
                                ${interaction.answer ? 
                                    `<p class="mb-0">"${interaction.answer}"</p>` : 
                                    `<textarea class="form-control mt-2 answer-input" rows="2" placeholder="Digite a resposta..." data-interaction-id="${interaction.id}"></textarea>
                                     <button class="btn btn-sm btn-primary mt-2 btn-save-answer" data-interaction-id="${interaction.id}">
                                         <i class="fas fa-save me-1"></i>Salvar Resposta
                                     </button>`
                                }
                            </div>
                        </div>
                    `;
                } else if (interaction.interaction_type === 'observacao') {
                    html += `
                        <div class="list-group-item mb-3 border-start border-warning border-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <span class="badge bg-warning me-2">② Observação</span>
                                    <small class="text-muted">${interaction.created_at} - ${interaction.created_by}</small>
                                </div>
                                <button class="btn btn-sm btn-danger btn-delete-interaction" data-interaction-id="${interaction.id}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                            <div>
                                <strong>Observação:</strong>
                                <p class="mb-0">"${interaction.observation || ''}"</p>
                            </div>
                        </div>
                    `;
                }
            });
            
            html += '</div>';
            $('#interactionsList').html(html);
        }

        // Mostrar formulário de pergunta
        $('.btn-add-question').on('click', function() {
            if (!currentCandidateIdForInteractions) {
                alert('Por favor, selecione um candidato primeiro.');
                return;
            }
            $('#questionForm').show();
            $('#observationForm').hide();
            $('#interactionQuestion, #interactionAnswer').val('');
        });

        // Mostrar formulário de observação
        $('.btn-add-observation').on('click', function() {
            if (!currentCandidateIdForInteractions) {
                alert('Por favor, selecione um candidato primeiro.');
                return;
            }
            $('#observationForm').show();
            $('#questionForm').hide();
            $('#interactionObservation').val('');
        });

        // Cancelar formulário
        $('.btn-cancel-interaction').on('click', function() {
            $('#questionForm, #observationForm').hide();
            $('#interactionQuestion, #interactionAnswer, #interactionObservation').val('');
        });

        // Salvar interação
        $(document).on('click', '.btn-save-interaction', function() {
            if (!currentCandidateIdForInteractions) {
                alert('Por favor, selecione um candidato primeiro.');
                return;
            }
            
            let type = $(this).data('type');
            let data = {
                candidate_id: currentCandidateIdForInteractions,
                step: currentStepForInteractions,
                interaction_type: type
            };
            
            if (type === 'pergunta') {
                let question = $('#interactionQuestion').val().trim();
                if (!question) {
                    alert('Por favor, digite a pergunta.');
                    return;
                }
                data.question = question;
                data.answer = $('#interactionAnswer').val().trim();
            } else if (type === 'observacao') {
                let observation = $('#interactionObservation').val().trim();
                if (!observation) {
                    alert('Por favor, digite a observação.');
                    return;
                }
                data.observation = observation;
            }
            
            $.ajax({
                url: '{{ route("selections.step.interactions.store", $process->selection_process_id) }}',
                method: 'POST',
                data: data,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        $('#questionForm, #observationForm').hide();
                        $('#interactionQuestion, #interactionAnswer, #interactionObservation').val('');
                        loadInteractions();
                    } else {
                        alert('Erro: ' + response.message);
                    }
                },
                error: function(xhr) {
                    let message = 'Erro ao salvar interação.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    alert(message);
                }
            });
        });

        // Salvar resposta
        $(document).on('click', '.btn-save-answer', function() {
            let interactionId = $(this).data('interaction-id');
            let answer = $(this).closest('.list-group-item').find('.answer-input').val().trim();
            
            if (!answer) {
                alert('Por favor, digite a resposta.');
                return;
            }
            
            $.ajax({
                url: `{{ route("selections.step.interactions.update", [$process->selection_process_id, ":id"]) }}`.replace(':id', interactionId),
                method: 'PUT',
                data: {
                    answer: answer
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        loadInteractions();
                    } else {
                        alert('Erro: ' + response.message);
                    }
                },
                error: function(xhr) {
                    let message = 'Erro ao salvar resposta.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    alert(message);
                }
            });
        });

        // Deletar interação
        $(document).on('click', '.btn-delete-interaction', function() {
            if (!confirm('Tem certeza que deseja excluir esta interação?')) {
                return;
            }
            
            let interactionId = $(this).data('interaction-id');
            
            $.ajax({
                url: `{{ route("selections.step.interactions.delete", [$process->selection_process_id, ":id"]) }}`.replace(':id', interactionId),
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        loadInteractions();
                    } else {
                        alert('Erro: ' + response.message);
                    }
                },
                error: function(xhr) {
                    let message = 'Erro ao excluir interação.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    alert(message);
                }
            });
        });
    });
</script>
@endpush

