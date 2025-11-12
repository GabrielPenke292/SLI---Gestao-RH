@extends('template.layout')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<style>
    .status-badge {
        font-size: 0.85rem;
    }
    .select2-container--bootstrap-5 .select2-selection {
        min-height: 38px;
    }
</style>
@endpush

@section('content')

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fas fa-exchange-alt me-2"></i>Movimentações de Cargo</h4>
                    <button type="button" class="btn btn-light btn-sm" id="btnNewMovement">
                        <i class="fas fa-plus me-1"></i>Nova Movimentação
                    </button>
                </div>
                <div class="card-body">
                    <!-- Tabela de Movimentações -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="movementsTable">
                            <thead class="table-dark">
                                <tr>
                                    <th>Funcionário</th>
                                    <th>Departamento</th>
                                    <th>Cargo</th>
                                    <th>Status</th>
                                    <th>Solicitado por</th>
                                    <th>Data</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="7" class="text-center">
                                        <div class="spinner-border" role="status">
                                            <span class="visually-hidden">Carregando...</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Nova Movimentação -->
<div class="modal fade" id="movementModal" tabindex="-1" aria-labelledby="movementModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="movementModalLabel">
                    <i class="fas fa-exchange-alt me-2"></i>Nova Movimentação de Cargo
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="movementForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="movementWorker" class="form-label required-field">Funcionário <span class="text-danger">*</span></label>
                        <select class="form-select" id="movementWorker" name="worker_id" style="width: 100%;" required>
                            <option value=""></option>
                        </select>
                        <small class="form-text text-muted">Digite pelo menos 2 caracteres para buscar o funcionário.</small>
                    </div>

                    <div id="workerInfoCard" style="display: none;" class="mb-3">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title"><i class="fas fa-user me-2"></i>Dados Atuais do Funcionário</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Nome:</strong> <span id="currentWorkerName"></span></p>
                                        <p class="mb-1"><strong>E-mail:</strong> <span id="currentWorkerEmail"></span></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Departamento Atual:</strong> <span id="currentDepartment"></span></p>
                                        <p class="mb-0"><strong>Cargo(s) Atual(is):</strong> <span id="currentRoles"></span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="newDepartment" class="form-label">Novo Departamento</label>
                            <select class="form-select" id="newDepartment" name="new_department_id" style="width: 100%;">
                                <option value="">Selecione um departamento...</option>
                            </select>
                            <small class="form-text text-muted">Deixe em branco se não houver mudança de departamento.</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="newRole" class="form-label">Novo Cargo</label>
                            <select class="form-select" id="newRole" name="new_role_id" style="width: 100%;">
                                <option value="">Selecione um cargo...</option>
                            </select>
                            <small class="form-text text-muted">Deixe em branco se não houver mudança de cargo.</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="movementObservation" class="form-label">Observação</label>
                        <textarea class="form-control" id="movementObservation" name="observation" rows="3" placeholder="Observações sobre a movimentação..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Solicitar Movimentação
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de Rejeitar Movimentação -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="rejectModalLabel">
                    <i class="fas fa-times-circle me-2"></i>Rejeitar Movimentação
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="rejectForm">
                <div class="modal-body">
                    <input type="hidden" id="rejectMovementId">
                    <div class="mb-3">
                        <label for="rejectionReason" class="form-label">Motivo da Rejeição</label>
                        <textarea class="form-control" id="rejectionReason" name="rejection_reason" rows="3" placeholder="Digite o motivo da rejeição (opcional)..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Rejeitar</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@php
    $permissions = session('user.permissions', []);
    $canApprove = in_array('admin', $permissions) || in_array('diretoria', $permissions) || in_array('gerente rh', $permissions);
@endphp
<script>
    $(document).ready(function() {
        let currentMovementId = null;
        let canApprove = @json($canApprove);

        // Função para inicializar Select2 do funcionário
        function initWorkerSelect2() {
            // Verificar se já está inicializado e destruir
            if ($('#movementWorker').hasClass('select2-hidden-accessible')) {
                $('#movementWorker').select2('destroy');
            }
            
            $('#movementWorker').select2({
                theme: 'bootstrap-5',
                placeholder: 'Digite o nome do funcionário...',
                allowClear: true,
                dropdownParent: $('#movementModal'),
                ajax: {
                    url: '{{ route("movements.workers") }}',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term || '',
                            page: params.page || 1
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.results || []
                        };
                    },
                    cache: true
                },
                minimumInputLength: 0,
                language: {
                    inputTooShort: function() {
                        return 'Digite pelo menos 2 caracteres para buscar...';
                    },
                    noResults: function() {
                        return 'Nenhum funcionário encontrado';
                    },
                    searching: function() {
                        return 'Buscando...';
                    }
                }
            });
        }
        
        // Inicializar Select2 do funcionário
        initWorkerSelect2();

        $('#newDepartment').select2({
            theme: 'bootstrap-5',
            placeholder: 'Selecione um departamento...',
            allowClear: true
        });

        $('#newRole').select2({
            theme: 'bootstrap-5',
            placeholder: 'Selecione um cargo...',
            allowClear: true
        });

        // Carregar departamentos e cargos
        function loadDepartments() {
            $.ajax({
                url: '{{ route("movements.departments") }}',
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        const select = $('#newDepartment');
                        select.html('<option value="">Selecione um departamento...</option>');
                        response.data.forEach(function(dept) {
                            select.append(`<option value="${dept.id}">${dept.text}</option>`);
                        });
                    }
                }
            });
        }

        function loadRoles() {
            $.ajax({
                url: '{{ route("movements.roles") }}',
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        const select = $('#newRole');
                        select.html('<option value="">Selecione um cargo...</option>');
                        response.data.forEach(function(role) {
                            select.append(`<option value="${role.id}">${role.text}</option>`);
                        });
                    }
                }
            });
        }

        // Quando selecionar funcionário
        $('#movementWorker').on('change', function() {
            const workerId = $(this).val();
            
            if (workerId) {
                $.ajax({
                    url: '{{ route("movements.worker.data", ":id") }}'.replace(':id', workerId),
                    method: 'GET',
                    success: function(response) {
                        if (response.success) {
                            const data = response.data;
                            $('#currentWorkerName').text(data.worker_name);
                            $('#currentWorkerEmail').text(data.worker_email);
                            $('#currentDepartment').text(data.department_name || 'Sem departamento');
                            $('#currentRoles').text(data.role_names.join(', ') || 'Sem cargo');
                            $('#workerInfoCard').show();
                        }
                    }
                });
            } else {
                $('#workerInfoCard').hide();
            }
        });

        // Carregar movimentações
        function loadMovements() {
            $.ajax({
                url: '{{ route("movements.data") }}',
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        renderTable(response.data);
                    }
                },
                error: function() {
                    $('#movementsTable tbody').html('<tr><td colspan="7" class="text-center text-danger">Erro ao carregar movimentações.</td></tr>');
                }
            });
        }

        // Renderizar tabela
        function renderTable(movements) {
            const tbody = $('#movementsTable tbody');
            
            if (movements.length === 0) {
                tbody.html('<tr><td colspan="7" class="text-center text-muted">Nenhuma movimentação registrada.</td></tr>');
                return;
            }

            let html = '';
            movements.forEach(function(movement) {
                const statusBadge = `<span class="badge bg-${movement.status_badge} status-badge">${movement.status_label}</span>`;
                
                let actions = '';
                if (movement.status === 'pendente' && canApprove) {
                    actions = `
                        <button class="btn btn-sm btn-success btn-approve" data-movement-id="${movement.movement_id}" title="Aprovar">
                            <i class="fas fa-check"></i>
                        </button>
                        <button class="btn btn-sm btn-danger btn-reject" data-movement-id="${movement.movement_id}" title="Rejeitar">
                            <i class="fas fa-times"></i>
                        </button>
                    `;
                } else {
                    actions = '-';
                }
                
                html += `
                    <tr>
                        <td>${movement.worker_name}</td>
                        <td>${movement.old_department} → ${movement.new_department}</td>
                        <td>${movement.old_role} → ${movement.new_role}</td>
                        <td>${statusBadge}</td>
                        <td>${movement.requested_by}</td>
                        <td>${movement.created_at}</td>
                        <td>${actions}</td>
                    </tr>
                `;
            });
            
            tbody.html(html);
        }

        // Abrir modal de nova movimentação
        $('#btnNewMovement').on('click', function() {
            loadDepartments();
            loadRoles();
            $('#movementForm')[0].reset();
            $('#movementWorker').val(null).trigger('change');
            $('#newDepartment').val(null).trigger('change');
            $('#newRole').val(null).trigger('change');
            $('#workerInfoCard').hide();
            
            // Reinicializar Select2 do funcionário quando o modal abrir
            initWorkerSelect2();
            
            $('#movementModal').modal('show');
            
            // Focar no campo após o modal abrir completamente
            $('#movementModal').one('shown.bs.modal', function() {
                setTimeout(function() {
                    $('#movementWorker').select2('open');
                }, 100);
            });
        });

        // Salvar movimentação
        $('#movementForm').on('submit', function(e) {
            e.preventDefault();
            
            const formData = $(this).serialize();

            $.ajax({
                url: '{{ route("movements.store") }}',
                method: 'POST',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        $('#movementModal').modal('hide');
                        alert(response.message);
                        loadMovements();
                    } else {
                        alert('Erro: ' + (response.message || 'Não foi possível solicitar a movimentação.'));
                    }
                },
                error: function(xhr) {
                    const message = xhr.responseJSON?.message || 'Erro ao solicitar movimentação.';
                    if (xhr.responseJSON?.errors) {
                        const errors = Object.values(xhr.responseJSON.errors).flat().join('\n');
                        alert('Erro de validação:\n' + errors);
                    } else {
                        alert('Erro: ' + message);
                    }
                }
            });
        });

        // Aprovar movimentação
        $(document).on('click', '.btn-approve', function() {
            if (!confirm('Tem certeza que deseja aprovar esta movimentação? O funcionário será atualizado imediatamente.')) {
                return;
            }

            const movementId = $(this).data('movement-id');

            $.ajax({
                url: '{{ route("movements.approve", ":id") }}'.replace(':id', movementId),
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        loadMovements();
                    } else {
                        alert('Erro: ' + (response.message || 'Não foi possível aprovar a movimentação.'));
                    }
                },
                error: function(xhr) {
                    const message = xhr.responseJSON?.message || 'Erro ao aprovar movimentação.';
                    alert('Erro: ' + message);
                }
            });
        });

        // Rejeitar movimentação
        $(document).on('click', '.btn-reject', function() {
            currentMovementId = $(this).data('movement-id');
            $('#rejectForm')[0].reset();
            $('#rejectMovementId').val(currentMovementId);
            $('#rejectModal').modal('show');
        });

        $('#rejectForm').on('submit', function(e) {
            e.preventDefault();
            
            if (!currentMovementId) {
                return;
            }

            const formData = $(this).serialize();

            $.ajax({
                url: '{{ route("movements.reject", ":id") }}'.replace(':id', currentMovementId),
                method: 'POST',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        $('#rejectModal').modal('hide');
                        alert(response.message);
                        loadMovements();
                    } else {
                        alert('Erro: ' + (response.message || 'Não foi possível rejeitar a movimentação.'));
                    }
                },
                error: function(xhr) {
                    const message = xhr.responseJSON?.message || 'Erro ao rejeitar movimentação.';
                    alert('Erro: ' + message);
                }
            });
        });

        // Carregar movimentações ao iniciar
        loadMovements();
    });
</script>
@endpush
