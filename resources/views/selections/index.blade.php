@extends('template.layout')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
<style>
    .dataTables_wrapper .dataTables_length select {
        padding: 0.25rem 0.5rem;
        margin: 0 0.5rem;
    }

    .dataTables_wrapper .dataTables_filter input {
        padding: 0.25rem 0.5rem;
        margin-left: 0.5rem;
    }

    .table-responsive {
        overflow-x: auto;
    }

    .process-card {
        transition: transform 0.2s;
        cursor: pointer;
    }

    .process-card:hover {
        transform: translateY(-5px);
    }

    .card-header-custom {
        font-weight: 600;
    }
</style>
@endpush

@section('content')

<div class="container mt-4">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="mb-0"><i class="fas fa-clipboard-list me-2"></i>Processos Seletivos</h4>
            <a href="{{ route('selections.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Novo Processo Seletivo
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Cards de Resumo -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card process-card border-warning" data-status="awaiting">
                <div class="card-body text-center">
                    <h5 class="card-title text-warning">
                        <i class="fas fa-clock me-2"></i>Aguardando Aprovação
                    </h5>
                    <h2 class="text-warning mb-0">{{ $awaitingApproval }}</h2>
                    <small class="text-muted">Processos aguardando aprovação</small>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card process-card border-success" data-status="in-progress">
                <div class="card-body text-center">
                    <h5 class="card-title text-success">
                        <i class="fas fa-spinner me-2"></i>Em Andamento
                    </h5>
                    <h2 class="text-success mb-0">{{ $inProgress }}</h2>
                    <small class="text-muted">Processos em andamento</small>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card process-card border-secondary" data-status="finished">
                <div class="card-body text-center">
                    <h5 class="card-title text-secondary">
                        <i class="fas fa-check-circle me-2"></i>Encerrados
                    </h5>
                    <h2 class="text-secondary mb-0">{{ $finished }}</h2>
                    <small class="text-muted">Processos finalizados ou congelados</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabelas de Processos -->
    <div class="row">
        <!-- Aguardando Aprovação -->
        <div class="col-12 mb-4" id="awaiting-section">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Processos Aguardando Aprovação</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="awaitingTable" class="table table-striped table-hover table-bordered nowrap w-100" style="width:100%">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Número</th>
                                    <th>Vaga</th>
                                    <th>Motivo</th>
                                    <th>Aprovador</th>
                                    <th>Verba</th>
                                    <th>Data Início</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Os dados serão carregados via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Em Andamento -->
        <div class="col-12 mb-4" id="in-progress-section">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-spinner me-2"></i>Processos Em Andamento</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="inProgressTable" class="table table-striped table-hover table-bordered nowrap w-100" style="width:100%">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Número</th>
                                    <th>Vaga</th>
                                    <th>Motivo</th>
                                    <th>Aprovador</th>
                                    <th>Verba</th>
                                    <th>Data Início</th>
                                    <th>Data Fim</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Os dados serão carregados via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Encerrados -->
        <div class="col-12 mb-4" id="finished-section">
            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-check-circle me-2"></i>Processos Encerrados</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="finishedTable" class="table table-striped table-hover table-bordered nowrap w-100" style="width:100%">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Número</th>
                                    <th>Vaga</th>
                                    <th>Motivo</th>
                                    <th>Aprovador</th>
                                    <th>Verba</th>
                                    <th>Data Início</th>
                                    <th>Data Fim</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Os dados serão carregados via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Aprovação -->
