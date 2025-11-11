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
                                @if($process->steps && count($process->steps) > 0)
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
                                                 aria-labelledby="step-{{ $index }}-tab">
                                                <div class="card border-top-0 rounded-top-0">
                                                    <div class="card-body">
                                                        <h6 class="card-title">{{ $step }}</h6>
                                                        <p class="text-muted">Conteúdo da etapa será implementado em breve.</p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endif

                                <!-- Seção de Candidatos -->
                                @if($process->status === 'em_andamento')
                                <div class="candidates-section">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5 class="mb-0"><i class="fas fa-users me-2"></i>Candidatos Vinculados</h5>
                                        <button type="button" class="btn btn-primary btn-sm" id="btnSearchCandidate" data-bs-toggle="modal" data-bs-target="#searchCandidateModal">
                                            <i class="fas fa-plus me-2"></i>Vincular Candidato
                                        </button>
                                    </div>
                                    
                                    <div id="candidatesTableContainer">
                                        <table class="table table-striped table-hover" id="candidatesTable">
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
                                                @forelse($process->candidates as $candidate)
                                                    <tr data-candidate-id="{{ $candidate->candidate_id }}">
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
                                                            <a href="{{ route('candidates.show', $candidate->candidate_id) }}" target="_blank" class="btn btn-sm btn-info" title="Ver Perfil">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            <button type="button" class="btn btn-sm btn-warning btn-add-note" data-candidate-id="{{ $candidate->candidate_id }}" data-candidate-name="{{ $candidate->candidate_name }}" data-current-notes="{{ $candidate->pivot->notes ?? '' }}" title="Adicionar Observação">
                                                                <i class="fas fa-sticky-note"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-danger btn-detach-candidate" data-candidate-id="{{ $candidate->candidate_id }}" title="Desvincular">
                                                                <i class="fas fa-unlink"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="6" class="text-center text-muted">Nenhum candidato vinculado ainda.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
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
                    <label for="candidateSearch" class="form-label">Buscar Candidato</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="candidateSearch" placeholder="Digite o nome, e-mail, telefone, CPF ou busque no texto do currículo...">
                        <button class="btn btn-primary" type="button" id="btnSearchCandidateAction">
                            <i class="fas fa-search me-1"></i>Buscar
                        </button>
                    </div>
                </div>
                
                <div id="candidatesSearchResults" class="mt-3">
                    <p class="text-muted text-center">Digite um termo de busca para encontrar candidatos.</p>
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
                <span class="badge bg-primary d-inline-flex align-items-center gap-1" data-step="${stepName.replace(/"/g, '&quot;')}">
                    ${stepName.replace(/</g, '&lt;').replace(/>/g, '&gt;')}
                    <button type="button" class="btn-close btn-close-white btn-close-sm" aria-label="Remover"></button>
                </span>
            `);
            
            $('#stepsContainer').append(badge);
            
            // Event listener para remover
            badge.find('.btn-close').on('click', function() {
                const stepToRemove = $(this).closest('.badge').data('step');
                steps = steps.filter(s => s !== stepToRemove);
                updateStepsInput();
                $(this).closest('.badge').remove();
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
            const search = $('#candidateSearch').val().trim();
            const resultsContainer = $('#candidatesSearchResults');
            
            resultsContainer.html('<div class="text-center"><div class="spinner-border" role="status"><span class="visually-hidden">Buscando...</span></div></div>');
            
            $.ajax({
                url: '{{ route("selections.candidates.search") }}',
                method: 'GET',
                data: {
                    search: search,
                    process_id: processId
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
                                            <button type="button" class="btn btn-sm btn-primary btn-attach-candidate" data-candidate-id="${candidate.id}">
                                                <i class="fas fa-link me-1"></i>Vincular
                                            </button>
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
            const button = $(this);
            
            button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Vinculando...');
            
            $.ajax({
                url: '{{ route("selections.attach.candidate", ":id") }}'.replace(':id', processId),
                method: 'POST',
                data: {
                    candidate_id: candidateId,
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

        // Desvincular candidato
        $(document).on('click', '.btn-detach-candidate', function() {
            if (!confirm('Tem certeza que deseja desvincular este candidato do processo?')) {
                return;
            }
            
            const candidateId = $(this).data('candidate-id');
            const button = $(this);
            const row = button.closest('tr');
            
            button.prop('disabled', true);
            
            $.ajax({
                url: '{{ route("selections.detach.candidate", ":id") }}'.replace(':id', processId),
                method: 'POST',
                data: {
                    candidate_id: candidateId,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        row.fadeOut(300, function() {
                            $(this).remove();
                            if ($('#candidatesTable tbody tr').length === 0) {
                                $('#candidatesTable tbody').html('<tr><td colspan="6" class="text-center text-muted">Nenhum candidato vinculado ainda.</td></tr>');
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
    });
</script>
@endpush

