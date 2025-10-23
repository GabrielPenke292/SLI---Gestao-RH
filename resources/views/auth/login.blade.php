<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SLI - Gestão de RH | Login</title>
    <link href="{{ asset('assets/css/bootstrap/bootstrap.min.css') }}" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/login/login.css') }}">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h1><i class="fas fa-users-cog me-2"></i>SLI</h1>
                <p>Sistema de Gestão de Recursos Humanos</p>
            </div>
            
            <div class="login-body">
                @if ($errors->any())
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        {{ $errors->first() }}
                    </div>
                @endif
                
                @if (session('success'))
                    <div class="alert alert-success" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        {{ session('success') }}
                    </div>
                @endif
                
                <form method="POST" action="{{ route('login') }}" id="loginForm">
                    @csrf
                    
                    <div class="form-floating position-relative">
                        <input type="email" 
                               class="form-control @error('email') is-invalid @enderror" 
                               id="email" 
                               name="email" 
                               placeholder="seu@email.com"
                               value="{{ old('email') }}"
                               required 
                               autocomplete="email" 
                               autofocus>
                        <label for="email">E-mail</label>
                        <div class="input-group-text">
                            <i class="fas fa-envelope"></i>
                        </div>
                        @error('email')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    
                    <div class="form-floating position-relative">
                        <input type="password" 
                               class="form-control @error('password') is-invalid @enderror" 
                               id="password" 
                               name="password" 
                               placeholder="Sua senha"
                               required 
                               autocomplete="current-password">
                        <label for="password">Senha</label>
                        <div class="input-group-text">
                            <i class="fas fa-lock"></i>
                        </div>
                        @error('password')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    
                    <div class="form-check">
                        <input class="form-check-input" 
                               type="checkbox" 
                               name="remember" 
                               id="remember" 
                               {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label" for="remember">
                            Lembrar-me
                        </label>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-login" id="loginBtn">
                        <span class="btn-text">
                            <i class="fas fa-sign-in-alt me-2"></i>Entrar
                        </span>
                        <span class="loading">
                            <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                            Entrando...
                        </span>
                    </button>
                </form>
                
                <div class="forgot-password">
                    <a href="#">
                        <i class="fas fa-key me-1"></i>Esqueceu sua senha?
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <script src="{{ asset('assets/js/bootstrap/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/login/login.js') }}"></script>
</body>
</html>
