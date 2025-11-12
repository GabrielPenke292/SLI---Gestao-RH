@extends('template.layout')

@push('styles')
<style>
    .status-badge {
        font-size: 0.85rem;
    }
</style>
@endpush

@section('content')

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fas fa-stethoscope me-2"></i>Exames Demissionais</h4>
                    <button type="button" class="btn btn-light btn-sm" id="btnScheduleExam">
                        <i class="fas fa-plus me-1"></i>Agendar Exame
                    </button>
                </div>
                <div class="card-body">
                    <!-- Tabela de Exames -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="examsTable">
                            <thead class="table-dark">
                                <tr>
                                    <th>Funcionário</th>
                                    <th>E-mail</th>
                                    <th>Departamento</th>
                                    <th>Cargo</th>
                                    <th>Clínica</th>
                                    <th>Data</th>
                                    <th>Horário</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="9" class="text-center">
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

<!-- Modal de Agendar Exame -->
<div class="modal fade" id="scheduleExamModal" tabindex="-1" aria-labelledby="scheduleExamModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="scheduleExamModalLabel">
                    <i class="fas fa-calendar-plus me-2"></i>Agendar Exame Demissional
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="scheduleExamForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="examWorker" class="form-label required-field">Funcionário Desligado <span class="text-danger">*</span></label>
                        <select class="form-select" id="examWorker" name="worker_id" required>
                            <option value="">Selecione um funcionário...</option>
                        </select>
                        <small class="form-text text-muted">Apenas funcionários desligados serão exibidos.</small>
                    </div>

                    <div class="mb-3" id="workerInfo" style="display: none;">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title">Informações do Funcionário</h6>
                                <p class="mb-1"><strong>Nome:</strong> <span id="selectedWorkerName"></span></p>
                                <p class="mb-1"><strong>E-mail:</strong> <span id="selectedWorkerEmail"></span></p>
                                <p class="mb-1"><strong>Departamento:</strong> <span id="selectedWorkerDepartment"></span></p>
                                <p class="mb-1"><strong>Cargo:</strong> <span id="selectedWorkerPosition"></span></p>
                                <p class="mb-0"><strong>Data de Desligamento:</strong> <span id="selectedLayoffDate"></span></p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="examClinic" class="form-label required-field">Clínica <span class="text-danger">*</span></label>
                        <select class="form-select" id="examClinic" name="clinic_id" required>
                            <option value="">Selecione uma clínica...</option>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="examDate" class="form-label required-field">Data do Exame <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="examDate" name="exam_date" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="examTime" class="form-label">Horário</label>
                            <input type="time" class="form-control" id="examTime" name="exam_time">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="examNotes" class="form-label">Observações</label>
                        <textarea class="form-control" id="examNotes" name="notes" rows="3" placeholder="Observações sobre o exame..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-save me-1"></i>Agendar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de Atualizar Status -->
