@extends('template.layout')

@section('content')

<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-book me-2"></i>Conteúdos de Treinamento
                    </h4>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addContentModal">
                        <i class="fas fa-plus me-1"></i>Adicionar Conteúdo
                    </button>
                </div>
                <div class="card-body">
                    <!-- Filtros -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="searchFilter" class="form-label">Buscar</label>
                            <input type="text" class="form-control" id="searchFilter" placeholder="Título, descrição ou categoria...">
                        </div>
                        <div class="col-md-3">
                            <label for="typeFilter" class="form-label">Tipo</label>
                        <select class="form-select" id="typeFilter">
                            <option value="">Todos os tipos</option>
                            <option value="pdf">PDF</option>
                            <option value="excel">Excel</option>
                            <option value="powerpoint">PowerPoint</option>
                            <option value="video_file">Vídeo</option>
                            <option value="youtube_link">YouTube</option>
                        </select>
                        </div>
                        <div class="col-md-3">
                            <label for="categoryFilter" class="form-label">Categoria</label>
                            <select class="form-select" id="categoryFilter">
                                <option value="">Todas as categorias</option>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button class="btn btn-secondary w-100" id="clearFilters">
                                <i class="fas fa-times me-1"></i>Limpar
                            </button>
                        </div>
                    </div>

                    <!-- Tabela -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="contentsTable">
                            <thead>
                                <tr>
                                    <th>Título</th>
                                    <th>Tipo</th>
                                    <th>Categoria</th>
                                    <th>Arquivo/URL</th>
                                    <th>Duração</th>
                                    <th>Visualizações</th>
                                    <th>Status</th>
                                    <th>Criado em</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody id="contentsTableBody">
                                <tr>
                                    <td colspan="9" class="text-center">
                                        <div class="spinner-border text-primary" role="status">
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

<!-- Modal de Adicionar Conteúdo -->
<div class="modal fade" id="addContentModal" tabindex="-1" aria-labelledby="addContentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="addContentModalLabel">
                    <i class="fas fa-plus-circle me-2"></i>Adicionar Conteúdo
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addContentForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="contentTitle" class="form-label">Título <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="contentTitle" name="title" required>
                    </div>

                    <div class="mb-3">
                        <label for="contentDescription" class="form-label">Descrição</label>
                        <textarea class="form-control" id="contentDescription" name="description" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="contentType" class="form-label">Tipo de Conteúdo <span class="text-danger">*</span></label>
                        <select class="form-select" id="contentType" name="content_type" required>
                            <option value="">Selecione o tipo</option>
                            <option value="pdf">PDF</option>
                            <option value="excel">Excel</option>
                            <option value="powerpoint">PowerPoint</option>
                            <option value="video_file">Arquivo de Vídeo</option>
                            <option value="youtube_link">Link do YouTube</option>
                        </select>
                    </div>

                    <!-- Campo de arquivo (mostrado para PDF, Excel, Vídeo) -->
                    <div class="mb-3" id="fileUploadDiv" style="display: none;">
                        <label for="contentFile" class="form-label">Arquivo <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" id="contentFile" name="file">
                        <small class="form-text text-muted">
                            <span id="fileTypesHint"></span> (máx. 100MB)
                        </small>
                    </div>

                    <!-- Campo de URL do YouTube -->
                    <div class="mb-3" id="youtubeUrlDiv" style="display: none;">
                        <label for="youtubeUrl" class="form-label">URL do YouTube <span class="text-danger">*</span></label>
                        <input type="url" class="form-control" id="youtubeUrl" name="youtube_url" placeholder="https://www.youtube.com/watch?v=...">
                        <small class="form-text text-muted">Cole o link completo do vídeo do YouTube</small>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="contentCategory" class="form-label">Categoria</label>
                            <input type="text" class="form-control" id="contentCategory" name="category" placeholder="Ex: Segurança, Qualidade, etc.">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="durationMinutes" class="form-label">Duração (minutos)</label>
                            <input type="number" class="form-control" id="durationMinutes" name="duration_minutes" min="0" placeholder="Opcional">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de Editar Conteúdo -->
