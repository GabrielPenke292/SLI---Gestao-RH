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
                            <h4 class="mb-0"><i class="fas fa-user-plus me-2"></i>Cadastrar Novo Usuário</h4>
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

                            <form action="{{ route('users.store') }}" method="POST" id="userForm">
                                @csrf

                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="user_name" class="form-label required-field">Nome Completo</label>
                                        <input type="text" class="form-control @error('user_name') is-invalid @enderror" 
                                               id="user_name" name="user_name" 
                                               value="{{ old('user_name') }}" 
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
                                               value="{{ old('user_email') }}" 
                                               placeholder="exemplo@email.com" 
                                               maxlength="45" required>
                                        @error('user_email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="user_password" class="form-label required-field">Senha</label>
                                        <input type="password" class="form-control @error('user_password') is-invalid @enderror" 
                                               id="user_password" name="user_password" 
                                               placeholder="Mínimo 6 caracteres" 
                                               minlength="6" 
                                               required>
                                        <div class="password-strength" id="passwordStrength"></div>
                                        @error('user_password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="user_password_confirmation" class="form-label required-field">Confirmar Senha</label>
                                        <input type="password" class="form-control @error('user_password') is-invalid @enderror" 
                                               id="user_password_confirmation" name="user_password_confirmation" 
                                               placeholder="Digite a senha novamente" 
                                               minlength="6" 
                                               required>
                                        <div class="invalid-feedback" id="passwordMatch"></div>
                                    </div>
                                </div>

                                <!-- Form Actions -->
                                <div class="row mt-4">
                                    <div class="col-12 d-flex justify-content-end gap-2">
                                        <a href="{{ route('users.index') }}" class="btn btn-secondary">
                                            <i class="fas fa-times me-2"></i>Cancelar
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>Salvar Usuário
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
        // Validação de força da senha
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

        // Validação de confirmação de senha
        $('#user_password_confirmation').on('input', function() {
            const password = $('#user_password').val();
            const confirmation = $(this).val();
            const matchDiv = $('#passwordMatch');

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
        });
    });
</script>
@endpush

