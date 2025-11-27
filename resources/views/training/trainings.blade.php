@extends('template.layout')

@section('content')

<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-university me-2"></i>Turmas de Treinamento
                    </h4>
                    <div class="d-flex gap-2">
                        <input type="text" class="form-control" id="searchFilter" placeholder="Buscar turmas..." style="width: 300px;">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addClassModal">
                            <i class="fas fa-plus me-1"></i>Nova Turma
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Título</th>
                                    <th>Instrutor</th>
                                    <th>Período</th>
                                    <th>Status</th>
                                    <th>Tópicos</th>
                                    <th>Participantes</th>
                                    <th>Criado em</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody id="classesTableBody">
                                <tr>
                                    <td colspan="8" class="text-center">
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

<!-- Modal de Adicionar/Editar Turma -->
<div class="modal fade" id="addClassModal" tabindex="-1" aria-labelledby="addClassModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="addClassModalLabel">
                    <i class="fas fa-plus-circle me-2"></i>Nova Turma
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="classForm">
                <div class="modal-body">
                    <input type="hidden" id="classId">
                    
                    <div class="mb-3">
                        <label for="classTitle" class="form-label">Título <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="classTitle" name="title" required>
                    </div>

                    <div class="mb-3">
                        <label for="classDescription" class="form-label">Descrição</label>
                        <textarea class="form-control" id="classDescription" name="description" rows="3"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="classStartDate" class="form-label">Data de Início</label>
                            <input type="date" class="form-control" id="classStartDate" name="start_date">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="classEndDate" class="form-label">Data de Término</label>
                            <input type="date" class="form-control" id="classEndDate" name="end_date">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="classStatus" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="classStatus" name="status" required>
                                <option value="planejado">Planejado</option>
                                <option value="em_andamento">Em Andamento</option>
                                <option value="concluido">Concluído</option>
                                <option value="cancelado">Cancelado</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="classMaxParticipants" class="form-label">Máximo de Participantes</label>
                            <input type="number" class="form-control" id="classMaxParticipants" name="max_participants" min="1">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="classInstructor" class="form-label">Instrutor</label>
                        <input type="text" class="form-control" id="classInstructor" name="instructor">
                    </div>

                    <div class="mb-3">
                        <label for="classNotes" class="form-label">Observações</label>
                        <textarea class="form-control" id="classNotes" name="notes" rows="3"></textarea>
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

<!-- Modal de Gerenciar Tópicos -->
<div class="modal fade" id="topicsModal" tabindex="-1" aria-labelledby="topicsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="topicsModalLabel">
                    <i class="fas fa-list me-2"></i>Gerenciar Tópicos
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="currentClassId">
                
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0">Tópicos da Turma</h6>
                    <button class="btn btn-sm btn-primary" id="addTopicBtn">
                        <i class="fas fa-plus me-1"></i>Adicionar Tópico
                    </button>
                </div>

                <div id="topicsList" class="mb-4">
                    <!-- Tópicos serão carregados aqui -->
                </div>

                <!-- Formulário de Tópico (oculto inicialmente) -->
                <div id="topicFormDiv" style="display: none;" class="card mb-3">
                    <div class="card-body">
                        <h6 class="card-title">Novo Tópico</h6>
                        <form id="topicForm">
                            <input type="hidden" id="topicId">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="topicTitle" class="form-label">Título <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="topicTitle" name="title" required>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="topicOrder" class="form-label">Ordem</label>
                                    <input type="number" class="form-control" id="topicOrder" name="order" min="0" value="0">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="topicDuration" class="form-label">Duração (min)</label>
                                    <input type="number" class="form-control" id="topicDuration" name="duration_minutes" min="0">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="topicDescription" class="form-label">Descrição</label>
                                <textarea class="form-control" id="topicDescription" name="description" rows="2"></textarea>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-sm btn-primary">
                                    <i class="fas fa-save me-1"></i>Salvar
                                </button>
                                <button type="button" class="btn btn-sm btn-secondary" id="cancelTopicBtn">
                                    Cancelar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Adicionar Conteúdo ao Tópico -->
