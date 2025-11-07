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

    .password-strength {
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }

    .password-strength.weak {
        color: #dc3545;
    }

    .password-strength.medium {
        color: #ffc107;
    }

    .password-strength.strong {
        color: #198754;
    }

    .permissions-section {
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        padding: 1rem;
        background-color: #f8f9fa;
    }

    .permission-group {
        margin-bottom: 0.5rem;
    }

    .permission-checkbox {
        margin-right: 0.5rem;
    }

    .permission-label {
        font-weight: normal;
        cursor: pointer;
        margin-bottom: 0;
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
                            <h4 class="mb-0"><i class="fas fa-user-edit me-2"></i>Editar Usuário</h4>
                            <a href="{{ route('users.index') }}" class="btn btn-light btn-sm">
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

                            <form action="{{ route('users.update', $user->users_id) }}" method="POST" id="userForm">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="user_name" class="form-label required-field">Nome Completo</label>
                                        <input type="text" class="form-control @error('user_name') is-invalid @enderror" 
                                               id="user_name" name="user_name" 
                                               value="{{ old('user_name', $user->user_name) }}" 
                                               placeholder="Digite o nome completo do usuário" 
                                               maxlength="75" required>
                                        @error('user_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <label for="user_email" class="form-label required-field">E-mail</label>
                                        <input type="email" class="form-control @error('user_email') is-invalid @enderror" 
                                               id="user_email" name="user_email" 
                                               value="{{ old('user_email', $user->user_email) }}" 
                                               placeholder="exemplo@email.com" 
                                               maxlength="45" required>
                                        @error('user_email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="user_password" class="form-label">Senha</label>
                                        <input type="password" class="form-control @error('user_password') is-invalid @enderror" 
                                               id="user_password" name="user_password" 
                                               placeholder="Deixe em branco para manter a senha atual" 
                                               minlength="6">
                                        <small class="form-text text-muted">Deixe em branco se não desejar alterar a senha.</small>
                                        <div class="password-strength" id="passwordStrength"></div>
                                        @error('user_password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="user_password_confirmation" class="form-label">Confirmar Senha</label>
                                        <input type="password" class="form-control @error('user_password') is-invalid @enderror" 
                                               id="user_password_confirmation" name="user_password_confirmation" 
                                               placeholder="Digite a senha novamente" 
                                               minlength="6">
                                        <div class="invalid-feedback" id="passwordMatch"></div>
                                    </div>
                                </div>

                                <!-- Seção de Permissões -->
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <div class="permissions-section">
                                            <label class="form-label fw-bold mb-3">
                                                <i class="fas fa-shield-alt me-2"></i>Permissões do Usuário
                                            </label>
                                            <p class="text-muted small mb-3">
                                                Selecione as permissões que este usuário terá no sistema. Nenhuma permissão é obrigatória.
                                            </p>
                                            
                                            @if($permissions && $permissions->count() > 0)
                                                <div class="row">
                                                    @php
                                                        $userPermissionIds = $user->permissions->pluck('permissio_id')->toArray();
                                                        $oldPermissions = old('permissions', $userPermissionIds);
                                                    @endphp
                                                    @foreach($permissions as $permission)
                                                        <div class="col-md-6 col-lg-4 mb-2 permission-group">
                                                            <div class="form-check">
                                                                <input class="form-check-input permission-checkbox" 
                                                                       type="checkbox" 
                                                                       name="permissions[]" 
                                                                       value="{{ $permission->permissio_id }}" 
                                                                       id="permission_{{ $permission->permissio_id }}"
                                                                       {{ in_array($permission->permissio_id, $oldPermissions) ? 'checked' : '' }}>
                                                                <label class="form-check-label permission-label" 
                                                                       for="permission_{{ $permission->permissio_id }}">
                                                                    {{ ucfirst(str_replace('_', ' ', $permission->permission_name)) }}
                                                                </label>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                                
                                                <div class="mt-3">
                                                    <button type="button" class="btn btn-sm btn-outline-primary" id="selectAllPermissions">
                                                        <i class="fas fa-check-square me-1"></i>Selecionar Todas
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline-secondary" id="deselectAllPermissions">
                                                        <i class="fas fa-square me-1"></i>Desselecionar Todas
                                                    </button>
                                                </div>
                                            @else
                                                <div class="alert alert-info mb-0">
                                                    <i class="fas fa-info-circle me-2"></i>Nenhuma permissão cadastrada no sistema.
                                                </div>
                                            @endif
                                            
                                            @error('permissions')
                                                <div class="text-danger small mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Form Actions -->
                                <div class="row mt-4">
                                    <div class="col-12 d-flex justify-content-end gap-2">
                                        <a href="{{ route('users.index') }}" class="btn btn-secondary">
                                            <i class="fas fa-times me-2"></i>Cancelar
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>Atualizar Usuário
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
        // Validação de força da senha (apenas se preenchida)
        $('#user_password').on('input', function() {
            const password = $(this).val();
            const strengthDiv = $('#passwordStrength');
            
            if (password.length === 0) {
                strengthDiv.text('').removeClass('weak medium strong');
                return;
            }

            let strength = 0;
            if (password.length >= 6) strength++;
            if (password.length >= 8) strength++;
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
            if (/\d/.test(password)) strength++;
            if (/[^a-zA-Z\d]/.test(password)) strength++;

            if (strength <= 2) {
                strengthDiv.text('Senha fraca').removeClass('medium strong').addClass('weak');
            } else if (strength <= 3) {
                strengthDiv.text('Senha média').removeClass('weak strong').addClass('medium');
            } else {
                strengthDiv.text('Senha forte').removeClass('weak medium').addClass('strong');
            }
        });

        // Validação de confirmação de senha (apenas se senha for preenchida)
        $('#user_password_confirmation').on('input', function() {
            const password = $('#user_password').val();
            const confirmation = $(this).val();
            const matchDiv = $('#passwordMatch');

            // Se a senha estiver vazia, não validar
            if (password.length === 0) {
                if (confirmation.length === 0) {
                    matchDiv.text('').hide();
                    $(this).removeClass('is-invalid is-valid');
                } else {
                    matchDiv.text('Preencha a senha primeiro').show();
                    $(this).removeClass('is-valid').addClass('is-invalid');
                }
                return;
            }

            if (confirmation.length === 0) {
                matchDiv.text('').hide();
                $(this).removeClass('is-invalid is-valid');
                return;
            }

            if (password === confirmation) {
                matchDiv.text('').hide();
                $(this).removeClass('is-invalid').addClass('is-valid');
            } else {
                matchDiv.text('As senhas não coincidem').show();
                $(this).removeClass('is-valid').addClass('is-invalid');
            }
        });

        // Validação do formulário antes de enviar
        $('#userForm').on('submit', function(e) {
            const password = $('#user_password').val();
            const confirmation = $('#user_password_confirmation').val();

            // Se a senha foi preenchida, validar
            if (password.length > 0) {
                if (password !== confirmation) {
                    e.preventDefault();
                    $('#user_password_confirmation').addClass('is-invalid');
                    $('#passwordMatch').text('As senhas não coincidem').show();
                    return false;
                }

                if (password.length < 6) {
                    e.preventDefault();
                    $('#user_password').addClass('is-invalid');
                    alert('A senha deve ter no mínimo 6 caracteres.');
                    return false;
                }
            } else {
                // Se a senha não foi preenchida, limpar o campo de confirmação
                $('#user_password_confirmation').val('');
            }
        });

        // Selecionar todas as permissões
        $('#selectAllPermissions').on('click', function() {
            $('.permission-checkbox').prop('checked', true);
        });

        // Desselecionar todas as permissões
        $('#deselectAllPermissions').on('click', function() {
            $('.permission-checkbox').prop('checked', false);
        });
    });
</script>
@endpush

