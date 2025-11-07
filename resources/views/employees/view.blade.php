@extends('template.layout')

@push('styles')
<style>
    .info-card {
        border-left: 4px solid #0d6efd;
        transition: transform 0.2s;
    }

    .info-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    .info-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 0.25rem;
    }

    .info-value {
        color: #212529;
        font-size: 1rem;
    }

    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        font-weight: 500;
        display: inline-block;
    }

    .status-active {
        background-color: #d1e7dd;
        color: #0f5132;
    }

    .status-inactive {
        background-color: #f8d7da;
        color: #842029;
    }

    .section-title {
        color: #0d6efd;
        font-weight: 600;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #e9ecef;
    }

    .badge-role {
        background-color: #e7f1ff;
        color: #0d6efd;
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        margin: 0.25rem;
        display: inline-block;
    }

    .timeline-item {
        padding-left: 2rem;
        position: relative;
        margin-bottom: 1rem;
    }

    .timeline-item::before {
        content: '';
        position: absolute;
        left: 0.5rem;
        top: 0.5rem;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background-color: #0d6efd;
        border: 2px solid #fff;
        box-shadow: 0 0 0 2px #0d6efd;
    }

    .timeline-item::after {
        content: '';
        position: absolute;
        left: 0.75rem;
        top: 1.5rem;
        width: 2px;
        height: calc(100% + 0.5rem);
        background-color: #dee2e6;
    }

    .timeline-item:last-child::after {
        display: none;
    }
</style>
@endpush

@section('content')

