@extends('template.layout')

@push('styles')
<style>
    .nav-tabs .nav-link {
        color: #495057;
        border: 1px solid transparent;
        border-top-left-radius: 0.25rem;
        border-top-right-radius: 0.25rem;
    }

    .nav-tabs .nav-link:hover {
        border-color: #e9ecef #e9ecef #dee2e6;
        isolation: isolate;
    }

    .nav-tabs .nav-link.active {
        color: #495057;
        background-color: #fff;
        border-color: #dee2e6 #dee2e6 #fff;
    }

    .form-label {
        font-weight: 500;
        margin-bottom: 0.5rem;
    }

    .required-field::after {
        content: " *";
        color: #dc3545;
    }

    .tab-content {
        padding: 1.5rem 0;
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
                <!-- page title -->
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                            <h4 class="mb-0"><i class="fas fa-user-edit me-2"></i>Editar Funcionário</h4>
                            <a href="{{ route('employees.view', $worker->worker_id) }}" class="btn btn-light btn-sm">
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

                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
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

                            <form action="{{ route('employees.update', $worker->worker_id) }}" method="POST" id="employeeForm">
                                @csrf
                                @method('PUT')

                                <!-- Tabs Navigation -->
                                <ul class="nav nav-tabs mb-3" id="employeeTabs" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="personal-tab" data-bs-toggle="tab" data-bs-target="#personal" type="button" role="tab" aria-controls="personal" aria-selected="true">
                                            <i class="fas fa-user me-2"></i>Dados Pessoais
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="professional-tab" data-bs-toggle="tab" data-bs-target="#professional" type="button" role="tab" aria-controls="professional" aria-selected="false">
                                            <i class="fas fa-briefcase me-2"></i>Dados Profissionais
                                        </button>
                                    </li>
                                </ul>

                                <!-- Tabs Content -->
                                <div class="tab-content" id="employeeTabsContent">
                                    <!-- Tab 1: Dados Pessoais -->
                                    <div class="tab-pane fade show active" id="personal" role="tabpanel" aria-labelledby="personal-tab">
                                        <div class="row">
                                            <div class="col-md-12 mb-3">
                                                <label for="worker_name" class="form-label required-field">Nome Completo</label>
                                                <input type="text" class="form-control @error('worker_name') is-invalid @enderror" 
                                                       id="worker_name" name="worker_name" 
                                                       value="{{ old('worker_name', $worker->worker_name) }}" 
                                                       placeholder="Digite o nome completo do funcionário" 
                                                       maxlength="75" required>
                                                @error('worker_name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="worker_email" class="form-label required-field">E-mail</label>
                                                <input type="email" class="form-control @error('worker_email') is-invalid @enderror" 
                                                       id="worker_email" name="worker_email" 
                                                       value="{{ old('worker_email', $worker->worker_email) }}" 
                                                       placeholder="exemplo@email.com" 
                                                       maxlength="45" required>
                                                @error('worker_email')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="worker_document" class="form-label required-field">CPF</label>
                                                <input type="text" class="form-control @error('worker_document') is-invalid @enderror" 
                                                       id="worker_document" name="worker_document" 
                                                       value="{{ old('worker_document', $worker->worker_document) }}" 
                                                       placeholder="000.000.000-00" 
                                                       maxlength="20" required>
                                                @error('worker_document')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="worker_rg" class="form-label">RG</label>
                                                <input type="text" class="form-control @error('worker_rg') is-invalid @enderror" 
                                                       id="worker_rg" name="worker_rg" 
                                                       value="{{ old('worker_rg', $worker->worker_rg) }}" 
                                                       placeholder="00.000.000-0" 
                                                       maxlength="20">
                                                @error('worker_rg')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="worker_birth_date" class="form-label required-field">Data de Nascimento</label>
                                                <input type="date" class="form-control @error('worker_birth_date') is-invalid @enderror" 
                                                       id="worker_birth_date" name="worker_birth_date" 
                                                       value="{{ old('worker_birth_date', $worker->worker_birth_date ? $worker->worker_birth_date->format('Y-m-d') : '') }}" 
                                                       required>
                                                @error('worker_birth_date')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Tab 2: Dados Profissionais -->
                                    <div class="tab-pane fade" id="professional" role="tabpanel" aria-labelledby="professional-tab">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="department_id" class="form-label required-field">Departamento</label>
                                                <select class="form-select @error('department_id') is-invalid @enderror" 
                                                        id="department_id" name="department_id" required>
                                                    <option value="">Selecione um departamento</option>
                                                    @foreach($departments as $department)
                                                        <option value="{{ $department->department_id }}" 
                                                                {{ old('department_id', $worker->department_id) == $department->department_id ? 'selected' : '' }}>
                                                            {{ $department->department_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('department_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="roles" class="form-label required-field">Cargo(s)</label>
                                                <select class="form-select @error('roles') is-invalid @enderror" 
                                                        id="roles" name="roles[]" multiple size="5" required>
                                                    @php
                                                        $selectedRoles = old('roles', $worker->roles->pluck('role_id')->toArray());
                                                    @endphp
                                                    @foreach($roles as $role)
                                                        <option value="{{ $role->role_id }}" 
                                                                {{ in_array($role->role_id, $selectedRoles) ? 'selected' : '' }}>
                                                            {{ $role->role_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <small class="form-text text-muted">Mantenha Ctrl (ou Cmd no Mac) pressionado para selecionar múltiplos cargos</small>
                                                @error('roles')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="worker_start_date" class="form-label required-field">Data de Admissão</label>
                                                <input type="date" class="form-control @error('worker_start_date') is-invalid @enderror" 
                                                       id="worker_start_date" name="worker_start_date" 
                                                       value="{{ old('worker_start_date', $worker->worker_start_date ? $worker->worker_start_date->format('Y-m-d') : '') }}" 
                                                       required>
                                                @error('worker_start_date')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="worker_salary" class="form-label required-field">Salário</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">R$</span>
                                                    <input type="number" class="form-control @error('worker_salary') is-invalid @enderror" 
                                                           id="worker_salary" name="worker_salary" 
                                                           value="{{ old('worker_salary', $worker->worker_salary) }}" 
                                                           placeholder="0.00" 
                                                           step="0.01" 
                                                           min="0" 
                                                           required>
                                                    @error('worker_salary')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="worker_status" class="form-label required-field">Status</label>
                                                <select class="form-select @error('worker_status') is-invalid @enderror" 
                                                        id="worker_status" name="worker_status" required>
                                                    <option value="">Selecione o status</option>
                                                    <option value="1" {{ old('worker_status', $worker->worker_status) == '1' ? 'selected' : '' }}>Ativo</option>
                                                    <option value="0" {{ old('worker_status', $worker->worker_status) == '0' ? 'selected' : '' }}>Inativo</option>
                                                </select>
                                                @error('worker_status')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Form Actions -->
                                <div class="row mt-4">
                                    <div class="col-12 d-flex justify-content-end gap-2">
                                        <a href="{{ route('employees.view', $worker->worker_id) }}" class="btn btn-secondary">
                                            <i class="fas fa-times me-2"></i>Cancelar
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>Atualizar Funcionário
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
        // Máscara para CPF
        $('#worker_document').on('input', function() {
            let value = $(this).val().replace(/\D/g, '');
            if (value.length <= 11) {
                value = value.replace(/(\d{3})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
                $(this).val(value);
            }
        });

        // Máscara para RG
        $('#worker_rg').on('input', function() {
            let value = $(this).val().replace(/\D/g, '');
            if (value.length <= 9) {
                value = value.replace(/(\d{2})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d{1})$/, '$1-$2');
                $(this).val(value);
            }
        });

        // Validação do formulário antes de enviar
        $('#employeeForm').on('submit', function(e) {
            let isValid = true;
            let firstInvalidTab = null;

            // Validar campos obrigatórios em cada tab
            $('#personal input[required]').each(function() {
                if (!$(this).val()) {
                    isValid = false;
                    if (firstInvalidTab === null) {
                        firstInvalidTab = 'personal';
                    }
                    $(this).addClass('is-invalid');
                } else {
                    $(this).removeClass('is-invalid');
                }
            });

            $('#professional select[required], #professional input[required]').each(function() {
                if (!$(this).val() || ($(this).is('select[multiple]') && $(this).val().length === 0)) {
                    isValid = false;
                    if (firstInvalidTab === null) {
                        firstInvalidTab = 'professional';
                    }
                    $(this).addClass('is-invalid');
                } else {
                    $(this).removeClass('is-invalid');
                }
            });

            if (!isValid) {
                e.preventDefault();
                // Mudar para a tab com erro
                if (firstInvalidTab === 'professional') {
                    $('#professional-tab').tab('show');
                } else {
                    $('#personal-tab').tab('show');
                }
                
                // Mostrar mensagem de erro
                if (!$('.alert-danger').length) {
                    $('<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                      '<i class="fas fa-exclamation-circle me-2"></i>Por favor, preencha todos os campos obrigatórios.' +
                      '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                      '</div>').prependTo('.card-body');
                }
                
                // Scroll para o topo do formulário
                $('html, body').animate({
                    scrollTop: $('.card-body').offset().top - 100
                }, 500);
            }
        });

        // Limpar classes de erro ao preencher campos
        $('input, select').on('input change', function() {
            if ($(this).val()) {
                $(this).removeClass('is-invalid');
            }
        });
    });
</script>
@endpush
