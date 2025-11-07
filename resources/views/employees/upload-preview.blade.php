@extends('template.layout')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<style>
    .status-badge {
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        font-size: 0.875rem;
        font-weight: 500;
    }

    .status-ok {
        background-color: #d1e7dd;
        color: #0f5132;
    }

    .status-error {
        background-color: #f8d7da;
        color: #842029;
    }

    .status-exists {
        background-color: #fff3cd;
        color: #664d03;
    }

    .stats-card {
        border-left: 4px solid;
        transition: transform 0.2s;
    }

    .stats-card:hover {
        transform: translateY(-2px);
    }

    .stats-card.total {
        border-left-color: #0d6efd;
    }

    .stats-card.to-insert {
        border-left-color: #198754;
    }

    .stats-card.exists {
        border-left-color: #ffc107;
    }

    .stats-card.errors {
        border-left-color: #dc3545;
    }

    .error-list {
        max-height: 100px;
        overflow-y: auto;
        font-size: 0.875rem;
    }
</style>
@endpush

@section('content')

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0"><i class="fas fa-file-excel me-2"></i>Preview dos Dados Importados</h4>
                        <small class="text-white">Revise os dados antes de confirmar a importação</small>
                    </div>
                    <a href="{{ route('employees.upload') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>Voltar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Estatísticas -->
    <div class="row mt-3">
        <div class="col-md-3 mb-3">
            <div class="card stats-card total h-100">
                <div class="card-body">
                    <h5 class="card-title text-primary">
                        <i class="fas fa-list me-2"></i>Total de Linhas
                    </h5>
                    <h2 class="mb-0">{{ $stats['total'] }}</h2>
                    <small class="text-muted">Linhas processadas</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card stats-card to-insert h-100">
                <div class="card-body">
                    <h5 class="card-title text-success">
                        <i class="fas fa-check-circle me-2"></i>Para Inserir
                    </h5>
                    <h2 class="mb-0 text-success">{{ $stats['to_insert'] }}</h2>
                    <small class="text-muted">Serão cadastrados</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card stats-card exists h-100">
                <div class="card-body">
                    <h5 class="card-title text-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>Já Existem
                    </h5>
                    <h2 class="mb-0 text-warning">{{ $stats['already_exists'] }}</h2>
                    <small class="text-muted">Email já cadastrado</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card stats-card errors h-100">
                <div class="card-body">
                    <h5 class="card-title text-danger">
                        <i class="fas fa-times-circle me-2"></i>Com Erros
                    </h5>
                    <h2 class="mb-0 text-danger">{{ $stats['with_errors'] }}</h2>
                    <small class="text-muted">Não serão inseridos</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabela de Preview -->
    <div class="row mt-3">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="previewTable" class="table table-striped table-hover table-bordered" style="width:100%">
                            <thead class="table-light">
                                <tr>
                                    <th>Linha</th>
                                    <th>Status</th>
                                    <th>Nome</th>
                                    <th>Email</th>
                                    <th>CPF</th>
                                    <th>Departamento</th>
                                    <th>Cargo(s)</th>
                                    <th>Data Admissão</th>
                                    <th>Salário</th>
                                    <th>Erros</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($processedData as $data)
                                <tr>
                                    <td>{{ $data['line'] }}</td>
                                    <td>
                                        @if($data['status'] === 'ok')
                                            <span class="status-badge status-ok">
                                                <i class="fas fa-check me-1"></i>OK
                                            </span>
                                        @elseif($data['status'] === 'exists')
                                            <span class="status-badge status-exists">
                                                <i class="fas fa-exclamation-triangle me-1"></i>Já Existe
                                            </span>
                                        @else
                                            <span class="status-badge status-error">
                                                <i class="fas fa-times me-1"></i>Erro
                                            </span>
                                        @endif
                                    </td>
                                    <td>{{ $data['worker_name'] }}</td>
                                    <td>{{ $data['worker_email'] }}</td>
                                    <td>{{ $data['worker_document'] }}</td>
                                    <td>{{ $data['department_name'] }}</td>
                                    <td>
                                        @if(!empty($data['role_names']))
                                            {{ implode(', ', $data['role_names']) }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($data['worker_start_date'])
                                            {{ date('d/m/Y', strtotime($data['worker_start_date'])) }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($data['worker_salary'])
                                            R$ {{ number_format($data['worker_salary'], 2, ',', '.') }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(!empty($data['errors']))
                                            <div class="error-list">
                                                <ul class="mb-0 text-danger" style="padding-left: 1.2rem;">
                                                    @foreach($data['errors'] as $error)
                                                        <li>{{ $error }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Apenas os registros com status "OK" serão inseridos no banco de dados.
                        </small>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('employees.upload') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </a>
                        @if($stats['to_insert'] > 0)
                            <form action="{{ route('employees.upload.confirm') }}" method="POST" id="confirmForm">
                                @csrf
                                <button type="submit" class="btn btn-success" onclick="return confirm('Tem certeza que deseja salvar {{ $stats['to_insert'] }} funcionário(s)?');">
                                    <i class="fas fa-save me-2"></i>Confirmar e Salvar ({{ $stats['to_insert'] }})
                                </button>
                            </form>
                        @else
                            <button type="button" class="btn btn-success" disabled>
                                <i class="fas fa-save me-2"></i>Nenhum registro para salvar
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        $('#previewTable').DataTable({
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.13.7/i18n/pt-BR.json"
            },
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Todos"]],
            order: [[0, 'asc']],
            columnDefs: [
                { orderable: false, targets: [9] } // Coluna de erros não ordenável
            ]
        });
    });
</script>
@endpush

