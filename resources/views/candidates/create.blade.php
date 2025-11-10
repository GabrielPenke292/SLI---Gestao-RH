@extends('template.layout')

@push('styles')
<style>
    .form-label {
        font-weight: 500;
        margin-bottom: 0.5rem;
    }

    .required-field::after {
        content: " *";
        color: #dc3545;
    }

    .alert {
        border-radius: 0.375rem;
    }
</style>
@endpush

@section('content')

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                            <h4 class="mb-0"><i class="fas fa-user-plus me-2"></i>Cadastrar Novo Candidato</h4>
                            <a href="{{ route('candidates.index') }}" class="btn btn-light btn-sm">
                                <i class="fas fa-arrow-left me-1"></i>Voltar
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-12 mt-3">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            @if(session('error'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            @if($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="fas fa-exclamation-circle me-2"></i>
                                    <strong>Erro!</strong> Por favor, corrija os seguintes erros:
                                    <ul class="mb-0 mt-2">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            <form action="{{ route('candidates.store') }}" method="POST" enctype="multipart/form-data" id="candidateForm">
                                @csrf

                                <h5 class="mb-3"><i class="fas fa-user me-2"></i>Informações Pessoais</h5>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="candidate_name" class="form-label required-field">Nome Completo</label>
                                        <input type="text" class="form-control @error('candidate_name') is-invalid @enderror" 
                                               id="candidate_name" name="candidate_name" 
                                               value="{{ old('candidate_name') }}" 
                                               placeholder="Nome completo do candidato" 
                                               maxlength="100" required>
                                        @error('candidate_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="candidate_email" class="form-label">E-mail</label>
                                        <input type="email" class="form-control @error('candidate_email') is-invalid @enderror" 
                                               id="candidate_email" name="candidate_email" 
                                               value="{{ old('candidate_email') }}" 
                                               placeholder="email@exemplo.com" 
                                               maxlength="100">
                                        @error('candidate_email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label for="candidate_phone" class="form-label">Telefone</label>
                                        <input type="text" class="form-control @error('candidate_phone') is-invalid @enderror" 
                                               id="candidate_phone" name="candidate_phone" 
                                               value="{{ old('candidate_phone') }}" 
                                               placeholder="(00) 00000-0000" 
                                               maxlength="20">
                                        @error('candidate_phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label for="candidate_document" class="form-label">CPF</label>
                                        <input type="text" class="form-control @error('candidate_document') is-invalid @enderror" 
                                               id="candidate_document" name="candidate_document" 
                                               value="{{ old('candidate_document') }}" 
                                               placeholder="000.000.000-00" 
                                               maxlength="14">
                                        @error('candidate_document')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label for="candidate_birth_date" class="form-label">Data de Nascimento</label>
                                        <input type="date" class="form-control @error('candidate_birth_date') is-invalid @enderror" 
                                               id="candidate_birth_date" name="candidate_birth_date" 
                                               value="{{ old('candidate_birth_date') }}">
                                        @error('candidate_birth_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label for="candidate_rg" class="form-label">RG</label>
                                        <input type="text" class="form-control @error('candidate_rg') is-invalid @enderror" 
                                               id="candidate_rg" name="candidate_rg" 
                                               value="{{ old('candidate_rg') }}" 
                                               placeholder="00.000.000-0" 
                                               maxlength="20">
                                        @error('candidate_rg')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <h5 class="mb-3 mt-4"><i class="fas fa-map-marker-alt me-2"></i>Endereço</h5>
                                <div class="row">
                                    <div class="col-md-8 mb-3">
                                        <label for="candidate_address" class="form-label">Endereço</label>
                                        <input type="text" class="form-control @error('candidate_address') is-invalid @enderror" 
                                               id="candidate_address" name="candidate_address" 
                                               value="{{ old('candidate_address') }}" 
                                               placeholder="Rua, número, complemento" 
                                               maxlength="255">
                                        @error('candidate_address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label for="candidate_zipcode" class="form-label">CEP</label>
                                        <input type="text" class="form-control @error('candidate_zipcode') is-invalid @enderror" 
                                               id="candidate_zipcode" name="candidate_zipcode" 
                                               value="{{ old('candidate_zipcode') }}" 
                                               placeholder="00000-000" 
                                               maxlength="10">
                                        @error('candidate_zipcode')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="candidate_city" class="form-label">Cidade</label>
                                        <input type="text" class="form-control @error('candidate_city') is-invalid @enderror" 
                                               id="candidate_city" name="candidate_city" 
                                               value="{{ old('candidate_city') }}" 
                                               placeholder="Cidade" 
                                               maxlength="100">
                                        @error('candidate_city')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="candidate_state" class="form-label">Estado (UF)</label>
                                        <input type="text" class="form-control @error('candidate_state') is-invalid @enderror" 
                                               id="candidate_state" name="candidate_state" 
                                               value="{{ old('candidate_state') }}" 
                                               placeholder="SP" 
                                               maxlength="2">
                                        @error('candidate_state')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <h5 class="mb-3 mt-4"><i class="fas fa-briefcase me-2"></i>Experiência e Formação</h5>
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="candidate_experience" class="form-label">Experiência Profissional</label>
                                        <textarea class="form-control @error('candidate_experience') is-invalid @enderror" 
                                                  id="candidate_experience" name="candidate_experience" 
                                                  rows="4" 
                                                  placeholder="Descreva a experiência profissional do candidato...">{{ old('candidate_experience') }}</textarea>
                                        @error('candidate_experience')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <label for="candidate_education" class="form-label">Formação Acadêmica</label>
                                        <textarea class="form-control @error('candidate_education') is-invalid @enderror" 
                                                  id="candidate_education" name="candidate_education" 
                                                  rows="4" 
                                                  placeholder="Descreva a formação acadêmica do candidato...">{{ old('candidate_education') }}</textarea>
                                        @error('candidate_education')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <label for="candidate_skills" class="form-label">Habilidades e Competências</label>
                                        <textarea class="form-control @error('candidate_skills') is-invalid @enderror" 
                                                  id="candidate_skills" name="candidate_skills" 
                                                  rows="3" 
                                                  placeholder="Liste as habilidades e competências do candidato...">{{ old('candidate_skills') }}</textarea>
                                        @error('candidate_skills')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <h5 class="mb-3 mt-4"><i class="fas fa-file-pdf me-2"></i>Currículo</h5>
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="candidate_resume_pdf" class="form-label">Anexar Currículo em PDF</label>
                                        <input type="file" 
                                               class="form-control @error('candidate_resume_pdf') is-invalid @enderror" 
                                               id="candidate_resume_pdf" 
                                               name="candidate_resume_pdf" 
                                               accept=".pdf">
                                        <small class="form-text text-muted">Tamanho máximo: 10MB. Apenas arquivos PDF.</small>
                                        @error('candidate_resume_pdf')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <label for="candidate_resume_text" class="form-label">Texto do Currículo (Cole aqui o conteúdo completo)</label>
                                        <textarea class="form-control @error('candidate_resume_text') is-invalid @enderror" 
                                                  id="candidate_resume_text" name="candidate_resume_text" 
                                                  rows="10" 
                                                  placeholder="Cole aqui todo o texto do currículo para permitir buscas mais eficientes...">{{ old('candidate_resume_text') }}</textarea>
                                        <small class="form-text text-muted">Cole todo o texto do currículo aqui. Isso permitirá buscas mais eficientes no sistema.</small>
                                        @error('candidate_resume_text')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <h5 class="mb-3 mt-4"><i class="fas fa-sticky-note me-2"></i>Observações</h5>
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="candidate_notes" class="form-label">Observações Adicionais</label>
                                        <textarea class="form-control @error('candidate_notes') is-invalid @enderror" 
                                                  id="candidate_notes" name="candidate_notes" 
                                                  rows="4" 
                                                  placeholder="Adicione observações sobre o candidato...">{{ old('candidate_notes') }}</textarea>
                                        @error('candidate_notes')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-12 d-flex justify-content-end gap-2">
                                        <a href="{{ route('candidates.index') }}" class="btn btn-secondary">
                                            <i class="fas fa-times me-2"></i>Cancelar
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>Salvar Candidato
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