<div class="modal fade" id="editContentModal" tabindex="-1" aria-labelledby="editContentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="editContentModalLabel">
                    <i class="fas fa-edit me-2"></i>Editar Conteúdo
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editContentForm">
                <div class="modal-body">
                    <input type="hidden" id="editContentId">
                    
                    <div class="mb-3">
                        <label for="editContentTitle" class="form-label">Título <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="editContentTitle" name="title" required>
                    </div>

                    <div class="mb-3">
                        <label for="editContentDescription" class="form-label">Descrição</label>
                        <textarea class="form-control" id="editContentDescription" name="description" rows="3"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="editContentCategory" class="form-label">Categoria</label>
                            <input type="text" class="form-control" id="editContentCategory" name="category">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="editDurationMinutes" class="form-label">Duração (minutos)</label>
                            <input type="number" class="form-control" id="editDurationMinutes" name="duration_minutes" min="0">
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="editIsActive" name="is_active" checked>
                            <label class="form-check-label" for="editIsActive">
                                Conteúdo ativo
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-save me-1"></i>Atualizar
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
        let contents = [];

        // Carregar categorias
        function loadCategories() {
            $.ajax({
                url: '{{ route("training.contents.categories") }}',
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        const categorySelect = $('#categoryFilter');
                        categorySelect.empty().append('<option value="">Todas as categorias</option>');
                        response.data.forEach(function(category) {
                            categorySelect.append(`<option value="${category}">${category}</option>`);
                        });
                    }
                }
            });
        }

        // Carregar conteúdos
        function loadContents() {
            const search = $('#searchFilter').val();
            const contentType = $('#typeFilter').val();
            const category = $('#categoryFilter').val();

            $.ajax({
                url: '{{ route("training.contents.data") }}',
                method: 'GET',
                data: {
                    search: search,
                    content_type: contentType,
                    category: category
                },
                success: function(response) {
                    if (response.success) {
                        contents = response.data;
                        renderTable(contents);
                    }
                },
                error: function() {
                    $('#contentsTableBody').html('<tr><td colspan="9" class="text-center text-danger">Erro ao carregar conteúdos.</td></tr>');
                }
            });
        }

        // Renderizar tabela
        function renderTable(data) {
            const tbody = $('#contentsTableBody');
            
            if (data.length === 0) {
                tbody.html('<tr><td colspan="9" class="text-center text-muted">Nenhum conteúdo encontrado.</td></tr>');
                return;
            }

            let html = '';
            data.forEach(function(content) {
                const statusBadge = content.is_active 
                    ? '<span class="badge bg-success">Ativo</span>' 
                    : '<span class="badge bg-secondary">Inativo</span>';

                let fileInfo = '-';
                if (content.content_type === 'youtube_link') {
                    fileInfo = `<a href="${content.youtube_url}" target="_blank" class="text-primary">
                        <i class="fab fa-youtube me-1"></i>Ver no YouTube
                    </a>`;
                } else if (content.file_name) {
                    fileInfo = `<span class="text-muted">${content.file_name}</span>`;
                    if (content.file_size) {
                        fileInfo += ` <small class="text-muted">(${content.file_size})</small>`;
                    }
                }

                const duration = content.duration_minutes 
                    ? `${content.duration_minutes} min` 
                    : '-';

                html += `
                    <tr>
                        <td>${content.title}</td>
                        <td>
                            <i class="fas ${content.content_type_icon} me-1"></i>
                            ${content.content_type_label}
                        </td>
                        <td>${content.category || '-'}</td>
                        <td>${fileInfo}</td>
                        <td>${duration}</td>
                        <td>${content.views_count}</td>
                        <td>${statusBadge}</td>
                        <td>${content.created_at}</td>
                        <td>
                            ${content.content_type !== 'youtube_link' ? `
                                <button class="btn btn-sm btn-info btn-download" data-id="${content.training_content_id}" title="Download">
                                    <i class="fas fa-download"></i>
                                </button>
                            ` : `
                                <button class="btn btn-sm btn-info btn-view-youtube" data-url="${content.youtube_url}" title="Ver no YouTube">
                                    <i class="fab fa-youtube"></i>
                                </button>
                            `}
                            <button class="btn btn-sm btn-warning btn-edit" data-id="${content.training_content_id}" title="Editar">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger btn-delete" data-id="${content.training_content_id}" title="Excluir">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });
            
            tbody.html(html);
        }

        // Mostrar/esconder campos baseado no tipo
        $('#contentType').on('change', function() {
            const type = $(this).val();
            const fileDiv = $('#fileUploadDiv');
            const youtubeDiv = $('#youtubeUrlDiv');
            const fileInput = $('#contentFile');
            const youtubeInput = $('#youtubeUrl');

            fileDiv.hide();
            youtubeDiv.hide();
            fileInput.prop('required', false);
            youtubeInput.prop('required', false);

            if (type === 'pdf') {
                fileDiv.show();
                fileInput.prop('required', true);
                $('#fileTypesHint').text('Formatos aceitos: PDF');
                fileInput.attr('accept', '.pdf');
            } else if (type === 'excel') {
                fileDiv.show();
                fileInput.prop('required', true);
                $('#fileTypesHint').text('Formatos aceitos: XLS, XLSX');
                fileInput.attr('accept', '.xls,.xlsx');
            } else if (type === 'powerpoint') {
                fileDiv.show();
                fileInput.prop('required', true);
                $('#fileTypesHint').text('Formatos aceitos: PPT, PPTX');
                fileInput.attr('accept', '.ppt,.pptx');
            } else if (type === 'video_file') {
                fileDiv.show();
                fileInput.prop('required', true);
                $('#fileTypesHint').text('Formatos aceitos: MP4, AVI, MOV, WMV');
                fileInput.attr('accept', '.mp4,.avi,.mov,.wmv');
            } else if (type === 'youtube_link') {
                youtubeDiv.show();
                youtubeInput.prop('required', true);
            }
        });

        // Limpar filtros
        $('#clearFilters').on('click', function() {
            $('#searchFilter').val('');
            $('#typeFilter').val('');
            $('#categoryFilter').val('');
            loadContents();
        });

        // Aplicar filtros
        $('#searchFilter, #typeFilter, #categoryFilter').on('change keyup', function() {
            clearTimeout(window.filterTimeout);
            window.filterTimeout = setTimeout(function() {
                loadContents();
            }, 500);
        });

        // Salvar novo conteúdo
        $('#addContentForm').on('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

            $.ajax({
                url: '{{ route("training.contents.store") }}',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        $('#addContentModal').modal('hide');
                        $('#addContentForm')[0].reset();
                        $('#fileUploadDiv, #youtubeUrlDiv').hide();
                        alert(response.message);
                        loadContents();
                        loadCategories();
                    } else {
                        alert('Erro: ' + (response.message || 'Não foi possível criar o conteúdo.'));
                    }
                },
                error: function(xhr) {
                    const message = xhr.responseJSON?.message || 'Erro ao criar conteúdo.';
                    if (xhr.responseJSON?.errors) {
                        const errors = Object.values(xhr.responseJSON.errors).flat().join('\n');
                        alert('Erro de validação:\n' + errors);
                    } else {
                        alert('Erro: ' + message);
                    }
                }
            });
        });

        // Editar conteúdo
        $(document).on('click', '.btn-edit', function() {
            const contentId = $(this).data('id');
            
            $.ajax({
                url: '{{ route("training.contents.get", ":id") }}'.replace(':id', contentId),
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        const content = response.data;
                        $('#editContentId').val(content.training_content_id);
                        $('#editContentTitle').val(content.title);
                        $('#editContentDescription').val(content.description);
                        $('#editContentCategory').val(content.category);
                        $('#editDurationMinutes').val(content.duration_minutes);
                        $('#editIsActive').prop('checked', content.is_active);
                        $('#editContentModal').modal('show');
                    }
                }
            });
        });

        // Salvar edição
        $('#editContentForm').on('submit', function(e) {
            e.preventDefault();
            
            const contentId = $('#editContentId').val();
            const formData = {
                _token: $('meta[name="csrf-token"]').attr('content'),
                title: $('#editContentTitle').val(),
                description: $('#editContentDescription').val(),
                category: $('#editContentCategory').val(),
                duration_minutes: $('#editDurationMinutes').val(),
                is_active: $('#editIsActive').is(':checked')
            };

            $.ajax({
                url: '{{ route("training.contents.update", ":id") }}'.replace(':id', contentId),
                method: 'PUT',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        $('#editContentModal').modal('hide');
                        alert(response.message);
                        loadContents();
                        loadCategories();
                    } else {
                        alert('Erro: ' + (response.message || 'Não foi possível atualizar o conteúdo.'));
                    }
                },
                error: function(xhr) {
                    const message = xhr.responseJSON?.message || 'Erro ao atualizar conteúdo.';
                    alert('Erro: ' + message);
                }
            });
        });

        // Excluir conteúdo
        $(document).on('click', '.btn-delete', function() {
            if (!confirm('Tem certeza que deseja excluir este conteúdo?')) {
                return;
            }

            const contentId = $(this).data('id');

            $.ajax({
                url: '{{ route("training.contents.delete", ":id") }}'.replace(':id', contentId),
                method: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        loadContents();
                        loadCategories();
                    } else {
                        alert('Erro: ' + (response.message || 'Não foi possível excluir o conteúdo.'));
                    }
                },
                error: function(xhr) {
                    const message = xhr.responseJSON?.message || 'Erro ao excluir conteúdo.';
                    alert('Erro: ' + message);
                }
            });
        });

        // Download de arquivo
        $(document).on('click', '.btn-download', function() {
            const contentId = $(this).data('id');
            const url = '{{ route("training.contents.download", ":id") }}'.replace(':id', contentId);
            window.open(url, '_blank');
        });

        // Ver no YouTube
        $(document).on('click', '.btn-view-youtube', function() {
            const url = $(this).data('url');
            window.open(url, '_blank');
        });

        // Carregar dados iniciais
        loadCategories();
        loadContents();
    });
</script>
@endpush
