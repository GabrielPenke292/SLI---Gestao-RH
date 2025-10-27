<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SLI - Dashboard</title>
    <link href="{{ asset('assets/css/bootstrap/bootstrap.min.css') }}" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">    
    <link rel="stylesheet" href="{{ asset('css/dashboard/dashboard.css') }}">
</head>
<body>
    <!-- Navbar -->
    @include('template.navbar') 

    <!-- Main Content -->
    <div class="container mt-4">
        <!-- Welcome Card -->
        <div class="card welcome-card mb-4">
            <div class="card-body">
                <h2 class="card-title">
                    <i class="fas fa-home me-2"></i>Bem-vindo ao Dashboard
                </h2>
                <p class="card-text">
                    Olá, <strong>{{ $user->user_name }}</strong>! Você está logado no sistema SLI de gestão de RH.
                </p>
            </div>
        </div>

        <div class="row">
            <!-- User Info Card -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-user me-2"></i>Informações do Usuário
                        </h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Nome:</strong> {{ $user->user_name }}</p>
                        <p><strong>Email:</strong> {{ $user->user_email }}</p>
                        <p><strong>ID:</strong> {{ $user->users_id }}</p>
                        <p><strong>Membro desde:</strong> {{ $user->created_at->format('d/m/Y') }}</p>
                    </div>
                </div>
            </div>

            <!-- Permissions Card -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-key me-2"></i>Suas Permissões
                        </h5>
                    </div>
                    <div class="card-body">
                        @if(count($permissions) > 0)
                            @foreach($permissions as $permission)
                                <span class="permission-badge">{{ $permission }}</span>
                            @endforeach
                        @else
                            <p class="text-muted">Nenhuma permissão atribuída.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-bolt me-2"></i>Ações Rápidas
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @if(in_array('employee_view', $permissions))
                            <div class="col-md-3 mb-3">
                                <a href="#" class="btn btn-outline-primary w-100">
                                    <i class="fas fa-users me-2"></i>Funcionários
                                </a>
                            </div>
                            @endif
                            
                            @if(in_array('reports_view', $permissions))
                            <div class="col-md-3 mb-3">
                                <a href="#" class="btn btn-outline-success w-100">
                                    <i class="fas fa-chart-bar me-2"></i>Relatórios
                                </a>
                            </div>
                            @endif
                            
                            @if(in_array('user_management', $permissions))
                            <div class="col-md-3 mb-3">
                                <a href="#" class="btn btn-outline-warning w-100">
                                    <i class="fas fa-user-cog me-2"></i>Usuários
                                </a>
                            </div>
                            @endif
                            
                            @if(in_array('admin', $permissions))
                            <div class="col-md-3 mb-3">
                                <a href="#" class="btn btn-outline-danger w-100">
                                    <i class="fas fa-crown me-2"></i>Administração
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/js/jquery/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap/bootstrap.bundle.min.js') }}"></script>
</body>
</html>