<div class="container mt-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0">
                            <i class="fas fa-user me-2"></i>{{ $worker->worker_name }}
                        </h4>
                        <small class="text-white-50">
                            ID: {{ $worker->worker_id }} | 
                            @if($worker->worker_status == 1)
                                <span class="badge bg-light text-dark">Ativo</span>
                            @else
                                <span class="badge bg-secondary">Inativo</span>
                            @endif
                        </small>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('employees.board') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Voltar
                        </a>
                        <button type="button" class="btn btn-warning btn-sm" data-worker-id="{{ $worker->worker_id }}" onclick="editEmployee(this)">
                            <i class="fas fa-edit me-1"></i>Editar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Dados Pessoais -->
        <div class="col-md-6 mb-4">
            <div class="card info-card shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="section-title mb-0">
                        <i class="fas fa-id-card me-2"></i>Dados Pessoais
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="info-label">Nome Completo</div>
                            <div class="info-value">{{ $worker->worker_name }}</div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="info-label">E-mail</div>
                            <div class="info-value">
                                <a href="mailto:{{ $worker->worker_email }}">{{ $worker->worker_email }}</a>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-label">CPF</div>
                            <div class="info-value">{{ $worker->worker_document ?? '-' }}</div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="info-label">RG</div>
                            <div class="info-value">{{ $worker->worker_rg ?? '-' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-label">Data de Nascimento</div>
                            <div class="info-value">
                                @if($worker->worker_birth_date)
                                    @php
                                        $birthDate = new DateTime($worker->worker_birth_date);
                                        $today = new DateTime();
                                        $age = $today->diff($birthDate)->y;
                                    @endphp
                                    {{ date('d/m/Y', strtotime($worker->worker_birth_date)) }}
                                    <small class="text-muted">({{ $age }} anos)</small>
                                @else
                                    -
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dados Profissionais -->
        <div class="col-md-6 mb-4">
            <div class="card info-card shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="section-title mb-0">
                        <i class="fas fa-briefcase me-2"></i>Dados Profissionais
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="info-label">Departamento</div>
                            <div class="info-value">
                                @if($worker->department)
                                    <span class="badge bg-primary">{{ $worker->department->department_name }}</span>
                                @else
                                    <span class="text-muted">Não definido</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-label">Status</div>
                            <div class="info-value">
                                @if($worker->worker_status == 1)
                                    <span class="status-badge status-active">
                                        <i class="fas fa-check-circle me-1"></i>Ativo
                                    </span>
                                @else
                                    <span class="status-badge status-inactive">
                                        <i class="fas fa-times-circle me-1"></i>Inativo
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="info-label">Data de Admissão</div>
                            <div class="info-value">
                                @if($worker->worker_start_date)
                                    {{ date('d/m/Y', strtotime($worker->worker_start_date)) }}
                                    @php
                                        $startDate = new DateTime($worker->worker_start_date);
                                        $today = new DateTime();
                                        $years = $today->diff($startDate)->y;
                                        $months = $today->diff($startDate)->m;
                                    @endphp
                                    <small class="text-muted">
                                        ({{ $years }} ano(s) e {{ $months }} mês(es))
                                    </small>
                                @else
                                    -
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-label">Salário</div>
                            <div class="info-value">
                                @if($worker->worker_salary)
                                    <strong class="text-success">R$ {{ number_format($worker->worker_salary, 2, ',', '.') }}</strong>
                                @else
                                    <span class="text-muted">Não informado</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="info-label">Cargo(s)</div>
                            <div class="info-value">
                                @if($worker->roles && $worker->roles->count() > 0)
                                    @foreach($worker->roles as $role)
                                        <span class="badge-role">
                                            <i class="fas fa-user-tie me-1"></i>{{ $role->role_name }}
                                        </span>
                                    @endforeach
                                @else
                                    <span class="text-muted">Nenhum cargo atribuído</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Informações de Auditoria -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card info-card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="section-title mb-0">
                        <i class="fas fa-history me-2"></i>Informações de Auditoria
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <div class="info-label">Criado em</div>
                            <div class="info-value">
                                @if($worker->created_at)
                                    {{ date('d/m/Y H:i:s', strtotime($worker->created_at)) }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="info-label">Criado por</div>
                            <div class="info-value">
                                @if($worker->created_by)
                                    {{ $worker->created_by }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="info-label">Atualizado em</div>
                            <div class="info-value">
                                @if($worker->updated_at)
                                    {{ date('d/m/Y H:i:s', strtotime($worker->updated_at)) }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="info-label">Atualizado por</div>
                            <div class="info-value">
                                @if($worker->updated_by)
                                    {{ $worker->updated_by }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Resumo -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-line me-2"></i>Resumo
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 text-center mb-3">
                            <div class="p-3 bg-light rounded">
                                <i class="fas fa-calendar-alt fa-2x text-primary mb-2"></i>
                                <div class="fw-bold">Tempo de Empresa</div>
                                @if($worker->worker_start_date)
                                    @php
                                        $startDate = new DateTime($worker->worker_start_date);
                                        $today = new DateTime();
                                        $interval = $today->diff($startDate);
                                    @endphp
                                    <div class="text-muted">
                                        {{ $interval->y }} ano(s), {{ $interval->m }} mês(es) e {{ $interval->d }} dia(s)
                                    </div>
                                @else
                                    <div class="text-muted">Não informado</div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4 text-center mb-3">
                            <div class="p-3 bg-light rounded">
                                <i class="fas fa-birthday-cake fa-2x text-success mb-2"></i>
                                <div class="fw-bold">Idade</div>
                                @if($worker->worker_birth_date)
                                    @php
                                        $birthDate = new DateTime($worker->worker_birth_date);
                                        $today = new DateTime();
                                        $age = $today->diff($birthDate)->y;
                                    @endphp
                                    <div class="text-muted">{{ $age }} anos</div>
                                @else
                                    <div class="text-muted">Não informado</div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4 text-center mb-3">
                            <div class="p-3 bg-light rounded">
                                <i class="fas fa-users fa-2x text-info mb-2"></i>
                                <div class="fw-bold">Cargos</div>
                                <div class="text-muted">
                                    {{ $worker->roles ? $worker->roles->count() : 0 }} cargo(s) atribuído(s)
                                </div>
                            </div>
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
    function editEmployee(button) {
        const workerId = button.getAttribute('data-worker-id');
        // Implementar redirecionamento para edição quando a funcionalidade estiver disponível
        alert('Funcionalidade de edição será implementada em breve. ID: ' + workerId);
        // window.location.href = '/employees/' + workerId + '/edit';
    }
</script>
@endpush
