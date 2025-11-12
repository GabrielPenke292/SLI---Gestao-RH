@extends('template.layout')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/main.min.css" rel="stylesheet">
<style>
    .fc {
        font-family: inherit;
    }

    .fc-toolbar-title {
        font-size: 1.5rem;
        font-weight: 600;
    }

    .fc-button {
        background-color: #0d6efd;
        border-color: #0d6efd;
        padding: 0.375rem 0.75rem;
    }

    .fc-button:hover {
        background-color: #0b5ed7;
        border-color: #0a58ca;
    }

    .fc-button-active {
        background-color: #0a58ca;
        border-color: #0a58ca;
    }

    .fc-event {
        cursor: pointer;
        border-radius: 4px;
    }

    .fc-event:hover {
        opacity: 0.8;
    }

    .event-birthday {
        background-color: #ff6b6b;
        border-color: #ff6b6b;
    }

    .event-anniversary {
        background-color: #4ecdc4;
        border-color: #4ecdc4;
    }

    .event-custom {
        background-color: #3788d8;
        border-color: #3788d8;
    }

    .modal-header.bg-primary {
        background-color: #0d6efd !important;
    }
</style>
@endpush

@section('content')

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Calendário de Funcionários</h4>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-light btn-sm" id="addEventBtn">
                            <i class="fas fa-plus me-1"></i>Novo Evento
                        </button>
                        <a href="{{ route('employees.board') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Voltar
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Legenda -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="d-flex flex-wrap gap-3">
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-danger me-2" style="width: 20px; height: 20px;"></span>
                                    <small>Aniversários</small>
                                </div>
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-info me-2" style="width: 20px; height: 20px; background-color: #4ecdc4 !important;"></span>
                                    <small>Aniversários de Empresa</small>
                                </div>
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-primary me-2" style="width: 20px; height: 20px;"></span>
                                    <small>Eventos Personalizados</small>
                                </div>
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-success me-2" style="width: 20px; height: 20px;"></span>
                                    <small>Exames Admissionais</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Calendário -->
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Adicionar/Editar Evento -->
<div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="eventModalLabel">
                    <i class="fas fa-calendar-plus me-2"></i>Novo Evento
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="eventForm">
                <div class="modal-body">
                    <input type="hidden" id="eventId" name="event_id">
                    <input type="hidden" id="eventType" name="event_type" value="custom">

                    <div class="mb-3">
                        <label for="eventTitle" class="form-label required-field">Título do Evento</label>
                        <input type="text" class="form-control" id="eventTitle" name="event_title" required>
                    </div>

                    <div class="mb-3">
                        <label for="eventDescription" class="form-label">Descrição</label>
                        <textarea class="form-control" id="eventDescription" name="event_description" rows="3"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="eventDate" class="form-label required-field">Data</label>
                            <input type="date" class="form-control" id="eventDate" name="event_date" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="eventColor" class="form-label">Cor</label>
                            <input type="color" class="form-control form-control-color" id="eventColor" name="event_color" value="#3788d8" title="Escolha a cor">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="eventStartTime" class="form-label">Hora de Início</label>
                            <input type="time" class="form-control" id="eventStartTime" name="event_start_time">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="eventEndTime" class="form-label">Hora de Término</label>
                            <input type="time" class="form-control" id="eventEndTime" name="event_end_time">
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