<div class="modal fade" id="addContentToTopicModal" tabindex="-1" aria-labelledby="addContentToTopicModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="addContentToTopicModalLabel">
                    <i class="fas fa-book me-2"></i>Adicionar Conteúdo ao Tópico
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addContentToTopicForm">
                <div class="modal-body">
                    <input type="hidden" id="contentTopicId">
                    
                    <div class="mb-3">
                        <label for="contentSelect" class="form-label">Selecione o Conteúdo <span class="text-danger">*</span></label>
                        <select class="form-select" id="contentSelect" name="training_content_id" required>
                            <option value="">Carregando...</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="contentOrder" class="form-label">Ordem</label>
                        <input type="number" class="form-control" id="contentOrder" name="order" min="0" value="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-plus me-1"></i>Adicionar
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
        let currentClassId = null;
        let availableContents = [];

        // Carregar turmas
        function loadClasses() {
            const search = $('#searchFilter').val();

            $.ajax({
                url: '{{ route("training.classes.data") }}',
                method: 'GET',
                data: { search: search },
                success: function(response) {
                    if (response.success) {
                        renderClasses(response.data);
                    }
                },
                error: function() {
                    $('#classesTableBody').html('<tr><td colspan="8" class="text-center text-danger">Erro ao carregar turmas.</td></tr>');
                }
            });
        }

        // Renderizar turmas
        function renderClasses(classes) {
            const tbody = $('#classesTableBody');
            
            if (classes.length === 0) {
                tbody.html('<tr><td colspan="8" class="text-center text-muted">Nenhuma turma encontrada.</td></tr>');
                return;
            }

            let html = '';
            classes.forEach(function(classItem) {
                html += `
                    <tr>
                        <td><strong>${classItem.title}</strong></td>
                        <td>${classItem.instructor || '-'}</td>
                        <td>
                            ${classItem.start_date !== '-' ? classItem.start_date : ''}
                            ${classItem.end_date !== '-' ? ' até ' + classItem.end_date : ''}
                        </td>
                        <td><span class="badge bg-${classItem.status_color}">${classItem.status_label}</span></td>
                        <td><span class="badge bg-info">${classItem.topics_count} tópicos</span></td>
                        <td>${classItem.max_participants || '-'}</td>
                        <td>${classItem.created_at}</td>
                        <td>
                            <button class="btn btn-sm btn-info btn-manage-topics" data-id="${classItem.training_class_id}" title="Gerenciar Tópicos">
                                <i class="fas fa-list"></i>
                            </button>
                            <button class="btn btn-sm btn-warning btn-edit-class" data-id="${classItem.training_class_id}" title="Editar">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger btn-delete-class" data-id="${classItem.training_class_id}" title="Excluir">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });
            
            tbody.html(html);
        }

        // Buscar filtro
        $('#searchFilter').on('keyup', function() {
            clearTimeout(window.searchTimeout);
            window.searchTimeout = setTimeout(function() {
                loadClasses();
            }, 500);
        });

        // Salvar turma
        $('#classForm').on('submit', function(e) {
            e.preventDefault();
            
            const classId = $('#classId').val();
            const url = classId 
                ? '{{ route("training.classes.update", ":id") }}'.replace(':id', classId)
                : '{{ route("training.classes.store") }}';
            const method = classId ? 'PUT' : 'POST';

            const formData = {
                _token: $('meta[name="csrf-token"]').attr('content'),
                title: $('#classTitle').val(),
                description: $('#classDescription').val(),
                start_date: $('#classStartDate').val(),
                end_date: $('#classEndDate').val(),
                status: $('#classStatus').val(),
                max_participants: $('#classMaxParticipants').val(),
                instructor: $('#classInstructor').val(),
                notes: $('#classNotes').val(),
            };

            $.ajax({
                url: url,
                method: method,
                data: formData,
                success: function(response) {
                    if (response.success) {
                        $('#addClassModal').modal('hide');
                        $('#classForm')[0].reset();
                        $('#classId').val('');
                        alert(response.message);
                        loadClasses();
                    }
                },
                error: function(xhr) {
                    const message = xhr.responseJSON?.message || 'Erro ao salvar turma.';
                    if (xhr.responseJSON?.errors) {
                        const errors = Object.values(xhr.responseJSON.errors).flat().join('\n');
                        alert('Erro de validação:\n' + errors);
                    } else {
                        alert('Erro: ' + message);
                    }
                }
            });
        });

        // Editar turma
        $(document).on('click', '.btn-edit-class', function() {
            const classId = $(this).data('id');
            
            $.ajax({
                url: '{{ route("training.classes.get", ":id") }}'.replace(':id', classId),
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        const classData = response.data;
                        $('#classId').val(classData.training_class_id);
                        $('#classTitle').val(classData.title);
                        $('#classDescription').val(classData.description);
                        $('#classStartDate').val(classData.start_date);
                        $('#classEndDate').val(classData.end_date);
                        $('#classStatus').val(classData.status);
                        $('#classMaxParticipants').val(classData.max_participants);
                        $('#classInstructor').val(classData.instructor);
                        $('#classNotes').val(classData.notes);
                        $('#addClassModalLabel').html('<i class="fas fa-edit me-2"></i>Editar Turma');
                        $('#addClassModal').modal('show');
                    }
                }
            });
        });

        // Resetar modal ao fechar
        $('#addClassModal').on('hidden.bs.modal', function() {
            $('#classForm')[0].reset();
            $('#classId').val('');
            $('#addClassModalLabel').html('<i class="fas fa-plus-circle me-2"></i>Nova Turma');
        });

        // Excluir turma
        $(document).on('click', '.btn-delete-class', function() {
            if (!confirm('Tem certeza que deseja excluir esta turma?')) {
                return;
            }

            const classId = $(this).data('id');

            $.ajax({
                url: '{{ route("training.classes.delete", ":id") }}'.replace(':id', classId),
                method: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        loadClasses();
                    }
                },
                error: function(xhr) {
                    alert('Erro: ' + (xhr.responseJSON?.message || 'Não foi possível excluir a turma.'));
                }
            });
        });

        // Gerenciar tópicos
        $(document).on('click', '.btn-manage-topics', function() {
            currentClassId = $(this).data('id');
            $('#currentClassId').val(currentClassId);
            $('#topicsModal').modal('show');
            loadTopics(currentClassId);
        });

        // Carregar tópicos
        function loadTopics(classId) {
            $.ajax({
                url: '{{ route("training.classes.get", ":id") }}'.replace(':id', classId),
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        renderTopics(response.data.topics);
                    }
                }
            });
        }

        // Renderizar tópicos
        function renderTopics(topics) {
            const topicsList = $('#topicsList');
            
            if (topics.length === 0) {
                topicsList.html('<p class="text-muted">Nenhum tópico cadastrado. Clique em "Adicionar Tópico" para começar.</p>');
                return;
            }

            let html = '';
            topics.forEach(function(topic) {
                let contentsHtml = '';
                if (topic.contents && topic.contents.length > 0) {
                    topic.contents.forEach(function(content) {
                        const typeIcons = {
                            'pdf': 'fa-file-pdf',
                            'excel': 'fa-file-excel',
                            'powerpoint': 'fa-file-powerpoint',
                            'video_file': 'fa-video',
                            'youtube_link': 'fa-youtube'
                        };
                        contentsHtml += `
                            <div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded">
                                <div>
                                    <i class="fas ${typeIcons[content.content_type] || 'fa-file'} me-2"></i>
                                    ${content.title}
                                </div>
                                <button class="btn btn-sm btn-danger btn-remove-content" 
                                        data-topic-id="${topic.training_topic_id}" 
                                        data-content-id="${content.training_content_id}">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        `;
                    });
                } else {
                    contentsHtml = '<p class="text-muted small mb-0">Nenhum conteúdo adicionado</p>';
                }

                html += `
                    <div class="card mb-3">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div>
                                <strong>${topic.title}</strong>
                                ${topic.description ? '<br><small class="text-muted">' + topic.description + '</small>' : ''}
                            </div>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-success btn-add-content" 
                                        data-topic-id="${topic.training_topic_id}">
                                    <i class="fas fa-plus me-1"></i>Conteúdo
                                </button>
                                <button class="btn btn-sm btn-warning btn-edit-topic" 
                                        data-topic-id="${topic.training_topic_id}"
                                        data-title="${topic.title}"
                                        data-description="${topic.description || ''}"
                                        data-order="${topic.order}"
                                        data-duration="${topic.duration_minutes || ''}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger btn-delete-topic" 
                                        data-topic-id="${topic.training_topic_id}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <h6 class="small text-muted mb-2">Conteúdos:</h6>
                            ${contentsHtml}
                        </div>
                    </div>
                `;
            });
            
            topicsList.html(html);
        }

        // Mostrar formulário de tópico
        $('#addTopicBtn').on('click', function() {
            $('#topicFormDiv').show();
            $('#topicForm')[0].reset();
            $('#topicId').val('');
        });

        // Cancelar formulário de tópico
        $('#cancelTopicBtn').on('click', function() {
            $('#topicFormDiv').hide();
            $('#topicForm')[0].reset();
        });

        // Salvar tópico
        $('#topicForm').on('submit', function(e) {
            e.preventDefault();
            
            const topicId = $('#topicId').val();
            const url = topicId 
                ? '{{ route("training.topics.update", ":id") }}'.replace(':id', topicId)
                : '{{ route("training.topics.store") }}';
            const method = topicId ? 'PUT' : 'POST';

            const formData = {
                _token: $('meta[name="csrf-token"]').attr('content'),
                training_class_id: currentClassId,
                title: $('#topicTitle').val(),
                description: $('#topicDescription').val(),
                order: $('#topicOrder').val(),
                duration_minutes: $('#topicDuration').val(),
            };

            $.ajax({
                url: url,
                method: method,
                data: formData,
                success: function(response) {
                    if (response.success) {
                        $('#topicFormDiv').hide();
                        $('#topicForm')[0].reset();
                        $('#topicId').val('');
                        alert(response.message);
                        loadTopics(currentClassId);
                    }
                },
                error: function(xhr) {
                    const message = xhr.responseJSON?.message || 'Erro ao salvar tópico.';
                    alert('Erro: ' + message);
                }
            });
        });

        // Editar tópico
        $(document).on('click', '.btn-edit-topic', function() {
            $('#topicId').val($(this).data('topic-id'));
            $('#topicTitle').val($(this).data('title'));
            $('#topicDescription').val($(this).data('description'));
            $('#topicOrder').val($(this).data('order'));
            $('#topicDuration').val($(this).data('duration'));
            $('#topicFormDiv').show();
        });

        // Excluir tópico
        $(document).on('click', '.btn-delete-topic', function() {
            if (!confirm('Tem certeza que deseja excluir este tópico?')) {
                return;
            }

            const topicId = $(this).data('topic-id');

            $.ajax({
                url: '{{ route("training.topics.delete", ":id") }}'.replace(':id', topicId),
                method: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        loadTopics(currentClassId);
                    }
                },
                error: function(xhr) {
                    alert('Erro: ' + (xhr.responseJSON?.message || 'Não foi possível excluir o tópico.'));
                }
            });
        });

        // Carregar conteúdos disponíveis
        function loadAvailableContents() {
            $.ajax({
                url: '{{ route("training.contents.available") }}',
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        availableContents = response.data;
                        updateContentSelect();
                    }
                }
            });
        }

        // Atualizar select de conteúdos
        function updateContentSelect(excludeIds = []) {
            const select = $('#contentSelect');
            select.empty().append('<option value="">Selecione um conteúdo</option>');
            
            availableContents.forEach(function(content) {
                if (!excludeIds.includes(content.training_content_id)) {
                    select.append(`
                        <option value="${content.training_content_id}">
                            ${content.title} (${content.content_type_label})
                        </option>
                    `);
                }
            });
        }

        // Adicionar conteúdo ao tópico
        $(document).on('click', '.btn-add-content', function() {
            const topicId = $(this).data('topic-id');
            $('#contentTopicId').val(topicId);
            
            // Carregar conteúdos já vinculados ao tópico
            $.ajax({
                url: '{{ route("training.classes.get", ":id") }}'.replace(':id', currentClassId),
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        const topic = response.data.topics.find(t => t.training_topic_id == topicId);
                        const excludeIds = topic ? topic.contents.map(c => c.training_content_id) : [];
                        updateContentSelect(excludeIds);
                        $('#addContentToTopicModal').modal('show');
                    }
                }
            });
        });

        // Salvar conteúdo ao tópico
        $('#addContentToTopicForm').on('submit', function(e) {
            e.preventDefault();
            
            const formData = {
                _token: $('meta[name="csrf-token"]').attr('content'),
                training_topic_id: $('#contentTopicId').val(),
                training_content_id: $('#contentSelect').val(),
                order: $('#contentOrder').val(),
            };

            $.ajax({
                url: '{{ route("training.topics.contents.add") }}',
                method: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        $('#addContentToTopicModal').modal('hide');
                        $('#addContentToTopicForm')[0].reset();
                        alert(response.message);
                        loadTopics(currentClassId);
                    }
                },
                error: function(xhr) {
                    const message = xhr.responseJSON?.message || 'Erro ao adicionar conteúdo.';
                    alert('Erro: ' + message);
                }
            });
        });

        // Remover conteúdo do tópico
        $(document).on('click', '.btn-remove-content', function() {
            if (!confirm('Tem certeza que deseja remover este conteúdo do tópico?')) {
                return;
            }

            const topicId = $(this).data('topic-id');
            const contentId = $(this).data('content-id');

            $.ajax({
                url: '{{ route("training.topics.contents.remove", [":topicId", ":contentId"]) }}'
                    .replace(':topicId', topicId)
                    .replace(':contentId', contentId),
                method: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        loadTopics(currentClassId);
                    }
                },
                error: function(xhr) {
                    alert('Erro: ' + (xhr.responseJSON?.message || 'Não foi possível remover o conteúdo.'));
                }
            });
        });

        // Carregar dados iniciais
        loadClasses();
        loadAvailableContents();
    });
</script>
@endpush
