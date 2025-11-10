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
            </div>
        </div>
    </div>
</div>

@endsection

