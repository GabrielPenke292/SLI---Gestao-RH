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
                                        <label for="approver_id" class="form-label">Aprovador</label>
                                        <select class="form-select @error('approver_id') is-invalid @enderror" 
                                                id="approver_id" name="approver_id">
                                            <option value="">Selecione um aprovador (opcional)</option>
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
        // Validação de data de encerramento
        $('#start_date, #end_date').on('change', function() {
            const startDate = $('#start_date').val();
            const endDate = $('#end_date').val();
            
            if (startDate && endDate) {
                if (new Date(endDate) < new Date(startDate)) {
                    $('#end_date').addClass('is-invalid');
                    $('#end_date').next('.invalid-feedback').remove();
                    $('#end_date').after('<div class="invalid-feedback">A data de encerramento deve ser igual ou posterior à data de início.</div>');
                } else {
                    $('#end_date').removeClass('is-invalid');
                    $('#end_date').next('.invalid-feedback').remove();
                }
            }
        });
    });
</script>
@endpush

