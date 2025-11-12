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
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fas fa-hospital me-2"></i>Clínicas Parceiras</h4>
                    <button type="button" class="btn btn-light btn-sm" id="btnAddClinic">
                        <i class="fas fa-plus me-1"></i>Adicionar Clínica
                    </button>
                </div>
                <div class="card-body">
                    <!-- Tabela de Clínicas -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="clinicsTable">
                            <thead class="table-dark">
                                <tr>
                                    <th>Razão Social</th>
                                    <th>Nome Fantasia</th>
                                    <th>CNPJ</th>
                                    <th>E-mail</th>
                                    <th>Telefone</th>
                                    <th>Cidade/UF</th>
                                    <th>Status</th>
                                    <th>Ações</th>
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

<!-- Modal de Adicionar/Editar Clínica -->
<div class="modal fade" id="clinicModal" tabindex="-1" aria-labelledby="clinicModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="clinicModalLabel">
                    <i class="fas fa-hospital me-2"></i>Adicionar Clínica
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="clinicForm">
                <div class="modal-body">
                    <input type="hidden" id="clinicId" name="clinic_id">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="corporateName" class="form-label required-field">Razão Social <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="corporateName" name="corporate_name" required maxlength="255">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="tradeName" class="form-label">Nome Fantasia</label>
                            <input type="text" class="form-control" id="tradeName" name="trade_name" maxlength="255">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="cnpj" class="form-label required-field">CNPJ <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="cnpj" name="cnpj" required maxlength="18" placeholder="00.000.000/0000-00">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">E-mail</label>
                            <input type="email" class="form-control" id="email" name="email" maxlength="255">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Telefone</label>
                            <input type="text" class="form-control" id="phone" name="phone" maxlength="20" placeholder="(00) 00000-0000">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="zipCode" class="form-label">CEP</label>
                            <input type="text" class="form-control" id="zipCode" name="zip_code" maxlength="10" placeholder="00000-000">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="address" class="form-label">Endereço</label>
                            <input type="text" class="form-control" id="address" name="address" maxlength="255">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="addressNumber" class="form-label">Número</label>
                            <input type="text" class="form-control" id="addressNumber" name="address_number" maxlength="20">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="addressComplement" class="form-label">Complemento</label>
                            <input type="text" class="form-control" id="addressComplement" name="address_complement" maxlength="100">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="neighborhood" class="form-label">Bairro</label>
                            <input type="text" class="form-control" id="neighborhood" name="neighborhood" maxlength="100">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="city" class="form-label">Cidade</label>
                            <input type="text" class="form-control" id="city" name="city" maxlength="100">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="state" class="form-label">Estado (UF)</label>
                            <select class="form-select" id="state" name="state">
                                <option value="">Selecione...</option>
                                <option value="AC">AC</option>
                                <option value="AL">AL</option>
                                <option value="AP">AP</option>
                                <option value="AM">AM</option>
                                <option value="BA">BA</option>
                                <option value="CE">CE</option>
                                <option value="DF">DF</option>
                                <option value="ES">ES</option>
                                <option value="GO">GO</option>
                                <option value="MA">MA</option>
                                <option value="MT">MT</option>
                                <option value="MS">MS</option>
                                <option value="MG">MG</option>
                                <option value="PA">PA</option>
                                <option value="PB">PB</option>
                                <option value="PR">PR</option>
                                <option value="PE">PE</option>
                                <option value="PI">PI</option>
                                <option value="RJ">RJ</option>
                                <option value="RN">RN</option>
                                <option value="RS">RS</option>
                                <option value="RO">RO</option>
                                <option value="RR">RR</option>
                                <option value="SC">SC</option>
                                <option value="SP">SP</option>
                                <option value="SE">SE</option>
                                <option value="TO">TO</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Observações</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="isActive" name="is_active" value="1" checked>
                            <label class="form-check-label" for="isActive">
                                Clínica Ativa
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Salvar
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
        let currentClinicId = null;

        // Máscaras
        $('#cnpj').on('input', function() {
            let value = $(this).val().replace(/\D/g, '');
            if (value.length <= 14) {
                value = value.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2}).*/, '$1.$2.$3/$4-$5');
                $(this).val(value);
            }
        });

        $('#phone').on('input', function() {
            let value = $(this).val().replace(/\D/g, '');
            if (value.length <= 11) {
                if (value.length <= 10) {
                    value = value.replace(/^(\d{2})(\d{4})(\d{4}).*/, '($1) $2-$3');
                } else {
                    value = value.replace(/^(\d{2})(\d{5})(\d{4}).*/, '($1) $2-$3');
                }
                $(this).val(value);
            }
        });

        $('#zipCode').on('input', function() {
            let value = $(this).val().replace(/\D/g, '');
            if (value.length <= 8) {
                value = value.replace(/^(\d{5})(\d{3}).*/, '$1-$2');
                $(this).val(value);
            }
        });

        // Carregar clínicas
        function loadClinics() {
            $.ajax({
                url: '{{ route("exams.clinics.data") }}',
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        renderTable(response.data);
                    }
                },
                error: function() {
                    $('#clinicsTable tbody').html('<tr><td colspan="8" class="text-center text-danger">Erro ao carregar clínicas.</td></tr>');
                }
            });
        }

        // Renderizar tabela
        function renderTable(clinics) {
            const tbody = $('#clinicsTable tbody');
            
            if (clinics.length === 0) {
                tbody.html('<tr><td colspan="8" class="text-center text-muted">Nenhuma clínica cadastrada.</td></tr>');
                return;
            }

            let html = '';
            clinics.forEach(function(clinic) {
                const statusBadge = clinic.is_active 
                    ? '<span class="badge bg-success">Ativa</span>' 
                    : '<span class="badge bg-secondary">Inativa</span>';
                
                html += `
                    <tr>
                        <td>${clinic.corporate_name}</td>
                        <td>${clinic.trade_name}</td>
                        <td>${clinic.cnpj}</td>
                        <td>${clinic.email}</td>
                        <td>${clinic.phone}</td>
                        <td>${clinic.city} / ${clinic.state}</td>
                        <td>${statusBadge}</td>
                        <td>
                            <button class="btn btn-sm btn-info btn-edit-clinic" data-clinic-id="${clinic.clinic_id}" title="Editar">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger btn-delete-clinic" data-clinic-id="${clinic.clinic_id}" title="Excluir">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });
            
            tbody.html(html);
        }

        // Abrir modal para adicionar
        $('#btnAddClinic').on('click', function() {
            currentClinicId = null;
            $('#clinicModalLabel').html('<i class="fas fa-hospital me-2"></i>Adicionar Clínica');
            $('#clinicForm')[0].reset();
            $('#clinicId').val('');
            $('#isActive').prop('checked', true);
            $('#clinicModal').modal('show');
        });

        // Abrir modal para editar
        $(document).on('click', '.btn-edit-clinic', function() {
            currentClinicId = $(this).data('clinic-id');
            
            $.ajax({
                url: '{{ route("exams.clinics.get", ":id") }}'.replace(':id', currentClinicId),
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        const clinic = response.data;
                        $('#clinicModalLabel').html('<i class="fas fa-hospital me-2"></i>Editar Clínica');
                        $('#clinicId').val(clinic.clinic_id);
                        $('#corporateName').val(clinic.corporate_name);
                        $('#tradeName').val(clinic.trade_name);
                        $('#cnpj').val(clinic.cnpj);
                        $('#email').val(clinic.email);
                        $('#phone').val(clinic.phone);
                        $('#address').val(clinic.address);
                        $('#addressNumber').val(clinic.address_number);
                        $('#addressComplement').val(clinic.address_complement);
                        $('#neighborhood').val(clinic.neighborhood);
                        $('#city').val(clinic.city);
                        $('#state').val(clinic.state);
                        $('#zipCode').val(clinic.zip_code);
                        $('#notes').val(clinic.notes);
                        $('#isActive').prop('checked', clinic.is_active);
                        $('#clinicModal').modal('show');
                    }
                },
                error: function() {
                    alert('Erro ao carregar dados da clínica.');
                }
            });
        });

        // Salvar clínica
        $('#clinicForm').on('submit', function(e) {
            e.preventDefault();
            
            const formData = $(this).serialize();
            const url = currentClinicId 
                ? '{{ route("exams.clinics.update", ":id") }}'.replace(':id', currentClinicId)
                : '{{ route("exams.clinics.store") }}';
            const method = currentClinicId ? 'PUT' : 'POST';

            $.ajax({
                url: url,
                method: method,
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        $('#clinicModal').modal('hide');
                        alert(response.message);
                        loadClinics();
                    } else {
                        alert('Erro: ' + (response.message || 'Não foi possível salvar a clínica.'));
                    }
                },
                error: function(xhr) {
                    const message = xhr.responseJSON?.message || 'Erro ao salvar clínica.';
                    if (xhr.responseJSON?.errors) {
                        const errors = Object.values(xhr.responseJSON.errors).flat().join('\n');
                        alert('Erro de validação:\n' + errors);
                    } else {
                        alert('Erro: ' + message);
                    }
                }
            });
        });

        // Excluir clínica
        $(document).on('click', '.btn-delete-clinic', function() {
            const clinicId = $(this).data('clinic-id');
            
            if (!confirm('Tem certeza que deseja excluir esta clínica?')) {
                return;
            }

            $.ajax({
                url: '{{ route("exams.clinics.delete", ":id") }}'.replace(':id', clinicId),
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        loadClinics();
                    } else {
                        alert('Erro: ' + (response.message || 'Não foi possível excluir a clínica.'));
                    }
                },
                error: function(xhr) {
                    const message = xhr.responseJSON?.message || 'Erro ao excluir clínica.';
                    alert('Erro: ' + message);
                }
            });
        });

        // Carregar clínicas ao iniciar
        loadClinics();
    });
</script>
@endpush