<div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="approveModalLabel">
                    <i class="fas fa-check-circle me-2"></i>Aprovar Processo Seletivo
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="approveForm">
                <div class="modal-body">
                    <p>Tem certeza que deseja aprovar este processo seletivo?</p>
                    <div class="mb-3">
                        <label for="approval_notes" class="form-label">Notas de Aprovação (opcional):</label>
                        <textarea class="form-control" id="approval_notes" name="approval_notes" rows="3" placeholder="Adicione observações sobre a aprovação..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check me-1"></i>Aprovar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de Confirmação de Exclusão -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>Confirmar Exclusão
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir este processo seletivo?</p>
                <p class="text-muted mb-0">
                    <small>
                        <i class="fas fa-info-circle me-1"></i>
                        Esta ação não pode ser desfeita.
                    </small>
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancelar
                </button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                    <i class="fas fa-trash me-1"></i>Excluir
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
<script>
    const baseUrl = "{{ url('/') }}";
    let awaitingTable, inProgressTable, finishedTable;
    let approveProcessId = null;
    let deleteProcessId = null;
    const canApprove = @json($canApprove);
    
    $(document).ready(function() {
        // Configurar DataTables
        awaitingTable = $('#awaitingTable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: "{{ route('selections.awaiting.data') }}",
            language: { url: "https://cdn.datatables.net/plug-ins/1.13.7/i18n/pt-BR.json" },
            columns: [
                { data: 'id', width: '5%' },
                { data: 'process_number' },
                { data: 'vacancy_title' },
                { data: 'reason' },
                { data: 'approver' },
                { data: 'budget', orderable: false },
                { data: 'start_date' },
                { data: 'status', orderable: false },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    width: '15%',
                    render: function(data, type, row) {
                        let html = '<div class="btn-group btn-group-sm" role="group">';
                        html += '<a href="' + baseUrl + '/selections/' + row.id + '/edit" class="btn btn-warning" title="Editar"><i class="fas fa-edit"></i></a>';
                        if (canApprove && row.status_raw === 'aguardando_aprovacao') {
                            html += '<button type="button" class="btn btn-success btn-approve" data-id="' + row.id + '" title="Aprovar"><i class="fas fa-check"></i></button>';
                        }
                        html += '<button type="button" class="btn btn-danger btn-delete" data-id="' + row.id + '" title="Excluir"><i class="fas fa-trash"></i></button>';
                        html += '</div>';
                        return html;
                    }
                }
            ],
            order: [[0, 'desc']],
            pageLength: 10
        });

        inProgressTable = $('#inProgressTable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: "{{ route('selections.in-progress.data') }}",
            language: { url: "https://cdn.datatables.net/plug-ins/1.13.7/i18n/pt-BR.json" },
            columns: [
                { data: 'id', width: '5%' },
                { data: 'process_number' },
                { data: 'vacancy_title' },
                { data: 'reason' },
                { data: 'approver' },
                { data: 'budget', orderable: false },
                { data: 'start_date' },
                { data: 'end_date' },
                { data: 'status', orderable: false },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    width: '10%',
                    render: function(data, type, row) {
                        let html = '<div class="btn-group btn-group-sm" role="group">';
                        html += '<a href="' + baseUrl + '/selections/' + row.id + '/edit" class="btn btn-warning" title="Editar"><i class="fas fa-edit"></i></a>';
                        html += '<button type="button" class="btn btn-danger btn-delete" data-id="' + row.id + '" title="Excluir"><i class="fas fa-trash"></i></button>';
                        html += '</div>';
                        return html;
                    }
                }
            ],
            order: [[0, 'desc']],
            pageLength: 10
        });

        finishedTable = $('#finishedTable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: "{{ route('selections.finished.data') }}",
            language: { url: "https://cdn.datatables.net/plug-ins/1.13.7/i18n/pt-BR.json" },
            columns: [
                { data: 'id', width: '5%' },
                { data: 'process_number' },
                { data: 'vacancy_title' },
                { data: 'reason' },
                { data: 'approver' },
                { data: 'budget', orderable: false },
                { data: 'start_date' },
                { data: 'end_date' },
                { data: 'status', orderable: false },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    width: '10%',
                    render: function(data, type, row) {
                        let html = '<div class="btn-group btn-group-sm" role="group">';
                        html += '<a href="' + baseUrl + '/selections/' + row.id + '/edit" class="btn btn-warning" title="Editar"><i class="fas fa-edit"></i></a>';
                        html += '<button type="button" class="btn btn-danger btn-delete" data-id="' + row.id + '" title="Excluir"><i class="fas fa-trash"></i></button>';
                        html += '</div>';
                        return html;
                    }
                }
            ],
            order: [[0, 'desc']],
            pageLength: 10
        });

        // Clique nos cards para rolar até a seção
        $('.process-card').on('click', function() {
            const status = $(this).data('status');
            const sectionId = status === 'awaiting' ? 'awaiting-section' : 
                             status === 'in-progress' ? 'in-progress-section' : 'finished-section';
            $('html, body').animate({
                scrollTop: $('#' + sectionId).offset().top - 100
            }, 500);
        });

        // Modal de aprovação
        const approveModal = new bootstrap.Modal(document.getElementById('approveModal'));
        $(document).on('click', '.btn-approve', function(e) {
            e.preventDefault();
            approveProcessId = $(this).data('id');
            approveModal.show();
        });

        $('#approveForm').on('submit', function(e) {
            e.preventDefault();
            if (!approveProcessId) return;

            const btn = $('#approveForm button[type="submit"]');
            const originalHtml = btn.html();
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Aprovando...');

            $.ajax({
                url: baseUrl + '/selections/' + approveProcessId + '/approve',
                type: 'POST',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                data: { approval_notes: $('#approval_notes').val() },
                success: function(response) {
                    approveModal.hide();
                    $('#approval_notes').val('');
                    showAlert('success', response.message);
                    awaitingTable.ajax.reload(null, false);
                    inProgressTable.ajax.reload(null, false);
                    location.reload(); // Recarregar para atualizar contadores
                },
                error: function(xhr) {
                    showAlert('danger', xhr.responseJSON?.message || 'Erro ao aprovar processo.');
                },
                complete: function() {
                    btn.prop('disabled', false).html(originalHtml);
                    approveProcessId = null;
                }
            });
        });

        // Modal de exclusão
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        $(document).on('click', '.btn-delete', function(e) {
            e.preventDefault();
            deleteProcessId = $(this).data('id');
            deleteModal.show();
        });

        $('#confirmDeleteBtn').on('click', function() {
            if (!deleteProcessId) return;
            const btn = $(this);
            const originalHtml = btn.html();
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Excluindo...');

            $.ajax({
                url: baseUrl + '/selections/' + deleteProcessId,
                type: 'DELETE',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function(response) {
                    deleteModal.hide();
                    showAlert('success', response.message);
                    awaitingTable.ajax.reload(null, false);
                    inProgressTable.ajax.reload(null, false);
                    finishedTable.ajax.reload(null, false);
                    location.reload();
                },
                error: function(xhr) {
                    showAlert('danger', xhr.responseJSON?.message || 'Erro ao excluir processo.');
                },
                complete: function() {
                    btn.prop('disabled', false).html(originalHtml);
                    deleteProcessId = null;
                }
            });
        });

        function showAlert(type, message) {
            const alertHtml = '<div class="alert alert-' + type + ' alert-dismissible fade show" role="alert">' +
                '<i class="fas fa-' + (type === 'success' ? 'check-circle' : 'exclamation-circle') + ' me-2"></i>' + message +
                '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
            $('.container').first().prepend(alertHtml);
        }
    });
</script>
@endpush
