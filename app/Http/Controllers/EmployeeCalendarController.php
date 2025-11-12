<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\CalendarEvent;
use App\Models\Worker;
use App\Models\AdmissionalExam;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EmployeeCalendarController extends Controller
{
    public function index()
    {
        return view('employees.calendar');
    }

    /**
     * Retorna todos os eventos para o calendário
     */
    public function getEvents(Request $request): JsonResponse
    {
        $start = $request->input('start');
        $end = $request->input('end');

        $events = [];

        // Buscar eventos customizados
        $customEvents = CalendarEvent::whereNull('deleted_at')
            ->where('event_status', 1)
            ->whereBetween('event_date', [$start, $end])
            ->get();

        foreach ($customEvents as $event) {
            $startDate = $event->event_date->format('Y-m-d');
            $startTime = $event->event_start_time ? (is_string($event->event_start_time) ? $event->event_start_time : $event->event_start_time->format('H:i:s')) : null;
            $endTime = $event->event_end_time ? (is_string($event->event_end_time) ? $event->event_end_time : $event->event_end_time->format('H:i:s')) : null;
            
            $events[] = [
                'id' => 'event_' . $event->event_id,
                'title' => $event->event_title,
                'start' => $startDate . ($startTime ? 'T' . $startTime : ''),
                'end' => $endTime ? $startDate . 'T' . $endTime : null,
                'allDay' => !$startTime && !$endTime,
                'backgroundColor' => $event->event_color,
                'borderColor' => $event->event_color,
                'extendedProps' => [
                    'type' => 'custom',
                    'event_id' => $event->event_id,
                    'description' => $event->event_description,
                    'worker_id' => $event->worker_id,
                ],
            ];
        }

        // Buscar aniversários de funcionários
        $workers = Worker::whereNull('deleted_at')
            ->where('worker_status', 1)
            ->whereNotNull('worker_birth_date')
            ->get();

        foreach ($workers as $worker) {
            if ($worker->worker_birth_date) {
                $birthDate = $worker->worker_birth_date;
                $currentYear = date('Y');
                
                // Criar data do aniversário no ano atual
                $birthdayThisYear = $currentYear . '-' . $birthDate->format('m-d');
                $birthdayNextYear = ($currentYear + 1) . '-' . $birthDate->format('m-d');
                
                // Verificar se está no range
                if ($birthdayThisYear >= $start && $birthdayThisYear <= $end) {
                    $events[] = [
                        'id' => 'birthday_' . $worker->worker_id . '_' . $currentYear,
                        'title' => '🎂 Aniversário: ' . $worker->worker_name,
                        'start' => $birthdayThisYear,
                        'allDay' => true,
                        'backgroundColor' => '#ff6b6b',
                        'borderColor' => '#ff6b6b',
                        'extendedProps' => [
                            'type' => 'birthday',
                            'worker_id' => $worker->worker_id,
                            'worker_name' => $worker->worker_name,
                        ],
                    ];
                }
                
                if ($birthdayNextYear >= $start && $birthdayNextYear <= $end) {
                    $events[] = [
                        'id' => 'birthday_' . $worker->worker_id . '_' . ($currentYear + 1),
                        'title' => '🎂 Aniversário: ' . $worker->worker_name,
                        'start' => $birthdayNextYear,
                        'allDay' => true,
                        'backgroundColor' => '#ff6b6b',
                        'borderColor' => '#ff6b6b',
                        'extendedProps' => [
                            'type' => 'birthday',
                            'worker_id' => $worker->worker_id,
                            'worker_name' => $worker->worker_name,
                        ],
                    ];
                }
            }
        }

        // Buscar aniversários de tempo de empresa
        foreach ($workers as $worker) {
            if ($worker->worker_start_date) {
                $startDate = $worker->worker_start_date;
                $currentYear = date('Y');
                
                // Criar data do aniversário de empresa no ano atual
                $anniversaryThisYear = $currentYear . '-' . $startDate->format('m-d');
                $anniversaryNextYear = ($currentYear + 1) . '-' . $startDate->format('m-d');
                
                // Calcular anos de empresa
                $years = $currentYear - $startDate->format('Y');
                
                // Verificar se está no range
                if ($anniversaryThisYear >= $start && $anniversaryThisYear <= $end) {
                    // Se for o primeiro dia (0 anos), mostrar mensagem especial
                    $title = $years == 0 
                        ? '🏢 ' . $worker->worker_name . ' começou na empresa'
                        : '🏢 ' . $years . ' ano(s) de empresa: ' . $worker->worker_name;
                    
                    $events[] = [
                        'id' => 'anniversary_' . $worker->worker_id . '_' . $currentYear,
                        'title' => $title,
                        'start' => $anniversaryThisYear,
                        'allDay' => true,
                        'backgroundColor' => '#4ecdc4',
                        'borderColor' => '#4ecdc4',
                        'extendedProps' => [
                            'type' => 'anniversary',
                            'worker_id' => $worker->worker_id,
                            'worker_name' => $worker->worker_name,
                            'years' => $years,
                        ],
                    ];
                }
                
                if ($anniversaryNextYear >= $start && $anniversaryNextYear <= $end) {
                    $nextYears = $years + 1;
                    // Se for o primeiro dia (0 anos no próximo ano significa que é o primeiro aniversário)
                    $title = $nextYears == 1 && $years == 0
                        ? '🏢 ' . $worker->worker_name . ' completou 1 ano na empresa'
                        : '🏢 ' . $nextYears . ' ano(s) de empresa: ' . $worker->worker_name;
                    
                    $events[] = [
                        'id' => 'anniversary_' . $worker->worker_id . '_' . ($currentYear + 1),
                        'title' => $title,
                        'start' => $anniversaryNextYear,
                        'allDay' => true,
                        'backgroundColor' => '#4ecdc4',
                        'borderColor' => '#4ecdc4',
                        'extendedProps' => [
                            'type' => 'anniversary',
                            'worker_id' => $worker->worker_id,
                            'worker_name' => $worker->worker_name,
                            'years' => $nextYears,
                        ],
                    ];
                }
            }
        }

        // Buscar exames admissionais agendados
        $admissionalExams = AdmissionalExam::whereNull('deleted_at')
            ->where('status', 'agendado')
            ->whereBetween('exam_date', [$start, $end])
            ->with(['candidate', 'selectionProcess.vacancy', 'clinic'])
            ->get();

        foreach ($admissionalExams as $exam) {
            $examDate = $exam->exam_date->format('Y-m-d');
            
            // Tratar o horário do exame
            $examTime = null;
            $examTimeFormatted = null;
            if ($exam->exam_time) {
                // Se for string, fazer parse; se já for objeto Carbon/DateTime, usar diretamente
                if (is_string($exam->exam_time)) {
                    try {
                        $timeObj = \Carbon\Carbon::parse($exam->exam_time);
                        $examTime = $timeObj->format('H:i:s');
                        $examTimeFormatted = $timeObj->format('H:i');
                    } catch (\Exception $e) {
                        // Se falhar, tentar usar diretamente como string
                        $examTime = $exam->exam_time;
                        $examTimeFormatted = substr($exam->exam_time, 0, 5);
                    }
                } else {
                    $examTime = $exam->exam_time->format('H:i:s');
                    $examTimeFormatted = $exam->exam_time->format('H:i');
                }
            }
            
            $title = '🏥 Exame: ' . ($exam->candidate->candidate_name ?? 'N/A');
            if ($exam->clinic) {
                $title .= ' - ' . $exam->clinic->corporate_name;
            }
            
            $events[] = [
                'id' => 'exam_' . $exam->admissional_exam_id,
                'title' => $title,
                'start' => $examDate . ($examTime ? 'T' . $examTime : ''),
                'allDay' => !$examTime,
                'backgroundColor' => '#28a745',
                'borderColor' => '#28a745',
                'extendedProps' => [
                    'type' => 'admissional_exam',
                    'exam_id' => $exam->admissional_exam_id,
                    'candidate_name' => $exam->candidate->candidate_name ?? 'N/A',
                    'candidate_email' => $exam->candidate->candidate_email ?? null,
                    'candidate_phone' => $exam->candidate->candidate_phone ?? null,
                    'process_number' => $exam->selectionProcess->process_number ?? 'N/A',
                    'vacancy_title' => $exam->selectionProcess->vacancy->vacancy_title ?? 'N/A',
                    'clinic_name' => $exam->clinic->corporate_name ?? 'N/A',
                    'clinic_phone' => $exam->clinic->phone ?? null,
                    'exam_time' => $examTimeFormatted,
                    'notes' => $exam->notes ?? null,
                ],
            ];
        }

        return response()->json($events);
    }

    /**
     * Cria um novo evento
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'event_title' => 'required|string|max:255',
            'event_description' => 'nullable|string',
            'event_date' => 'required|date',
            'event_start_time' => 'nullable|date_format:H:i',
            'event_end_time' => 'nullable|date_format:H:i|after:event_start_time',
            'event_color' => 'nullable|string|max:7',
            'worker_id' => 'nullable|exists:workers,worker_id',
        ], [
            'event_title.required' => 'O título do evento é obrigatório.',
            'event_date.required' => 'A data do evento é obrigatória.',
            'event_end_time.after' => 'A hora de término deve ser posterior à hora de início.',
        ]);

        try {
            $event = CalendarEvent::create([
                'event_title' => $validated['event_title'],
                'event_description' => $validated['event_description'] ?? null,
                'event_date' => $validated['event_date'],
                'event_start_time' => $validated['event_start_time'] ?? null,
                'event_end_time' => $validated['event_end_time'] ?? null,
                'event_type' => 'custom',
                'worker_id' => $validated['worker_id'] ?? null,
                'event_color' => $validated['event_color'] ?? '#3788d8',
                'event_status' => 1,
                'created_by' => Auth::user()->name ?? 'system',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Evento criado com sucesso!',
                'event' => $event,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar evento: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Atualiza um evento
     */
    public function update(Request $request, $id): JsonResponse
    {
        $event = CalendarEvent::whereNull('deleted_at')->findOrFail($id);

        $validated = $request->validate([
            'event_title' => 'required|string|max:255',
            'event_description' => 'nullable|string',
            'event_date' => 'required|date',
            'event_start_time' => 'nullable|date_format:H:i',
            'event_end_time' => 'nullable|date_format:H:i|after:event_start_time',
            'event_color' => 'nullable|string|max:7',
            'worker_id' => 'nullable|exists:workers,worker_id',
        ], [
            'event_title.required' => 'O título do evento é obrigatório.',
            'event_date.required' => 'A data do evento é obrigatória.',
            'event_end_time.after' => 'A hora de término deve ser posterior à hora de início.',
        ]);

        try {
            $event->update([
                'event_title' => $validated['event_title'],
                'event_description' => $validated['event_description'] ?? null,
                'event_date' => $validated['event_date'],
                'event_start_time' => $validated['event_start_time'] ?? null,
                'event_end_time' => $validated['event_end_time'] ?? null,
                'worker_id' => $validated['worker_id'] ?? null,
                'event_color' => $validated['event_color'] ?? '#3788d8',
                'updated_by' => Auth::user()->name ?? 'system',
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Evento atualizado com sucesso!',
                'event' => $event,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar evento: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove um evento
     */
    public function destroy($id): JsonResponse
    {
        try {
            $event = CalendarEvent::whereNull('deleted_at')->findOrFail($id);
            
            $event->deleted_by = Auth::user()->name ?? 'system';
            $event->deleted_at = now();
            $event->save();

            return response()->json([
                'success' => true,
                'message' => 'Evento excluído com sucesso!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao excluir evento: ' . $e->getMessage(),
            ], 500);
        }
    }
}