<!-- Modal para Visualizar Evento -->
<div class="modal fade" id="viewEventModal" tabindex="-1" aria-labelledby="viewEventModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="viewEventModalLabel">
                    <i class="fas fa-info-circle me-2"></i>Detalhes do Evento
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="viewEventContent">
                <!-- Conteúdo será preenchido via JavaScript -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Fechar
                </button>
                <button type="button" class="btn btn-warning" id="editEventBtn">
                    <i class="fas fa-edit me-1"></i>Editar
                </button>
                <button type="button" class="btn btn-danger" id="deleteEventBtn">
                    <i class="fas fa-trash me-1"></i>Excluir
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Carregar FullCalendar de forma assíncrona e garantir que esteja disponível
    function loadFullCalendar() {
        return new Promise(function(resolve, reject) {
            // Verificar se já está carregado
            if (typeof FullCalendar !== 'undefined') {
                resolve();
                return;
            }

            // Carregar o script principal
            const script1 = document.createElement('script');
            script1.src = 'https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/main.min.js';
            script1.onload = function() {
                // Carregar o locale
                const script2 = document.createElement('script');
                script2.src = 'https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/locales/pt-br.js';
                script2.onload = function() {
                    if (typeof FullCalendar !== 'undefined') {
                        resolve();
                    } else {
                        reject('FullCalendar não foi carregado corretamente');
                    }
                };
                script2.onerror = function() {
                    reject('Erro ao carregar locale do FullCalendar');
                };
                document.head.appendChild(script2);
            };
            script1.onerror = function() {
                reject('Erro ao carregar FullCalendar');
            };
            document.head.appendChild(script1);
        });
    }

    // Aguardar jQuery e então carregar FullCalendar
    $(document).ready(function() {
        loadFullCalendar().then(function() {
            initCalendar();
        }).catch(function(error) {
            console.error('Erro ao carregar FullCalendar:', error);
            $('#calendar').html('<div class="alert alert-danger">Erro ao carregar o calendário. Por favor, recarregue a página.</div>');
        });
    });

    function initCalendar() {
        const baseUrl = "{{ url('/') }}";
        let calendar;
        let currentEvent = null;
        const eventModal = new bootstrap.Modal(document.getElementById('eventModal'));
        const viewEventModal = new bootstrap.Modal(document.getElementById('viewEventModal'));

        // Inicializar calendário
        const calendarEl = document.getElementById('calendar');
        
        if (!calendarEl) {
            console.error('Elemento calendar não encontrado');
            return;
        }

        // Verificar novamente se FullCalendar está disponível
        if (typeof FullCalendar === 'undefined') {
            console.error('FullCalendar ainda não está disponível');
            return;
        }

        calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'pt-br',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            buttonText: {
                today: 'Hoje',
                month: 'Mês',
                week: 'Semana',
                day: 'Dia'
            },
            events: function(fetchInfo, successCallback, failureCallback) {
                $.ajax({
                    url: baseUrl + '/employees/calendar/events',
                    type: 'GET',
                    data: {
                        start: fetchInfo.startStr,
                        end: fetchInfo.endStr
                    },
                    success: function(response) {
                        successCallback(response);
                    },
                    error: function() {
                        failureCallback();
                    }
                });
            },
            eventClick: function(info) {
                const event = info.event;
                const extendedProps = event.extendedProps;
                
            currentEvent = {
                id: extendedProps.event_id || extendedProps.exam_id || null,
                type: extendedProps.type,
                title: event.title,
                start: event.start,
                end: event.end,
                color: event.backgroundColor || event.borderColor || '#3788d8',
                description: extendedProps.description || '',
                worker_id: extendedProps.worker_id || null,
                worker_name: extendedProps.worker_name || null,
                years: extendedProps.years || null,
                // Dados do exame admissional
                exam_id: extendedProps.exam_id || null,
                candidate_name: extendedProps.candidate_name || null,
                candidate_email: extendedProps.candidate_email || null,
                candidate_phone: extendedProps.candidate_phone || null,
                process_number: extendedProps.process_number || null,
                vacancy_title: extendedProps.vacancy_title || null,
                clinic_name: extendedProps.clinic_name || null,
                clinic_phone: extendedProps.clinic_phone || null,
                exam_time: extendedProps.exam_time || null,
                notes: extendedProps.notes || null,
            };

                // Se for evento customizado, permitir editar/excluir
                if (currentEvent.type === 'custom' && currentEvent.id) {
                    $('#editEventBtn, #deleteEventBtn').show();
                } else {
                    $('#editEventBtn, #deleteEventBtn').hide();
                }

                showEventDetails(currentEvent);
                viewEventModal.show();
            },
            dateClick: function(info) {
                // Preencher data ao clicar em uma data
                $('#eventDate').val(info.dateStr.split('T')[0]);
                $('#eventForm')[0].reset();
                $('#eventId').val('');
                $('#eventType').val('custom');
                $('#eventColor').val('#3788d8');
                eventModal.show();
            },
            eventDisplay: 'block',
            height: 'auto',
        });

        calendar.render();

        // Botão adicionar evento
        $('#addEventBtn').on('click', function() {
            $('#eventForm')[0].reset();
            $('#eventId').val('');
            $('#eventType').val('custom');
            $('#eventColor').val('#3788d8');
            $('#eventModalLabel').html('<i class="fas fa-calendar-plus me-2"></i>Novo Evento');
            eventModal.show();
        });

        // Salvar evento
        $('#eventForm').on('submit', function(e) {
            e.preventDefault();
            
            const formData = {
                event_title: $('#eventTitle').val(),
                event_description: $('#eventDescription').val(),
                event_date: $('#eventDate').val(),
                event_start_time: $('#eventStartTime').val() || null,
                event_end_time: $('#eventEndTime').val() || null,
                event_color: $('#eventColor').val(),
            };

            const eventId = $('#eventId').val();
            const url = eventId 
                ? baseUrl + '/employees/calendar/events/' + eventId
                : baseUrl + '/employees/calendar/events';
            const method = eventId ? 'PUT' : 'POST';

            $.ajax({
                url: url,
                type: method,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: formData,
                success: function(response) {
                    if (response.success) {
                        eventModal.hide();
                        calendar.refetchEvents();
                        showAlert('success', response.message);
                    }
                },
                error: function(xhr) {
                    let message = 'Erro ao salvar evento.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    showAlert('danger', message);
                }
            });
        });

        // Editar evento
        $('#editEventBtn').on('click', function() {
            if (!currentEvent || currentEvent.type !== 'custom' || !currentEvent.id) return;
            
            viewEventModal.hide();
            
            $('#eventId').val(currentEvent.id);
            $('#eventTitle').val(currentEvent.title);
            $('#eventDescription').val(currentEvent.description);
            $('#eventDate').val(currentEvent.start.toISOString().split('T')[0]);
            $('#eventColor').val(currentEvent.color || '#3788d8');
            
            if (currentEvent.start.toTimeString) {
                const timeStr = currentEvent.start.toTimeString().split(' ')[0].substring(0, 5);
                $('#eventStartTime').val(timeStr);
            }
            
            if (currentEvent.end && currentEvent.end.toTimeString) {
                const timeStr = currentEvent.end.toTimeString().split(' ')[0].substring(0, 5);
                $('#eventEndTime').val(timeStr);
            }
            
            $('#eventModalLabel').html('<i class="fas fa-edit me-2"></i>Editar Evento');
            eventModal.show();
        });

        // Excluir evento
        $('#deleteEventBtn').on('click', function() {
            if (!currentEvent || currentEvent.type !== 'custom' || !currentEvent.id) return;
            
            if (!confirm('Tem certeza que deseja excluir este evento?')) return;

            $.ajax({
                url: baseUrl + '/employees/calendar/events/' + currentEvent.id,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        viewEventModal.hide();
                        calendar.refetchEvents();
                        showAlert('success', response.message);
                    }
                },
                error: function(xhr) {
                    let message = 'Erro ao excluir evento.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    showAlert('danger', message);
                }
            });
        });

        // Mostrar detalhes do evento
        function showEventDetails(event) {
            let html = '<div class="mb-3">';
            html += '<h6 class="fw-bold">' + event.title + '</h6>';
            
            if (event.description) {
                html += '<p class="text-muted">' + event.description + '</p>';
            }
            
            html += '<div class="row">';
            html += '<div class="col-12 mb-2">';
            html += '<strong>Data:</strong> ' + formatDate(event.start);
            if (event.exam_time) {
                html += ' às ' + event.exam_time;
            }
            if (event.end && event.start.toDateString() !== event.end.toDateString()) {
                html += ' até ' + formatDate(event.end);
            }
            html += '</div>';
            
            // Informações específicas para exames admissionais
            if (event.type === 'admissional_exam') {
                html += '<div class="col-12 mb-3"><hr></div>';
                html += '<div class="col-12 mb-2"><strong>Dados do Candidato:</strong></div>';
                html += '<div class="col-12 mb-2">';
                html += '<strong>Nome:</strong> ' + (event.candidate_name || 'N/A');
                html += '</div>';
                if (event.candidate_email) {
                    html += '<div class="col-12 mb-2">';
                    html += '<strong>E-mail:</strong> ' + event.candidate_email;
                    html += '</div>';
                }
                if (event.candidate_phone) {
                    html += '<div class="col-12 mb-2">';
                    html += '<strong>Telefone:</strong> ' + event.candidate_phone;
                    html += '</div>';
                }
                html += '<div class="col-12 mb-3"><hr></div>';
                html += '<div class="col-12 mb-2"><strong>Dados do Processo e Vaga:</strong></div>';
                html += '<div class="col-12 mb-2">';
                html += '<strong>Processo:</strong> ' + (event.process_number || 'N/A');
                html += '</div>';
                html += '<div class="col-12 mb-2">';
                html += '<strong>Vaga:</strong> ' + (event.vacancy_title || 'N/A');
                html += '</div>';
                html += '<div class="col-12 mb-3"><hr></div>';
                html += '<div class="col-12 mb-2"><strong>Dados da Clínica:</strong></div>';
                html += '<div class="col-12 mb-2">';
                html += '<strong>Clínica:</strong> ' + (event.clinic_name || 'N/A');
                html += '</div>';
                if (event.clinic_phone) {
                    html += '<div class="col-12 mb-2">';
                    html += '<strong>Telefone:</strong> ' + event.clinic_phone;
                    html += '</div>';
                }
                if (event.notes) {
                    html += '<div class="col-12 mb-3"><hr></div>';
                    html += '<div class="col-12 mb-2">';
                    html += '<strong>Observações:</strong><br>' + event.notes;
                    html += '</div>';
                }
            } else {
                // Informações para outros tipos de eventos
                if (event.worker_name) {
                    html += '<div class="col-12 mb-2">';
                    html += '<strong>Funcionário:</strong> ' + event.worker_name;
                    html += '</div>';
                }
                
                if (event.years !== null) {
                    html += '<div class="col-12 mb-2">';
                    html += '<strong>Anos:</strong> ' + event.years;
                    html += '</div>';
                }
            }
            
            html += '</div>';
            html += '</div>';
            
            $('#viewEventContent').html(html);
        }

        function formatDate(date) {
            if (!date) return '';
            const d = new Date(date);
            return d.toLocaleDateString('pt-BR', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        }

        function showAlert(type, message) {
            const alertHtml = '<div class="alert alert-' + type + ' alert-dismissible fade show" role="alert">' +
                '<i class="fas fa-' + (type === 'success' ? 'check-circle' : 'exclamation-circle') + ' me-2"></i>' + message +
                '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                '</div>';
            $('.card-body').prepend(alertHtml);
            
            setTimeout(function() {
                $('.alert').fadeOut();
            }, 3000);
        }
    }
</script>
@endpush
