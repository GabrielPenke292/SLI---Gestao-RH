@extends('template.layout')

@push('styles')
<style>
    .proposal-card {
        border-left: 4px solid #007bff;
        margin-bottom: 1rem;
    }
    
    .proposal-card.accepted {
        border-left-color: #28a745;
    }
    
    .proposal-card.rejected {
        border-left-color: #dc3545;
    }
    
    .proposal-card.counter {
        border-left-color: #ffc107;
    }
    
    .version-badge {
        font-size: 0.85rem;
    }
</style>
@endpush

@section('content')

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-handshake me-2"></i>Negociações</h4>
                </div>
                <div class="card-body">
                    <!-- Seleção de Processo Seletivo -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="processSelect" class="form-label"><strong>Selecione o Processo Seletivo:</strong></label>
                            <select class="form-select" id="processSelect">
                                <option value="">Selecione um processo...</option>
                            </select>
                            <small class="form-text text-muted">Apenas processos com candidatos aprovados serão exibidos.</small>
                        </div>
                    </div>

                    <!-- Informações do Processo -->
                    <div id="processInfo" class="alert alert-info" style="display: none;">
                        <h6 class="mb-2"><strong>Processo:</strong> <span id="processNumber"></span></h6>
                        <p class="mb-0"><strong>Vaga:</strong> <span id="processVacancy"></span></p>
                    </div>

                    <!-- Seleção de Candidato -->
                    <div id="candidateSection" style="display: none;">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="candidateSelect" class="form-label"><strong>Selecione o Candidato Aprovado:</strong></label>
                                <select class="form-select" id="candidateSelect">
                                    <option value="">Selecione um candidato...</option>
                                </select>
                            </div>
                        </div>

                        <!-- Informações do Candidato -->
                        <div id="candidateInfo" class="card mb-4" style="display: none;">
                            <div class="card-body">
                                <h6 class="card-title"><i class="fas fa-user me-2"></i>Dados do Candidato</h6>
                                <p class="mb-1"><strong>Nome:</strong> <span id="candidateName"></span></p>
                                <p class="mb-1"><strong>E-mail:</strong> <span id="candidateEmail"></span></p>
                                <p class="mb-0"><strong>Telefone:</strong> <span id="candidatePhone"></span></p>
                            </div>
                        </div>

                        <!-- Formulário de Proposta -->
                        <div id="proposalFormSection" style="display: none;">
                            <div class="card mb-4">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0"><i class="fas fa-file-contract me-2"></i>Cadastrar Proposta</h5>
                                </div>
                                <div class="card-body">
                                    <form id="proposalForm" enctype="multipart/form-data">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="proposalSalary" class="form-label">Salário (R$)</label>
                                                <input type="number" class="form-control" id="proposalSalary" name="salary" step="0.01" min="0" placeholder="Ex: 5000.00">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="proposalContractModel" class="form-label">Modelo de Contratação</label>
                                                <select class="form-select" id="proposalContractModel" name="contract_model">
                                                    <option value="">Selecione...</option>
                                                    <option value="CLT">CLT</option>
                                                    <option value="PJ">PJ</option>
                                                    <option value="Estágio">Estágio</option>
                                                    <option value="Temporário">Temporário</option>
                                                    <option value="Autônomo">Autônomo</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="proposalWorkload" class="form-label">Carga Horária</label>
                                                <input type="text" class="form-control" id="proposalWorkload" name="workload" placeholder="Ex: 40h, 44h, 30h">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="proposalStartDate" class="form-label">Data de Início</label>
                                                <input type="date" class="form-control" id="proposalStartDate" name="start_date">
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="proposalBenefits" class="form-label">Benefícios</label>
                                            <textarea class="form-control" id="proposalBenefits" name="benefits" rows="3" placeholder="Descreva os benefícios oferecidos..."></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label for="proposalAdditionalInfo" class="form-label">Informações Adicionais</label>
                                            <textarea class="form-control" id="proposalAdditionalInfo" name="additional_info" rows="3" placeholder="Informações complementares sobre a proposta..."></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label for="proposalFile" class="form-label">Anexar PDF da Proposta</label>
                                            <input type="file" class="form-control" id="proposalFile" name="proposal_file" accept=".pdf">
                                            <small class="form-text text-muted">Apenas arquivos PDF. Tamanho máximo: 10MB.</small>
                                        </div>
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-save me-1"></i>Salvar Proposta
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Lista de Propostas -->
                        <div id="proposalsSection" style="display: none;">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0"><i class="fas fa-list me-2"></i>Propostas Cadastradas</h5>
                                </div>
                                <div class="card-body">
                                    <div id="proposalsList">
                                        <p class="text-muted text-center">Nenhuma proposta cadastrada ainda.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Recusar Proposta -->
