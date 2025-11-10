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

                                <!-- Seção de Candidatos (preparada para implementação futura) -->
                                <div class="candidates-section">
                                    <h5 class="mb-3"><i class="fas fa-users me-2"></i>Candidatos</h5>
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        A gestão de candidatos será implementada em breve.
                                    </div>
                                </div>

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

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        let vacancyOpeningDate = null;
        let vacancyClosingDate = null;

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

