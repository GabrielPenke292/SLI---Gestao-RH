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
</style>
@endpush

@section('content')

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="row">
                <div class="col-12 d-flex justify-content-end gap-2">
                    <!-- btn add new employee aligned right -->
                    <a href="{{ route('employees.upload') }}" class="btn btn-success mb-3">
                        <i class="fas fa-upload me-2"></i>Upload Planilha
                    </a>
                    <a href="{{ route('employees.create') }}" class="btn btn-primary mb-3">
                        <i class="fas fa-plus me-2"></i>Novo Funcionário
                    </a>
                </div>
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><i class="fas fa-users me-2"></i>Quadro de Funcionários</h4>
                    </div>
                    <div class="card-body">
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
                                        <p>Tem certeza que deseja excluir este funcionário?</p>
                                        <p class="text-muted mb-0">
                                            <small>
                                                <i class="fas fa-info-circle me-1"></i>
                                                Esta ação é definitiva e irreversível. Todos os registros relacionados a este funcionário serão excluídos.
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

                        <div class="table-responsive">
                            <table id="employeesTable" class="table table-striped table-hover table-bordered nowrap w-100" style="width:100%">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Nome</th>
                                        <th>Email</th>
                                        <th>Cargo</th>
                                        <th>Departamento</th>
                                        <th>Data de Admissão</th>
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
    @endsection

    @push('scripts')
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
    <script>
        const baseUrl = "{{ url('/') }}";
        
    </script>
    <script>
        $(document).ready(function() {
            $('#employeesTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: "{{ route('employees.data') }}",
                    type: "GET",
                    error: function(xhr, error, code) {
                        console.error('Erro ao carregar dados:', error);
                        console.error('Código:', code);
                        console.error('Response:', xhr.responseText);
                    }
                },
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.13.7/i18n/pt-BR.json"
                },
                columns: [{
                        data: 'id',
                        name: 'id',
                        width: '5%'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'position',
                        name: 'position'
                    },
                    {
                        data: 'department',
                        name: 'department'
                    },
                    {
                        data: 'hire_date',
                        name: 'hire_date',
                        render: function(data) {
                            if (data) {
                                return new Date(data).toLocaleDateString('pt-BR');
                            }
                            return '-';
                        }
                    },
                    {
                        data: 'status',
                        name: 'status',
                        render: function(data) {
                            if (data === 'active' || data === 1) {
                                return '<span class="badge bg-success">Ativo</span>';
                            }
                            return '<span class="badge bg-secondary">Inativo</span>';
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        width: '10%',
                        defaultContent: '',
                        render: function(data, type, row) {
                            // Gerar HTML dos botões de ação
                            let html = '<div class="btn-group btn-group-sm" role="group">';
                            html += '<a href="' + baseUrl + '/employees/' + row.id + '" class="btn btn-info btn-view" data-id="' + row.id + '" title="Visualizar">';
                            html += '<i class="fas fa-eye"></i>';
                            html += '</a>';
                            html += '<a href="' + baseUrl + '/employees/' + row.id + '/edit" class="btn btn-warning btn-edit" data-id="' + row.id + '" title="Editar">';
                            html += '<i class="fas fa-edit"></i>';
                            html += '</a>';
                            html += '<button type="button" class="btn btn-danger btn-delete" data-id="' + row.id + '" title="Excluir">';
                            html += '<i class="fas fa-trash"></i>';
                            html += '</button>';
                            html += '</div>';
                            return html;
                        }
                    }
                ],
                order: [
                    [0, 'desc']
                ],
                pageLength: 10,
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "Todos"]
                ]
            });

            let deleteWorkerId = null;
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));

            $(document).on('click', '.btn-delete', function(e) {
                e.preventDefault();
                deleteWorkerId = $(this).data('id');
                deleteModal.show();
            });

            $('#confirmDeleteBtn').on('click', function() {
                if (!deleteWorkerId) return;

                const btn = $(this);
                const originalHtml = btn.html();
                
                // Desabilitar botão e mostrar loading
                btn.prop('disabled', true);
                btn.html('<span class="spinner-border spinner-border-sm me-1"></span>Excluindo...');

                $.ajax({
                    url: baseUrl + '/employees/' + deleteWorkerId,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        deleteModal.hide();
                        
                        // Mostrar mensagem de sucesso
                        const alertHtml = '<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                            '<i class="fas fa-check-circle me-2"></i>' + response.message +
                            '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                            '</div>';
                        $('.card-body').prepend(alertHtml);

                        // Recarregar tabela
                        $('#employeesTable').DataTable().ajax.reload(null, false);

                        // Resetar variável
                        deleteWorkerId = null;
                    },
                    error: function(xhr) {
                        let errorMessage = 'Erro ao excluir funcionário.';
                        
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }

                        deleteModal.hide();
                        
                        // Mostrar mensagem de erro
                        const alertHtml = '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                            '<i class="fas fa-exclamation-circle me-2"></i>' + errorMessage +
                            '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                            '</div>';
                        $('.card-body').prepend(alertHtml);
                    },
                    complete: function() {
                        // Reabilitar botão
                        btn.prop('disabled', false);
                        btn.html(originalHtml);
                        deleteWorkerId = null;
                    }
                });
            });
        });
    </script>
    @endpush