<div class="modal fade" id="updateStatusModal" tabindex="-1" aria-labelledby="updateStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="updateStatusModalLabel">
                    <i class="fas fa-edit me-2"></i>Atualizar Status do Exame
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="updateStatusForm">
                <div class="modal-body">
                    <input type="hidden" id="statusExamId">
                    
                    <div class="mb-3">
                        <label for="examStatus" class="form-label required-field">Status <span class="text-danger">*</span></label>
                        <select class="form-select" id="examStatus" name="status" required>
                            <option value="agendado">Agendado</option>
                            <option value="cancelado">Cancelado</option>
                            <option value="finalizado">Finalizado</option>
                        </select>
                    </div>

                    <div class="mb-3" id="cancellationReasonDiv" style="display: none;">
                        <label for="cancellationReason" class="form-label required-field">Motivo do Cancelamento <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="cancellationReason" name="cancellation_reason" rows="3" placeholder="Digite o motivo do cancelamento..."></textarea>
                    </div>

                    <div class="mb-3" id="examResultDiv" style="display: none;">
                        <label for="examResult" class="form-label">Resultado do Exame</label>
                        <textarea class="form-control" id="examResult" name="exam_result" rows="3" placeholder="Digite o resultado do exame..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning">Atualizar Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        let currentExamId = null;

        // Carregar exames
        function loadExams() {
            $.ajax({
                url: '{{ route("exams.dismissals.data") }}',
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        renderTable(response.data);
                    }
                },
                error: function() {
                    $('#examsTable tbody').html('<tr><td colspan="9" class="text-center text-danger">Erro ao carregar exames.</td></tr>');
                }
            });
        }

        // Renderizar tabela
        function renderTable(exams) {
            const tbody = $('#examsTable tbody');
            
            if (exams.length === 0) {
                tbody.html('<tr><td colspan="9" class="text-center text-muted">Nenhum exame agendado.</td></tr>');
                return;
            }

            let html = '';
            exams.forEach(function(exam) {
                const statusBadge = getStatusBadge(exam.status);
                
                html += `
                    <tr>
                        <td>${exam.worker_name}</td>
                        <td>${exam.worker_email}</td>
                        <td>${exam.department}</td>
                        <td>${exam.position}</td>
                        <td>${exam.clinic_name}</td>
                        <td>${exam.exam_date}</td>
                        <td>${exam.exam_time}</td>
                        <td>${statusBadge}</td>
                        <td>
                            <button class="btn btn-sm btn-info btn-generate-pdf" data-exam-id="${exam.dismissal_exam_id}" title="Gerar PDF">
                                <i class="fas fa-file-pdf"></i>
                            </button>
                            <button class="btn btn-sm btn-warning btn-update-status" data-exam-id="${exam.dismissal_exam_id}" title="Atualizar Status">
                                <i class="fas fa-edit"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });
            
            tbody.html(html);
        }

        function getStatusBadge(status) {
            const badges = {
                'agendado': '<span class="badge bg-primary status-badge">Agendado</span>',
                'cancelado': '<span class="badge bg-danger status-badge">Cancelado</span>',
                'finalizado': '<span class="badge bg-success status-badge">Finalizado</span>'
            };
            return badges[status] || status;
        }

        // Carregar funcionários desligados
        function loadDismissedWorkers() {
            $.ajax({
                url: '{{ route("exams.dismissals.dismissed.workers") }}',
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        const select = $('#examWorker');
                        select.html('<option value="">Selecione um funcionário...</option>');
                        response.data.forEach(function(worker) {
                            select.append(`<option value="${worker.worker_id}" 
                                data-name="${worker.worker_name}"
                                data-email="${worker.worker_email}"
                                data-department="${worker.department}"
                                data-position="${worker.position}"
                                data-layoff-date="${worker.layoff_date}">${worker.worker_name} - ${worker.department} (Desligado em ${worker.layoff_date})</option>`);
                        });
                    }
                },
                error: function() {
                    alert('Erro ao carregar funcionários desligados.');
                }
            });
        }

        // Carregar clínicas ativas
        function loadActiveClinics() {
            $.ajax({
                url: '{{ route("exams.admissionals.active.clinics") }}',
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        const select = $('#examClinic');
                        select.html('<option value="">Selecione uma clínica...</option>');
                        response.data.forEach(function(clinic) {
                            select.append(`<option value="${clinic.clinic_id}">${clinic.corporate_name}${clinic.trade_name && clinic.trade_name !== clinic.corporate_name ? ' - ' + clinic.trade_name : ''}</option>`);
                        });
                    }
                },
                error: function() {
                    alert('Erro ao carregar clínicas.');
                }
            });
        }

        // Abrir modal de agendamento
        $('#btnScheduleExam').on('click', function() {
            loadDismissedWorkers();
            loadActiveClinics();
            $('#scheduleExamForm')[0].reset();
            $('#workerInfo').hide();
            $('#scheduleExamModal').modal('show');
        });

        // Quando selecionar funcionário
        $('#examWorker').on('change', function() {
            const option = $(this).find('option:selected');
            const workerId = option.val();
            
            if (workerId) {
                $('#selectedWorkerName').text(option.data('name'));
                $('#selectedWorkerEmail').text(option.data('email'));
                $('#selectedWorkerDepartment').text(option.data('department'));
                $('#selectedWorkerPosition').text(option.data('position'));
                $('#selectedLayoffDate').text(option.data('layoff-date'));
                $('#workerInfo').show();
            } else {
                $('#workerInfo').hide();
            }
        });

        // Salvar agendamento
        $('#scheduleExamForm').on('submit', function(e) {
            e.preventDefault();
            
            const formData = $(this).serialize();

            $.ajax({
                url: '{{ route("exams.dismissals.store") }}',
                method: 'POST',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        $('#scheduleExamModal').modal('hide');
                        alert(response.message);
                        loadExams();
                    } else {
                        alert('Erro: ' + (response.message || 'Não foi possível agendar o exame.'));
                    }
                },
                error: function(xhr) {
                    const message = xhr.responseJSON?.message || 'Erro ao agendar exame.';
                    if (xhr.responseJSON?.errors) {
                        const errors = Object.values(xhr.responseJSON.errors).flat().join('\n');
                        alert('Erro de validação:\n' + errors);
                    } else {
                        alert('Erro: ' + message);
                    }
                }
            });
        });

        // Atualizar status
        $(document).on('click', '.btn-update-status', function() {
            currentExamId = $(this).data('exam-id');
            $('#updateStatusForm')[0].reset();
            $('#statusExamId').val(currentExamId);
            $('#examStatus').val('agendado');
            $('#cancellationReasonDiv').hide();
            $('#examResultDiv').hide();
            $('#updateStatusModal').modal('show');
        });

        // Mostrar/esconder campos baseado no status
        $('#examStatus').on('change', function() {
            const status = $(this).val();
            $('#cancellationReasonDiv').toggle(status === 'cancelado');
            $('#examResultDiv').toggle(status === 'finalizado');
            
            if (status === 'cancelado') {
                $('#cancellationReason').prop('required', true);
            } else {
                $('#cancellationReason').prop('required', false);
            }
        });

        // Salvar atualização de status
        $('#updateStatusForm').on('submit', function(e) {
            e.preventDefault();
            
            if (!currentExamId) {
                return;
            }

            const formData = $(this).serialize();

            $.ajax({
                url: '{{ route("exams.dismissals.update.status", ":id") }}'.replace(':id', currentExamId),
                method: 'PUT',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        $('#updateStatusModal').modal('hide');
                        alert(response.message);
                        loadExams();
                    } else {
                        alert('Erro: ' + (response.message || 'Não foi possível atualizar o status.'));
                    }
                },
                error: function(xhr) {
                    const message = xhr.responseJSON?.message || 'Erro ao atualizar status.';
                    if (xhr.responseJSON?.errors) {
                        const errors = Object.values(xhr.responseJSON.errors).flat().join('\n');
                        alert('Erro de validação:\n' + errors);
                    } else {
                        alert('Erro: ' + message);
                    }
                }
            });
        });

        // Gerar PDF
        $(document).on('click', '.btn-generate-pdf', function() {
            const examId = $(this).data('exam-id');
            const url = '{{ route("exams.dismissals.pdf", ":id") }}'.replace(':id', examId);
            window.open(url, '_blank');
        });

        // Carregar exames ao iniciar
        loadExams();
    });
</script>
@endpush

