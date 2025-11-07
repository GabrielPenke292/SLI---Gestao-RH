@extends('template.layout')

@push('styles')
<style>
    .upload-area {
        border: 2px dashed #dee2e6;
        border-radius: 0.5rem;
        padding: 3rem;
        text-align: center;
        transition: all 0.3s;
        background-color: #f8f9fa;
    }

    .upload-area:hover {
        border-color: #0d6efd;
        background-color: #e7f1ff;
    }

    .upload-area.dragover {
        border-color: #0d6efd;
        background-color: #cfe2ff;
    }

    .file-info {
        display: none;
        margin-top: 1rem;
        padding: 1rem;
        background-color: #e7f1ff;
        border-radius: 0.25rem;
    }

    .instructions-card {
        background-color: #f8f9fa;
        border-left: 4px solid #0d6efd;
    }
</style>
@endpush

@section('content')

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0"><i class="fas fa-upload me-2"></i>Upload de Base de Funcionários</h4>
                        <small class="text-white">Importe a base de funcionários em formato Excel (.xlsx, .xls) ou CSV (.csv)</small>
                    </div>
                    <a href="{{ route('employees.board') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>Voltar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Instruções -->
                    <div class="card instructions-card mb-4">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="fas fa-info-circle me-2 text-primary"></i>Instruções
                            </h5>
                            <p class="mb-2">A planilha deve conter as seguintes colunas (nesta ordem):</p>
                            <ol class="mb-0">
                                <li><strong>Nome</strong> - Nome completo do funcionário (obrigatório)</li>
                                <li><strong>Email</strong> - Email válido (obrigatório, único)</li>
                                <li><strong>CPF</strong> - CPF do funcionário (obrigatório)</li>
                                <li><strong>RG</strong> - RG do funcionário (opcional)</li>
                                <li><strong>Data de Nascimento</strong> - Formato: DD/MM/AAAA ou AAAA-MM-DD (obrigatório)</li>
                                <li><strong>Data de Admissão</strong> - Formato: DD/MM/AAAA ou AAAA-MM-DD (obrigatório)</li>
                                <li><strong>Salário</strong> - Valor numérico (obrigatório)</li>
                                <li><strong>Status</strong> - "Ativo" ou "Inativo" (obrigatório)</li>
                                <li><strong>Departamento</strong> - Nome do departamento (deve existir no sistema)</li>
                                <li><strong>Cargo</strong> - Nome do(s) cargo(s), separados por vírgula se múltiplos (deve existir no sistema)</li>
                            </ol>
                        </div>
                    </div>

                    <!-- Formulário de Upload -->
                    <form action="{{ route('employees.upload.process') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                        @csrf
                        
                        <div class="upload-area" id="uploadArea">
                            <i class="fas fa-cloud-upload-alt fa-3x text-primary mb-3"></i>
                            <h5>Arraste o arquivo aqui ou clique para selecionar</h5>
                            <p class="text-muted mb-3">Formatos aceitos: .xlsx, .xls, .csv (máximo 10MB)</p>
                            <input type="file" 
                                   class="form-control d-none" 
                                   id="fileInput" 
                                   name="file" 
                                   accept=".xlsx,.xls,.csv" 
                                   required>
                            <button type="button" class="btn btn-primary" onclick="document.getElementById('fileInput').click();">
                                <i class="fas fa-folder-open me-2"></i>Selecionar Arquivo
                            </button>
                        </div>

                        <div class="file-info" id="fileInfo">
                            <i class="fas fa-file-excel me-2"></i>
                            <span id="fileName"></span>
                            <span class="text-muted ms-2" id="fileSize"></span>
                            <button type="button" class="btn btn-sm btn-danger ms-2" onclick="clearFile()">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                        <div class="mt-4 d-flex justify-content-end gap-2">
                            <a href="{{ route('employees.board') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary" id="submitBtn" disabled>
                                <i class="fas fa-upload me-2"></i>Processar Arquivo
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const uploadArea = document.getElementById('uploadArea');
    const fileInput = document.getElementById('fileInput');
    const fileInfo = document.getElementById('fileInfo');
    const fileName = document.getElementById('fileName');
    const fileSize = document.getElementById('fileSize');
    const submitBtn = document.getElementById('submitBtn');
    const uploadForm = document.getElementById('uploadForm');

    // Drag and drop
    uploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadArea.classList.add('dragover');
    });

    uploadArea.addEventListener('dragleave', () => {
        uploadArea.classList.remove('dragover');
    });

    uploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadArea.classList.remove('dragover');
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            handleFile(files[0]);
        }
    });

    // Click na área de upload
    uploadArea.addEventListener('click', () => {
        fileInput.click();
    });

    // Seleção de arquivo
    fileInput.addEventListener('change', (e) => {
        if (e.target.files.length > 0) {
            handleFile(e.target.files[0]);
        }
    });

    function handleFile(file) {
        // Validar tipo de arquivo
        const validTypes = [
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // .xlsx
            'application/vnd.ms-excel', // .xls
            'text/csv', // .csv
            'application/csv'
        ];

        if (!validTypes.includes(file.type) && !file.name.match(/\.(xlsx|xls|csv)$/i)) {
            alert('Por favor, selecione um arquivo Excel (.xlsx, .xls) ou CSV (.csv)');
            return;
        }

        // Validar tamanho (10MB)
        if (file.size > 10 * 1024 * 1024) {
            alert('O arquivo não pode ser maior que 10MB');
            return;
        }

        // Atualizar input
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(file);
        fileInput.files = dataTransfer.files;

        // Mostrar informações do arquivo
        fileName.textContent = file.name;
        fileSize.textContent = formatFileSize(file.size);
        fileInfo.style.display = 'block';
        submitBtn.disabled = false;
    }

    function clearFile() {
        fileInput.value = '';
        fileInfo.style.display = 'none';
        submitBtn.disabled = true;
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
    }

    // Submissão do formulário
    uploadForm.addEventListener('submit', function(e) {
        if (!fileInput.files.length) {
            e.preventDefault();
            alert('Por favor, selecione um arquivo');
            return;
        }

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processando...';
    });
</script>
@endpush
