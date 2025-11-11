@extends('template.layout')

@push('styles')
<style>
    .info-card {
        border-left: 4px solid #0d6efd;
    }
</style>
@endpush

@section('content')

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0"><i class="fas fa-user-tie me-2"></i>Perfil do Candidato</h4>
                <div>
                    <a href="{{ route('candidates.edit', $candidate->candidate_id) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-2"></i>Editar
                    </a>
                    <a href="{{ route('candidates.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Voltar
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="row">
                <!-- Informações Pessoais -->
                <div class="col-md-6 mb-4">
                    <div class="card info-card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-user me-2"></i>Informações Pessoais</h5>
                        </div>
                        <div class="card-body">
                            <p><strong>Nome:</strong> {{ $candidate->candidate_name }}</p>
                            @if($candidate->candidate_email)
                                <p><strong>E-mail:</strong> <a href="mailto:{{ $candidate->candidate_email }}">{{ $candidate->candidate_email }}</a></p>
                            @endif
                            @if($candidate->candidate_phone)
                                <p><strong>Telefone:</strong> {{ $candidate->candidate_phone }}</p>
                            @endif
                            @if($candidate->candidate_document)
                                <p><strong>CPF:</strong> {{ $candidate->candidate_document }}</p>
                            @endif
                            @if($candidate->candidate_rg)
                                <p><strong>RG:</strong> {{ $candidate->candidate_rg }}</p>
                            @endif
                            @if($candidate->candidate_birth_date)
                                <p><strong>Data de Nascimento:</strong> {{ $candidate->candidate_birth_date->format('d/m/Y') }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Endereço -->
                <div class="col-md-6 mb-4">
                    <div class="card info-card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-map-marker-alt me-2"></i>Endereço</h5>
                        </div>
                        <div class="card-body">
                            @if($candidate->candidate_address || $candidate->candidate_city || $candidate->candidate_state)
                                @if($candidate->candidate_address)
                                    <p><strong>Endereço:</strong> {{ $candidate->candidate_address }}</p>
                                @endif
                                @if($candidate->candidate_zipcode)
                                    <p><strong>CEP:</strong> {{ $candidate->candidate_zipcode }}</p>
                                @endif
                                @if($candidate->candidate_city || $candidate->candidate_state)
                                    <p><strong>Cidade/UF:</strong> 
                                        {{ $candidate->candidate_city ?? '' }}
                                        @if($candidate->candidate_city && $candidate->candidate_state) / @endif
                                        {{ $candidate->candidate_state ?? '' }}
                                    </p>
                                @endif
                            @else
                                <p class="text-muted">Nenhum endereço cadastrado.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Experiência Profissional -->
                @if($candidate->candidate_experience)
                <div class="col-md-12 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0"><i class="fas fa-briefcase me-2"></i>Experiência Profissional</h5>
                        </div>
                        <div class="card-body">
                            <div class="whitespace-pre-line">{{ $candidate->candidate_experience }}</div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Formação Acadêmica -->
                @if($candidate->candidate_education)
                <div class="col-md-12 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0"><i class="fas fa-graduation-cap me-2"></i>Formação Acadêmica</h5>
                        </div>
                        <div class="card-body">
                            <div class="whitespace-pre-line">{{ $candidate->candidate_education }}</div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Habilidades -->
                @if($candidate->candidate_skills)
                <div class="col-md-12 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0"><i class="fas fa-star me-2"></i>Habilidades e Competências</h5>
                        </div>
                        <div class="card-body">
                            <div class="whitespace-pre-line">{{ $candidate->candidate_skills }}</div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Currículo PDF -->
                @if(!empty($candidate->candidate_resume_pdf))
                <div class="col-md-12 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-danger text-white">
                            <h5 class="mb-0"><i class="fas fa-file-pdf me-2"></i>Currículo</h5>
                        </div>
                        <div class="card-body text-center">
                            <a href="{{ $candidate->resume_pdf_url }}" target="_blank" class="btn btn-danger btn-lg">
                                <i class="fas fa-file-pdf me-2"></i>Visualizar Currículo em PDF
                            </a>
                        </div>
                    </div>
                </div>
                @else
                <div class="col-md-12 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0"><i class="fas fa-file-pdf me-2"></i>Currículo</h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted mb-0">Nenhum currículo em PDF cadastrado.</p>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Observações -->
                @if($candidate->candidate_notes)
                <div class="col-md-12 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-dark text-white">
                            <h5 class="mb-0"><i class="fas fa-sticky-note me-2"></i>Observações</h5>
                        </div>
                        <div class="card-body">
                            <div class="whitespace-pre-line">{{ $candidate->candidate_notes }}</div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Timeline de Processos Seletivos -->
                @if($candidate->selectionProcesses->count() > 0)
                <div class="col-md-12 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-history me-2"></i>Timeline de Processos Seletivos</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="processSelect" class="form-label">Selecione o Processo Seletivo:</label>
                                <select class="form-select" id="processSelect">
                                    <option value="">Selecione um processo...</option>
                                    @foreach($candidate->selectionProcesses as $process)
                                        <option value="{{ $process->selection_process_id }}">
                                            {{ $process->process_number }} - {{ $process->vacancy->vacancy_title ?? 'N/A' }} 
                                            ({{ ucfirst(str_replace('_', ' ', $process->status)) }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div id="timelineContainer" class="mt-4">
                                <p class="text-muted text-center">Selecione um processo seletivo para visualizar a timeline.</p>
                            </div>
                            
                            <!-- Tabs para separar Atividades e Interações -->
                            <div id="timelineTabsContainer" style="display: none;">
                                <ul class="nav nav-tabs mb-3" id="timelineTabs" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="activities-tab" data-bs-toggle="tab" data-bs-target="#activities" type="button" role="tab" aria-controls="activities" aria-selected="true">
                                            <i class="fas fa-tasks me-2"></i>Atividades
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="interactions-tab" data-bs-toggle="tab" data-bs-target="#interactions" type="button" role="tab" aria-controls="interactions" aria-selected="false">
                                            <i class="fas fa-comments me-2"></i>Interações
                                        </button>
                                    </li>
                                </ul>
                                <div class="tab-content" id="timelineTabContent">
                                    <div class="tab-pane fade show active" id="activities" role="tabpanel" aria-labelledby="activities-tab">
                                        <div id="activitiesTimeline" class="timeline">
                                            <!-- Atividades serão renderizadas aqui -->
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="interactions" role="tabpanel" aria-labelledby="interactions-tab">
                                        <div id="interactionsTimeline" class="timeline">
                                            <!-- Interações serão renderizadas aqui -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .timeline {
        position: relative;
        padding-left: 30px;
    }
    
    .timeline::before {
        content: '';
        position: absolute;
        left: 15px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #dee2e6;
    }
    
    .timeline-item {
        position: relative;
        margin-bottom: 30px;
        padding-left: 40px;
    }
    
    .timeline-item::before {
        content: '';
        position: absolute;
        left: -7px;
        top: 5px;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background: #fff;
        border: 3px solid;
        z-index: 1;
    }
    
    .timeline-item.completed::before {
        border-color: #28a745;
    }
    
    .timeline-item.current::before {
        border-color: #ffc107;
        box-shadow: 0 0 0 4px rgba(255, 193, 7, 0.2);
        animation: pulse 2s infinite;
    }
    
    .timeline-item.danger::before {
        border-color: #dc3545;
    }
    
    @keyframes pulse {
        0%, 100% {
            box-shadow: 0 0 0 4px rgba(255, 193, 7, 0.2);
        }
        50% {
            box-shadow: 0 0 0 8px rgba(255, 193, 7, 0);
        }
    }
    
    .timeline-content {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
        border-left: 4px solid;
    }
    
    .timeline-content.primary {
        border-left-color: #0d6efd;
    }
    
    .timeline-content.success {
        border-left-color: #28a745;
    }
    
    .timeline-content.warning {
        border-left-color: #ffc107;
    }
    
    .timeline-content.danger {
        border-left-color: #dc3545;
    }
    
    .timeline-content.info {
        border-left-color: #17a2b8;
    }
    
    .timeline-content.secondary {
        border-left-color: #6c757d;
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        const candidateId = {{ $candidate->candidate_id ?? 0 }};
        
        $('#processSelect').on('change', function() {
            const processId = $(this).val();
            const container = $('#timelineContainer');
            
            if (!processId) {
                container.html('<p class="text-muted text-center">Selecione um processo seletivo para visualizar a timeline.</p>');
                $('#timelineTabsContainer').hide();
                return;
            }
            
            container.html('<div class="text-center"><div class="spinner-border" role="status"><span class="visually-hidden">Carregando...</span></div></div>');
            
            $.ajax({
                url: '{{ route("candidates.timeline", ":id") }}'.replace(':id', candidateId),
                method: 'GET',
                data: {
                    process_id: processId
                },
                success: function(response) {
                    if (response.success) {
                        // Mostrar informações do processo
                        let processInfo = `
                            <div class="mb-3">
                                <h6><strong>Processo:</strong> ${response.process.number}</h6>
                                <p class="mb-0"><strong>Vaga:</strong> ${response.process.vacancy}</p>
                            </div>
                        `;
                        
                        // Renderizar atividades
                        let activitiesHtml = '';
                        if (response.activities && response.activities.length > 0) {
                            response.activities.forEach(function(item) {
                                activitiesHtml += `
                                    <div class="timeline-item ${item.status}">
                                        <div class="timeline-content ${item.color}">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="mb-0">
                                                    <i class="fas ${item.icon} me-2"></i>${item.title}
                                                </h6>
                                                <small class="text-muted">${item.date}</small>
                                            </div>
                                            <p class="mb-0">${item.description}</p>
                                        </div>
                                    </div>
                                `;
                            });
                        } else {
                            activitiesHtml = '<p class="text-muted text-center">Nenhuma atividade registrada ainda.</p>';
                        }
                        
                        // Renderizar interações
                        let interactionsHtml = '';
                        if (response.interactions && response.interactions.length > 0) {
                            response.interactions.forEach(function(item) {
                                interactionsHtml += `
                                    <div class="timeline-item ${item.status}">
                                        <div class="timeline-content ${item.color}">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="mb-0">
                                                    <i class="fas ${item.icon} me-2"></i>${item.title}
                                                </h6>
                                                <small class="text-muted">${item.date}</small>
                                            </div>
                                            <div class="mb-0">${item.description}</div>
                                        </div>
                                    </div>
                                `;
                            });
                        } else {
                            interactionsHtml = '<p class="text-muted text-center">Nenhuma interação registrada ainda.</p>';
                        }
                        
                        // Atualizar contadores nas abas
                        let activitiesCount = response.activities ? response.activities.length : 0;
                        let interactionsCount = response.interactions ? response.interactions.length : 0;
                        
                        $('#activities-tab').html(`<i class="fas fa-tasks me-2"></i>Atividades ${activitiesCount > 0 ? '<span class="badge bg-primary">' + activitiesCount + '</span>' : ''}`);
                        $('#interactions-tab').html(`<i class="fas fa-comments me-2"></i>Interações ${interactionsCount > 0 ? '<span class="badge bg-primary">' + interactionsCount + '</span>' : ''}`);
                        
                        // Atualizar conteúdo
                        container.html(processInfo);
                        $('#activitiesTimeline').html(activitiesHtml);
                        $('#interactionsTimeline').html(interactionsHtml);
                        
                        // Mostrar as abas
                        $('#timelineTabsContainer').show();
                    } else {
                        container.html('<div class="alert alert-danger">' + (response.message || 'Erro ao carregar timeline.') + '</div>');
                        $('#timelineTabsContainer').hide();
                    }
                },
                error: function(xhr) {
                    const message = xhr.responseJSON?.message || 'Erro ao carregar timeline.';
                    container.html('<div class="alert alert-danger">' + message + '</div>');
                }
            });
        });
    });
</script>
@endpush