<div class="modal fade" id="rejectProposalModal" tabindex="-1" aria-labelledby="rejectProposalModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="rejectProposalModalLabel">
                    <i class="fas fa-times-circle me-2"></i>Recusar Proposta
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="rejectionObservation" class="form-label">Observação (opcional):</label>
                    <textarea class="form-control" id="rejectionObservation" rows="4" placeholder="Digite uma observação sobre a recusa da proposta..."></textarea>
                    <small class="form-text text-muted">Máximo de 1000 caracteres.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="btnConfirmRejectProposal">
                    <i class="fas fa-times-circle me-1"></i>Confirmar Recusa
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Contraproposta -->
<div class="modal fade" id="counterProposalModal" tabindex="-1" aria-labelledby="counterProposalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="counterProposalModalLabel">
                    <i class="fas fa-exchange-alt me-2"></i>Criar Contraproposta
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    A proposta original será mantida como versão anterior para fins de auditoria.
                </div>
                <form id="counterProposalForm" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="counterSalary" class="form-label">Salário (R$)</label>
                            <input type="number" class="form-control" id="counterSalary" name="salary" step="0.01" min="0">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="counterContractModel" class="form-label">Modelo de Contratação</label>
                            <select class="form-select" id="counterContractModel" name="contract_model">
                                <option value="">Selecione...</option>
                                <option value="CLT">CLT</option>
                                <option value="PJ">PJ</option>
                                <option value="Estágio">Estágio</option>
                                <option value="Temporário">Temporário</option>
                                <option value="Autônomo">Autônomo</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="counterWorkload" class="form-label">Carga Horária</label>
                            <input type="text" class="form-control" id="counterWorkload" name="workload">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="counterStartDate" class="form-label">Data de Início</label>
                            <input type="date" class="form-control" id="counterStartDate" name="start_date">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="counterBenefits" class="form-label">Benefícios</label>
                        <textarea class="form-control" id="counterBenefits" name="benefits" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="counterAdditionalInfo" class="form-label">Informações Adicionais</label>
                        <textarea class="form-control" id="counterAdditionalInfo" name="additional_info" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="counterFile" class="form-label">Anexar PDF da Contraproposta</label>
                        <input type="file" class="form-control" id="counterFile" name="proposal_file" accept=".pdf">
                        <small class="form-text text-muted">Apenas arquivos PDF. Tamanho máximo: 10MB.</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-warning" id="btnConfirmCounterProposal">
                    <i class="fas fa-exchange-alt me-1"></i>Criar Contraproposta
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        let currentProcessId = null;
        let currentCandidateId = null;
        let currentProposalId = null;

        // Carregar processos finalizados
        function loadFinishedProcesses() {
            $.ajax({
                url: '{{ route("negotiations.finished.processes") }}',
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        const select = $('#processSelect');
                        select.html('<option value="">Selecione um processo...</option>');
                        response.data.forEach(function(process) {
                            const statusLabel = process.status === 'finalizado' ? 'Finalizado' : 
                                               process.status === 'em_andamento' ? 'Em Andamento' : 
                                               process.status === 'aguardando_aprovacao' ? 'Aguardando Aprovação' : 
                                               process.status;
                            const dateInfo = process.end_date !== '-' ? `Finalizado em ${process.end_date}` : 'Em andamento';
                            select.append(`<option value="${process.id}">${process.number} - ${process.vacancy} (${statusLabel}, ${process.approved_count} aprovado(s))</option>`);
                        });
                    }
                },
                error: function() {
                    alert('Erro ao carregar processos finalizados.');
                }
            });
        }

        // Carregar candidatos aprovados
        function loadApprovedCandidates(processId) {
            $.ajax({
                url: '{{ route("negotiations.approved.candidates", ":id") }}'.replace(':id', processId),
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        const select = $('#candidateSelect');
                        select.html('<option value="">Selecione um candidato...</option>');
                        response.data.forEach(function(candidate) {
                            select.append(`<option value="${candidate.candidate_id}">${candidate.candidate_name}</option>`);
                        });
                        $('#candidateSection').show();
                    }
                },
                error: function() {
                    alert('Erro ao carregar candidatos aprovados.');
                }
            });
        }

        // Carregar propostas
        function loadProposals(processId, candidateId) {
            $.ajax({
                url: '{{ route("negotiations.proposals", ":id") }}'.replace(':id', processId),
                method: 'GET',
                data: { candidate_id: candidateId },
                success: function(response) {
                    if (response.success) {
                        renderProposals(response.data);
                        $('#proposalsSection').show();
                    }
                },
                error: function() {
                    alert('Erro ao carregar propostas.');
                }
            });
        }

        // Renderizar propostas
        function renderProposals(proposals) {
            const container = $('#proposalsList');
            
            if (proposals.length === 0) {
                container.html('<p class="text-muted text-center">Nenhuma proposta cadastrada ainda.</p>');
                return;
            }

            let html = '';
            proposals.forEach(function(proposal) {
                const statusClass = proposal.status === 'aceita' ? 'accepted' : 
                                   proposal.status === 'recusada' ? 'rejected' : 
                                   proposal.status === 'contraproposta' ? 'counter' : '';
                
                html += `
                    <div class="card proposal-card ${statusClass} mb-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h6 class="mb-1">
                                        Proposta Versão ${proposal.version}
                                        <span class="badge bg-${getStatusColor(proposal.status)} ms-2">${getStatusLabel(proposal.status)}</span>
                                    </h6>
                                    <small class="text-muted">Criada em ${proposal.created_at} por ${proposal.created_by}</small>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Salário:</strong> R$ ${proposal.salary}</p>
                                    <p class="mb-1"><strong>Modelo:</strong> ${proposal.contract_model}</p>
                                    <p class="mb-1"><strong>Carga Horária:</strong> ${proposal.workload}</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Data de Início:</strong> ${proposal.start_date}</p>
                                    ${proposal.proposal_file_name ? `<p class="mb-1"><strong>Arquivo:</strong> <a href="/${proposal.proposal_file_path}" target="_blank"><i class="fas fa-file-pdf me-1"></i>${proposal.proposal_file_name}</a></p>` : ''}
                                </div>
                            </div>
                            ${proposal.benefits !== '-' ? `<p class="mb-1"><strong>Benefícios:</strong> ${proposal.benefits}</p>` : ''}
                            ${proposal.additional_info !== '-' ? `<p class="mb-1"><strong>Informações Adicionais:</strong> ${proposal.additional_info}</p>` : ''}
                            ${proposal.rejection_observation ? `<p class="mb-1 text-danger"><strong>Observação da Recusa:</strong> ${proposal.rejection_observation}</p>` : ''}
                            
                            ${proposal.status === 'pendente' ? `
                                <div class="mt-3">
                                    <button class="btn btn-sm btn-success btn-accept-proposal" data-proposal-id="${proposal.proposal_id}">
                                        <i class="fas fa-check me-1"></i>Aceitar
                                    </button>
                                    <button class="btn btn-sm btn-danger btn-reject-proposal" data-proposal-id="${proposal.proposal_id}">
                                        <i class="fas fa-times me-1"></i>Recusar
                                    </button>
                                    <button class="btn btn-sm btn-warning btn-counter-proposal" data-proposal-id="${proposal.proposal_id}">
                                        <i class="fas fa-exchange-alt me-1"></i>Contraproposta
                                    </button>
                                </div>
                            ` : ''}
                        </div>
                    </div>
                `;
            });
            
            container.html(html);
        }

        function getStatusColor(status) {
            const colors = {
                'pendente': 'secondary',
                'aceita': 'success',
                'recusada': 'danger',
                'contraproposta': 'warning'
            };
            return colors[status] || 'secondary';
        }

        function getStatusLabel(status) {
            const labels = {
                'pendente': 'Pendente',
                'aceita': 'Aceita',
                'recusada': 'Recusada',
                'contraproposta': 'Contraproposta'
            };
            return labels[status] || status;
        }

        // Eventos
        $('#processSelect').on('change', function() {
            const processId = $(this).val();
            currentProcessId = processId;
            
            if (!processId) {
                $('#processInfo').hide();
                $('#candidateSection').hide();
                return;
            }

            // Buscar informações do processo
            const optionText = $(this).find('option:selected').text();
            const parts = optionText.split(' - ');
            $('#processNumber').text(parts[0] || '');
            $('#processVacancy').text(parts[1] ? parts[1].split(' (')[0] : '');
            $('#processInfo').show();
            
            loadApprovedCandidates(processId);
        });

        $('#candidateSelect').on('change', function() {
            const candidateId = $(this).val();
            currentCandidateId = candidateId;
            
            if (!candidateId) {
                $('#candidateInfo').hide();
                $('#proposalFormSection').hide();
                $('#proposalsSection').hide();
                return;
            }

            // Buscar informações do candidato
            $.ajax({
                url: '{{ route("negotiations.approved.candidates", ":id") }}'.replace(':id', currentProcessId),
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        const candidate = response.data.find(c => c.candidate_id == candidateId);
                        if (candidate) {
                            $('#candidateName').text(candidate.candidate_name);
                            $('#candidateEmail').text(candidate.candidate_email);
                            $('#candidatePhone').text(candidate.candidate_phone);
                            $('#candidateInfo').show();
                        }
                    }
                }
            });
            $('#proposalFormSection').show();
            
            // Carregar propostas existentes
            if (currentProcessId) {
                loadProposals(currentProcessId, candidateId);
            }
        });

        // Salvar proposta
        $('#proposalForm').on('submit', function(e) {
            e.preventDefault();
            
            if (!currentProcessId || !currentCandidateId) {
                alert('Selecione um processo e um candidato primeiro.');
                return;
            }

            const formData = new FormData(this);
            formData.append('candidate_id', currentCandidateId);

            $.ajax({
                url: '{{ route("negotiations.proposals.store", ":id") }}'.replace(':id', currentProcessId),
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        alert('Proposta criada com sucesso!');
                        $('#proposalForm')[0].reset();
                        loadProposals(currentProcessId, currentCandidateId);
                    } else {
                        alert('Erro: ' + (response.message || 'Não foi possível criar a proposta.'));
                    }
                },
                error: function(xhr) {
                    const message = xhr.responseJSON?.message || 'Erro ao criar proposta.';
                    alert('Erro: ' + message);
                }
            });
        });

        // Aceitar proposta
        $(document).on('click', '.btn-accept-proposal', function() {
            const proposalId = $(this).data('proposal-id');
            
            if (!confirm('Tem certeza que deseja aceitar esta proposta?')) {
                return;
            }

            $.ajax({
                url: '{{ route("negotiations.proposals.accept", [":processId", ":proposalId"]) }}'
                    .replace(':processId', currentProcessId)
                    .replace(':proposalId', proposalId),
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        alert('Proposta aceita com sucesso!');
                        loadProposals(currentProcessId, currentCandidateId);
                    } else {
                        alert('Erro: ' + (response.message || 'Não foi possível aceitar a proposta.'));
                    }
                },
                error: function(xhr) {
                    const message = xhr.responseJSON?.message || 'Erro ao aceitar proposta.';
                    alert('Erro: ' + message);
                }
            });
        });

        // Recusar proposta
        $(document).on('click', '.btn-reject-proposal', function() {
            currentProposalId = $(this).data('proposal-id');
            $('#rejectionObservation').val('');
            $('#rejectProposalModal').modal('show');
        });

        $('#btnConfirmRejectProposal').on('click', function() {
            if (!currentProposalId) {
                return;
            }

            const observation = $('#rejectionObservation').val().trim();

            $.ajax({
                url: '{{ route("negotiations.proposals.reject", [":processId", ":proposalId"]) }}'
                    .replace(':processId', currentProcessId)
                    .replace(':proposalId', currentProposalId),
                method: 'POST',
                data: {
                    observation: observation,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        $('#rejectProposalModal').modal('hide');
                        alert('Proposta recusada com sucesso!');
                        loadProposals(currentProcessId, currentCandidateId);
                    } else {
                        alert('Erro: ' + (response.message || 'Não foi possível recusar a proposta.'));
                    }
                },
                error: function(xhr) {
                    const message = xhr.responseJSON?.message || 'Erro ao recusar proposta.';
                    alert('Erro: ' + message);
                }
            });
        });

        // Contraproposta
        $(document).on('click', '.btn-counter-proposal', function() {
            currentProposalId = $(this).data('proposal-id');
            
            // Buscar dados da proposta original para preencher o formulário
            $.ajax({
                url: '{{ route("negotiations.proposals", ":id") }}'.replace(':id', currentProcessId),
                method: 'GET',
                data: { candidate_id: currentCandidateId },
                success: function(response) {
                    if (response.success) {
                        const originalProposal = response.data.find(p => p.proposal_id == currentProposalId);
                        if (originalProposal) {
                            // Preencher campos com valores da proposta original
                            const salaryValue = originalProposal.salary !== '-' ? originalProposal.salary.replace(/[^\d,]/g, '').replace(',', '.') : '';
                            $('#counterSalary').val(salaryValue);
                            $('#counterContractModel').val(originalProposal.contract_model !== '-' ? originalProposal.contract_model : '');
                            $('#counterWorkload').val(originalProposal.workload !== '-' ? originalProposal.workload : '');
                            $('#counterBenefits').val(originalProposal.benefits !== '-' ? originalProposal.benefits : '');
                            $('#counterAdditionalInfo').val(originalProposal.additional_info !== '-' ? originalProposal.additional_info : '');
                        }
                    }
                    $('#counterProposalModal').modal('show');
                }
            });
        });

        $('#btnConfirmCounterProposal').on('click', function() {
            if (!currentProposalId) {
                return;
            }

            const formData = new FormData($('#counterProposalForm')[0]);

            $.ajax({
                url: '{{ route("negotiations.proposals.counter", [":processId", ":proposalId"]) }}'
                    .replace(':processId', currentProcessId)
                    .replace(':proposalId', currentProposalId),
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        $('#counterProposalModal').modal('hide');
                        alert('Contraproposta criada com sucesso!');
                        $('#counterProposalForm')[0].reset();
                        loadProposals(currentProcessId, currentCandidateId);
                    } else {
                        alert('Erro: ' + (response.message || 'Não foi possível criar a contraproposta.'));
                    }
                },
                error: function(xhr) {
                    const message = xhr.responseJSON?.message || 'Erro ao criar contraproposta.';
                    alert('Erro: ' + message);
                }
            });
        });

        // Carregar processos ao iniciar
        loadFinishedProcesses();
    });
</script>
@endpush
