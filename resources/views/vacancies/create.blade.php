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
                            <h4 class="mb-0"><i class="fas fa-briefcase me-2"></i>Cadastrar Nova Vaga</h4>
                            <a href="{{ route('vacancies.open') }}" class="btn btn-light btn-sm">
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

                            <form action="{{ route('vacancies.store') }}" method="POST" id="vacancyForm">
                                @csrf

                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="vacancy_title" class="form-label required-field">Título da Vaga</label>
                                        <input type="text" class="form-control @error('vacancy_title') is-invalid @enderror" 
                                               id="vacancy_title" name="vacancy_title" 
                                               value="{{ old('vacancy_title') }}" 
                                               placeholder="Ex: Desenvolvedor Full Stack" 
                                               maxlength="255" required>
                                        @error('vacancy_title')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <label for="vacancy_description" class="form-label required-field">Descrição</label>
                                        <textarea class="form-control @error('vacancy_description') is-invalid @enderror" 
                                                  id="vacancy_description" name="vacancy_description" 
                                                  rows="5" 
                                                  placeholder="Descreva a vaga, responsabilidades e requisitos principais..." 
                                                  required>{{ old('vacancy_description') }}</textarea>
                                        @error('vacancy_description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="urgency_level" class="form-label required-field">Grau de Urgência</label>
                                        <select class="form-select @error('urgency_level') is-invalid @enderror" 
                                                id="urgency_level" name="urgency_level" required>
                                            <option value="">Selecione...</option>
                                            <option value="baixa" {{ old('urgency_level') == 'baixa' ? 'selected' : '' }}>Baixa</option>
                                            <option value="media" {{ old('urgency_level', 'media') == 'media' ? 'selected' : '' }}>Média</option>
                                            <option value="alta" {{ old('urgency_level') == 'alta' ? 'selected' : '' }}>Alta</option>
                                            <option value="critica" {{ old('urgency_level') == 'critica' ? 'selected' : '' }}>Crítica</option>
                                        </select>
                                        @error('urgency_level')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="status" class="form-label required-field">Status</label>
                                        <select class="form-select @error('status') is-invalid @enderror" 
                                                id="status" name="status" required>
                                            <option value="aberta" {{ old('status', 'aberta') == 'aberta' ? 'selected' : '' }}>Aberta</option>
                                            <option value="pausada" {{ old('status') == 'pausada' ? 'selected' : '' }}>Pausada</option>
                                            <option value="encerrada" {{ old('status') == 'encerrada' ? 'selected' : '' }}>Encerrada</option>
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="department_id" class="form-label">Departamento</label>
                                        <select class="form-select @error('department_id') is-invalid @enderror" 
                                                id="department_id" name="department_id">
                                            <option value="">Selecione um departamento (opcional)</option>
                                            @foreach($departments as $department)
                                                <option value="{{ $department->department_id }}" {{ old('department_id') == $department->department_id ? 'selected' : '' }}>
                                                    {{ $department->department_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('department_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="salary" class="form-label">Salário</label>
                                        <div class="input-group">
                                            <span class="input-group-text">R$</span>
                                            <input type="number" 
                                                   class="form-control @error('salary') is-invalid @enderror" 
                                                   id="salary" 
                                                   name="salary" 
                                                   value="{{ old('salary') }}" 
                                                   placeholder="0.00" 
                                                   step="0.01" 
                                                   min="0">
                                        </div>
                                        <small class="form-text text-muted">Campo opcional</small>
                                        @error('salary')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="work_type" class="form-label">Tipo de Trabalho</label>
                                        <input type="text" 
                                               class="form-control @error('work_type') is-invalid @enderror" 
                                               id="work_type" 
                                               name="work_type" 
                                               value="{{ old('work_type') }}" 
                                               placeholder="Ex: Presencial, Remoto, Híbrido" 
                                               maxlength="50">
                                        @error('work_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="work_schedule" class="form-label">Jornada de Trabalho</label>
                                        <input type="text" 
                                               class="form-control @error('work_schedule') is-invalid @enderror" 
                                               id="work_schedule" 
                                               name="work_schedule" 
                                               value="{{ old('work_schedule') }}" 
                                               placeholder="Ex: Integral, Meio Período" 
                                               maxlength="50">
                                        @error('work_schedule')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="opening_date" class="form-label">Data de Abertura</label>
                                        <input type="date" 
                                               class="form-control @error('opening_date') is-invalid @enderror" 
                                               id="opening_date" 
                                               name="opening_date" 
                                               value="{{ old('opening_date') }}">
                                        <small class="form-text text-muted">Deixe em branco para usar a data atual</small>
                                        @error('opening_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="closing_date" class="form-label">Data de Fechamento</label>
                                        <input type="date" 
                                               class="form-control @error('closing_date') is-invalid @enderror" 
                                               id="closing_date" 
                                               name="closing_date" 
                                               value="{{ old('closing_date') }}">
                                        <small class="form-text text-muted">Campo opcional</small>
                                        @error('closing_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <label for="requirements" class="form-label">Requisitos</label>
                                        <textarea class="form-control @error('requirements') is-invalid @enderror" 
                                                  id="requirements" 
                                                  name="requirements" 
                                                  rows="4" 
                                                  placeholder="Liste os requisitos necessários para a vaga...">{{ old('requirements') }}</textarea>
                                        @error('requirements')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <label for="benefits" class="form-label">Benefícios</label>
                                        <textarea class="form-control @error('benefits') is-invalid @enderror" 
                                                  id="benefits" 
                                                  name="benefits" 
                                                  rows="4" 
                                                  placeholder="Liste os benefícios oferecidos...">{{ old('benefits') }}</textarea>
                                        @error('benefits')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-12 d-flex justify-content-end gap-2">
                                        <a href="{{ route('vacancies.open') }}" class="btn btn-secondary">
                                            <i class="fas fa-times me-2"></i>Cancelar
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>Salvar Vaga
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
        // Validação de data de fechamento
        $('#opening_date, #closing_date').on('change', function() {
            const openingDate = $('#opening_date').val();
            const closingDate = $('#closing_date').val();
            
            if (openingDate && closingDate) {
                if (new Date(closingDate) < new Date(openingDate)) {
                    $('#closing_date').addClass('is-invalid');
                    $('#closing_date').next('.invalid-feedback').remove();
                    $('#closing_date').after('<div class="invalid-feedback">A data de fechamento deve ser igual ou posterior à data de abertura.</div>');
                } else {
                    $('#closing_date').removeClass('is-invalid');
                    $('#closing_date').next('.invalid-feedback').remove();
                }
            }
        });
    });
</script>
@endpush

