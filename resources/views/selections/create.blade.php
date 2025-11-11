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
                            <h4 class="mb-0"><i class="fas fa-clipboard-list me-2"></i>Criar Novo Processo Seletivo</h4>
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

                            <form action="{{ route('selections.store') }}" method="POST" id="selectionForm">
                                @csrf

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="process_number" class="form-label required-field">Número do Processo</label>
                                        <input type="text" class="form-control @error('process_number') is-invalid @enderror" 
                                               id="process_number" name="process_number" 
                                               value="{{ old('process_number') }}" 
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
                                                <option value="{{ $vacancy->vacancy_id }}" {{ old('vacancy_id') == $vacancy->vacancy_id ? 'selected' : '' }}>
                                                    {{ $vacancy->vacancy_title }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="form-text text-muted">Apenas vagas abertas podem ser selecionadas</small>
                                        @error('vacancy_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="reason" class="form-label required-field">Motivo do Processo</label>
                                        <select class="form-select @error('reason') is-invalid @enderror" 
                                                id="reason" name="reason" required>
                                            <option value="">Selecione...</option>
                                            <option value="substituicao" {{ old('reason') == 'substituicao' ? 'selected' : '' }}>Substituição</option>
                                            <option value="aumento_quadro" {{ old('reason') == 'aumento_quadro' ? 'selected' : '' }}>Aumento de Quadro</option>
                                        </select>
                                        @error('reason')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="approver_id" class="form-label required-field">Aprovador</label>
                                        <select class="form-select @error('approver_id') is-invalid @enderror" 
                                                id="approver_id" name="approver_id" required>
                                            <option value="">Selecione um aprovador...</option>
                                            @foreach($approvers as $approver)
                                                <option value="{{ $approver->users_id }}" {{ old('approver_id') == $approver->users_id ? 'selected' : '' }}>
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
                                                   value="{{ old('budget') }}" 
                                                   placeholder="0.00" 
                                                   step="0.01" 
                                                   min="0">
                                        </div>
                                        <small class="form-text text-muted">Campo opcional</small>
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
                                               value="{{ old('start_date') }}">
                                        <small class="form-text text-muted">Campo opcional</small>
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
                                               value="{{ old('end_date') }}">
                                        <small class="form-text text-muted">Campo opcional</small>
                                        @error('end_date')
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
                                        <input type="hidden" name="steps" id="stepsInput" value="{{ old('steps', '[]') }}">
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
                                                  placeholder="Adicione observações sobre o processo seletivo...">{{ old('observations') }}</textarea>
                                        @error('observations')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-12 d-flex justify-content-end gap-2">
                                        <a href="{{ route('selections.index') }}" class="btn btn-secondary">
                                            <i class="fas fa-times me-2"></i>Cancelar
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>Salvar Processo Seletivo
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

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        let vacancyOpeningDate = null;
        let vacancyClosingDate = null;
        
        // ========== GERENCIAMENTO DE ETAPAS ==========
        let steps = [];
        
        // Carregar etapas do old() se houver
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
                <span class="badge bg-primary d-inline-flex align-items-center gap-1" data-step="${stepName}">
                    ${stepName}
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

        // Quando a vaga for selecionada ou alterada
        $('#vacancy_id').on('change', function() {
            const vacancyId = $(this).val();
            updateDateRestrictions(vacancyId);
            
            // Limpar datas se necessário
            if (vacancyId) {
                validateEndDate();
            } else {
                $('#end_date').val('');
                $('#start_date').val('');
            }
        });

        // Validação quando as datas mudarem
        $('#start_date, #end_date').on('change', function() {
            validateEndDate();
        });

        // Inicializar restrições se já houver uma vaga selecionada (caso de erro de validação)
        const initialVacancyId = $('#vacancy_id').val();
        if (initialVacancyId) {
            updateDateRestrictions(initialVacancyId);
        }
    });
</script>
@endpush

