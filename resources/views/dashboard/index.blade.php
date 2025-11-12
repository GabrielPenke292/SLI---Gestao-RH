@extends('template.layout')

@section('content')
<div class="container mt-4">
    <!-- Welcome Card -->
    <div class="card welcome-card mb-4">
        <div class="card-body">
            <h2 class="card-title">
                <i class="fas fa-home me-2"></i>Bem-vindo ao Dashboard
            </h2>
            <p class="card-text">
                Olá, <strong>{{ session('user.name') }}</strong>! Você está logado no sistema SLI de gestão de RH.
            </p>
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
                    @php
                        $userPermissions = session('user.permissions', []);
                        $canViewEmployees = in_array('funcionario', $userPermissions) 
                            || in_array('rh operacional', $userPermissions) 
                            || in_array('gerente rh', $userPermissions) 
                            || in_array('diretoria', $userPermissions) 
                            || in_array('admin', $userPermissions);
                        $canViewReports = in_array('relatorios', $userPermissions) 
                            || in_array('diretoria', $userPermissions) 
                            || in_array('admin', $userPermissions);
                        $canManageUsers = in_array('admin', $userPermissions) 
                            || in_array('diretoria', $userPermissions);
                    @endphp
                    <div class="row">
                        @if($canViewEmployees)
                        <div class="col-md-3 mb-3 p-3">
                            <a href="{{ route('employees.index') }}" class="btn btn-outline-primary w-100 p-5">
                                <i class="fas fa-users me-2"></i>Funcionários
                            </a>
                        </div>
                        @endif

                        @if($canViewReports)
                        <div class="col-md-3 mb-3 p-3">
                            <a href="#" class="btn btn-outline-primary w-100 p-5">
                                <i class="fas fa-chart-bar me-2"></i>Relatórios
                            </a>
                        </div>
                        @endif

                        @if($canManageUsers)
                        <div class="col-md-3 mb-3 p-3">
                            <a href="{{ route('users.index') }}" class="btn btn-outline-primary w-100 p-5">
                                <i class="fas fa-user-cog me-2"></i>Usuários
                            </a>
                        </div>
                        @endif

                        @if($canManageUsers)
                        <div class="col-md-3 mb-3 p-3">
                            <a href="#" class="btn btn-outline-primary w-100 p-5">
                                <i class="fas fa-crown me-2"></i>Administração
                            </a>
                        </div>
                        @endif
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-3 mb-3 p-3">
                            <a href="{{ route('vacancies.index') }}" class="btn btn-outline-primary w-100 p-5">
                                <i class="fas fa-briefcase me-2"></i>Vagas
                            </a>
                        </div>

                        <div class="col-md-3 mb-3 p-3">
                            <a href="{{ route('selections.index') }}" class="btn btn-outline-primary w-100 p-5">
                                <i class="fas fa-file-alt me-2"></i>Processos Seletivos
                            </a>
                        </div>

                        <div class="col-md-3 mb-3 p-3">
                            <a href="{{ route('candidates.index') }}" class="btn btn-outline-primary w-100 p-5">
                                <i class="fas fa-user-plus me-2"></i>Candidatos
                            </a>
                        </div>

                        <div class="col-md-3 mb-3 p-3">
                            <a href="{{ route('negotiations.index') }}" class="btn btn-outline-primary w-100 p-5">
                                <i class="fas fa-handshake me-2"></i>Negociações
                            </a>
                        </div>

                        <div class="col-md-3 mb-3 p-3">
                            <a href="{{ route('exams.index') }}" class="btn btn-outline-primary w-100 p-5">
                                <i class="fas fa-file-alt me-2"></i>Exames/Clínicas	
                            </a>
                        </div>

                        <div class="col-md-3 mb-3 p-3">
                            <a href="#" class="btn btn-outline-primary w-100 p-5">
                                <i class="fas fa-sign-out-alt me-2"></i>Desligamentos
                            </a>
                        </div>

                        <div class="col-md-3 mb-3 p-3">
                            <a href="#" class="btn btn-outline-primary w-100 p-5">
                                <i class="fas fa-exchange-alt me-2"></i>Movimentação
                            </a>
                        </div>

                        <div class="col-md-3 mb-3 p-3">
                            <a href="#" class="btn btn-outline-primary w-100 p-5">
                                <i class="fas fa-university me-2"></i>Treinamentos
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection