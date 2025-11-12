@extends('template.layout')

@push('styles')
<style>
    .table-responsive {
        max-height: 600px;
        overflow-y: auto;
    }
</style>
@endpush

@section('content')

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-sign-out-alt me-2"></i>Desligamentos</h4>
                </div>
                <div class="card-body">
                    <!-- Tabs para Funcionários e Desligamentos -->
                    <ul class="nav nav-tabs mb-3" id="layoffsTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="workers-tab" data-bs-toggle="tab" data-bs-target="#workers" type="button" role="tab" aria-controls="workers" aria-selected="true">
                                <i class="fas fa-users me-2"></i>Funcionários Ativos
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="layoffs-tab" data-bs-toggle="tab" data-bs-target="#layoffs" type="button" role="tab" aria-controls="layoffs" aria-selected="false">
                                <i class="fas fa-list me-2"></i>Desligamentos Registrados
                            </button>
                        </li>
                    </ul>
                    
                    <div class="tab-content" id="layoffsTabContent">
                        <!-- Tab de Funcionários -->
                        <div class="tab-pane fade show active" id="workers" role="tabpanel" aria-labelledby="workers-tab">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover" id="workersTable">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Nome</th>
                                            <th>E-mail</th>
                                            <th>CPF</th>
                                            <th>Departamento</th>
                                            <th>Cargo</th>
                                            <th>Data de Admissão</th>
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
                        
                        <!-- Tab de Desligamentos -->
                        <div class="tab-pane fade" id="layoffs" role="tabpanel" aria-labelledby="layoffs-tab">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover" id="layoffsTable">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Funcionário</th>
                                            <th>E-mail</th>
                                            <th>Departamento</th>
                                            <th>Cargo</th>
                                            <th>Data de Desligamento</th>
                                            <th>Tipo</th>
                                            <th>Motivo</th>
                                            <th>Data de Registro</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td colspan="8" class="text-center">
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
    </div>
</div>

<!-- Modal de Desligamento -->
<div class="modal fade" id="layoffModal" tabindex="-1" aria-labelledby="layoffModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="layoffModalLabel">
                    <i class="fas fa-sign-out-alt me-2"></i>Registrar Desligamento
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="layoffForm">
                <div class="modal-body">
                    <input type="hidden" id="layoffWorkerId" name="worker_id">
                    
                    <!-- Informações do Funcionário -->
                    <div class="card bg-light mb-3">
                        <div class="card-body">
                            <h6 class="card-title"><i class="fas fa-user me-2"></i>Dados do Funcionário</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Nome:</strong> <span id="layoffWorkerName"></span></p>
                                    <p class="mb-1"><strong>E-mail:</strong> <span id="layoffWorkerEmail"></span></p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Departamento:</strong> <span id="layoffWorkerDepartment"></span></p>
                                    <p class="mb-1"><strong>Cargo:</strong> <span id="layoffWorkerPosition"></span></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="layoffDate" class="form-label required-field">Data de Desligamento <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="layoffDate" name="layoff_date" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="layoffType" class="form-label required-field">Tipo de Desligamento <span class="text-danger">*</span></label>
                            <select class="form-select" id="layoffType" name="layoff_type" required>
                                <option value="">Selecione...</option>
                                <option value="pedido_demissao">Pedido de Demissão</option>
                                <option value="demitido">Demitido</option>
                                <option value="rescisao_indireta">Rescisão Indireta</option>
                                <option value="justa_causa">Justa Causa</option>
                                <option value="outro">Outro</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="layoffReason" class="form-label">Motivo do Desligamento</label>
                        <input type="text" class="form-control" id="layoffReason" name="reason" maxlength="255" placeholder="Descreva o motivo do desligamento...">
                    </div>

                    <div class="mb-3">
                        <label for="layoffObservations" class="form-label">Observações</label>
                        <textarea class="form-control" id="layoffObservations" name="observations" rows="3" placeholder="Observações adicionais sobre o desligamento..."></textarea>
                    </div>

                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Aviso Prévio</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="hasNoticePeriod" name="has_notice_period" value="1">
                                    <label class="form-check-label" for="hasNoticePeriod">
                                        Funcionário teve aviso prévio
                                    </label>
                                </div>
                            </div>
                            <div class="mb-3" id="noticePeriodDaysDiv" style="display: none;">
                                <label for="noticePeriodDays" class="form-label">Dias de Aviso Prévio</label>
                                <input type="number" class="form-control" id="noticePeriodDays" name="notice_period_days" min="0" placeholder="Ex: 30">
                            </div>
                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-money-bill-wave me-2"></i>Rescisão</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="severancePay" class="form-label">Valor da Rescisão (R$)</label>
                                    <input type="number" class="form-control" id="severancePay" name="severance_pay" step="0.01" min="0" placeholder="0.00">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="severanceDetails" class="form-label">Detalhes da Rescisão</label>
                                <textarea class="form-control" id="severanceDetails" name="severance_details" rows="3" placeholder="Detalhes sobre os valores da rescisão..."></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-laptop me-2"></i>Equipamentos</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="returnedEquipment" name="returned_equipment" value="1">
                                    <label class="form-check-label" for="returnedEquipment">
                                        Equipamentos foram devolvidos
                                    </label>
                                </div>
                            </div>
                            <div class="mb-3" id="equipmentDetailsDiv" style="display: none;">
                                <label for="equipmentDetails" class="form-label">Detalhes dos Equipamentos</label>
                                <textarea class="form-control" id="equipmentDetails" name="equipment_details" rows="3" placeholder="Liste os equipamentos devolvidos..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-save me-1"></i>Registrar Desligamento
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Carregar funcionários ativos
        function loadActiveWorkers() {
            $.ajax({
                url: '{{ route("layoffs.active.workers") }}',
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        renderWorkersTable(response.data);
                    }
                },
                error: function() {
                    $('#workersTable tbody').html('<tr><td colspan="7" class="text-center text-danger">Erro ao carregar funcionários.</td></tr>');
                }
            });
        }

        // Renderizar tabela de funcionários
        function renderWorkersTable(workers) {
            const tbody = $('#workersTable tbody');
            
            if (workers.length === 0) {
                tbody.html('<tr><td colspan="7" class="text-center text-muted">Nenhum funcionário ativo encontrado.</td></tr>');
                return;
            }

            let html = '';
            workers.forEach(function(worker) {
                html += `
                    <tr>
                        <td>${worker.worker_name}</td>
                        <td>${worker.worker_email}</td>
                        <td>${worker.worker_document}</td>
                        <td>${worker.department}</td>
                        <td>${worker.position}</td>
                        <td>${worker.worker_start_date}</td>
                        <td>
                            <button class="btn btn-sm btn-danger btn-layoff-worker" 
                                    data-worker-id="${worker.worker_id}"
                                    data-worker-name="${worker.worker_name}"
                                    data-worker-email="${worker.worker_email}"
                                    data-worker-department="${worker.department}"
                                    data-worker-position="${worker.position}"
                                    title="Desligar Funcionário">
                                <i class="fas fa-sign-out-alt"></i> Desligar
                            </button>
                        </td>
                    </tr>
                `;
            });
            
            tbody.html(html);
        }

        // Carregar desligamentos
        function loadLayoffs() {
            $.ajax({
                url: '{{ route("layoffs.data") }}',
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        renderLayoffsTable(response.data);
                    }
                },
                error: function() {
                    $('#layoffsTable tbody').html('<tr><td colspan="8" class="text-center text-danger">Erro ao carregar desligamentos.</td></tr>');
                }
            });
        }

        // Renderizar tabela de desligamentos
        function renderLayoffsTable(layoffs) {
            const tbody = $('#layoffsTable tbody');
            
            if (layoffs.length === 0) {
                tbody.html('<tr><td colspan="8" class="text-center text-muted">Nenhum desligamento registrado.</td></tr>');
                return;
            }

            let html = '';
            layoffs.forEach(function(layoff) {
                html += `
                    <tr>
                        <td>${layoff.worker_name}</td>
                        <td>${layoff.worker_email}</td>
                        <td>${layoff.department}</td>
                        <td>${layoff.position}</td>
                        <td>${layoff.layoff_date}</td>
                        <td><span class="badge bg-danger">${layoff.layoff_type}</span></td>
                        <td>${layoff.reason}</td>
                        <td>${layoff.created_at}</td>
                    </tr>
                `;
            });
            
            tbody.html(html);
        }

        // Abrir modal de desligamento
        $(document).on('click', '.btn-layoff-worker', function() {
            const workerId = $(this).data('worker-id');
            const workerName = $(this).data('worker-name');
            const workerEmail = $(this).data('worker-email');
            const workerDepartment = $(this).data('worker-department');
            const workerPosition = $(this).data('worker-position');
            
            $('#layoffWorkerId').val(workerId);
            $('#layoffWorkerName').text(workerName);
            $('#layoffWorkerEmail').text(workerEmail);
            $('#layoffWorkerDepartment').text(workerDepartment);
            $('#layoffWorkerPosition').text(workerPosition);
            
            // Resetar formulário
            $('#layoffForm')[0].reset();
            $('#layoffWorkerId').val(workerId);
            $('#hasNoticePeriod').prop('checked', false);
            $('#returnedEquipment').prop('checked', false);
            $('#noticePeriodDaysDiv').hide();
            $('#equipmentDetailsDiv').hide();
            
            // Definir data de hoje como padrão
            const today = new Date().toISOString().split('T')[0];
            $('#layoffDate').val(today);
            
            $('#layoffModal').modal('show');
        });

        // Mostrar/esconder campos condicionais
        $('#hasNoticePeriod').on('change', function() {
            $('#noticePeriodDaysDiv').toggle($(this).is(':checked'));
        });

        $('#returnedEquipment').on('change', function() {
            $('#equipmentDetailsDiv').toggle($(this).is(':checked'));
        });

        // Salvar desligamento
        $('#layoffForm').on('submit', function(e) {
            e.preventDefault();
            
            // Converter checkboxes para boolean
            const hasNoticePeriod = $('#hasNoticePeriod').is(':checked');
            const returnedEquipment = $('#returnedEquipment').is(':checked');
            
            // Coletar dados do formulário
            const formData = {
                worker_id: $('#layoffWorkerId').val(),
                layoff_date: $('#layoffDate').val(),
                layoff_type: $('#layoffType').val(),
                reason: $('#layoffReason').val(),
                observations: $('#layoffObservations').val(),
                has_notice_period: hasNoticePeriod ? 1 : 0,
                notice_period_days: $('#noticePeriodDays').val() || null,
                severance_pay: $('#severancePay').val() || null,
                severance_details: $('#severanceDetails').val(),
                returned_equipment: returnedEquipment ? 1 : 0,
                equipment_details: $('#equipmentDetails').val(),
            };
            
            $.ajax({
                url: '{{ route("layoffs.store") }}',
                method: 'POST',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        $('#layoffModal').modal('hide');
                        alert(response.message);
                        loadActiveWorkers();
                        loadLayoffs();
                        // Mudar para aba de desligamentos
                        $('#layoffs-tab').tab('show');
                    } else {
                        alert('Erro: ' + (response.message || 'Não foi possível registrar o desligamento.'));
                    }
                },
                error: function(xhr) {
                    const message = xhr.responseJSON?.message || 'Erro ao registrar desligamento.';
                    if (xhr.responseJSON?.errors) {
                        const errors = Object.values(xhr.responseJSON.errors).flat().join('\n');
                        alert('Erro de validação:\n' + errors);
                    } else {
                        alert('Erro: ' + message);
                    }
                }
            });
        });

        // Carregar dados ao mudar de aba
        $('#layoffs-tab').on('shown.bs.tab', function() {
            loadLayoffs();
        });

        // Carregar funcionários ao iniciar
        loadActiveWorkers();
    });
</script>
@endpush
