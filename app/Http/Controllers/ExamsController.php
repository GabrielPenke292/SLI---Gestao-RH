<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Clinic;
use App\Models\AdmissionalExam;
use App\Models\DismissalExam;
use App\Models\Candidate;
use App\Models\SelectionProcess;
use App\Models\Worker;
use App\Models\Layoff;
use Barryvdh\DomPDF\Facade\Pdf;

class ExamsController extends Controller
{
    
    public function index()
    {
        return view('exams.index');
    }
    
    public function admissionals()
    {
        return view('exams.admissionals');
    }
    
    public function clinics()
    {
        return view('exams.clinics');
    }

    public function dismissals()
    {
        return view('exams.dismissals');
    }

    /**
     * Buscar todas as clínicas (para DataTable)
     */
    public function getClinicsData(): JsonResponse
    {
        $clinics = Clinic::whereNull('deleted_at')
            ->orderBy('corporate_name', 'asc')
            ->get()
            ->map(function($clinic) {
                return [
                    'clinic_id' => $clinic->clinic_id,
                    'corporate_name' => $clinic->corporate_name,
                    'trade_name' => $clinic->trade_name ?? '-',
                    'cnpj' => $clinic->formatted_cnpj,
                    'email' => $clinic->email ?? '-',
                    'phone' => $clinic->formatted_phone,
                    'city' => $clinic->city ?? '-',
                    'state' => $clinic->state ?? '-',
                    'is_active' => $clinic->is_active,
                    'created_at' => $clinic->created_at?->format('d/m/Y H:i') ?? '-',
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $clinics
        ]);
    }

    /**
     * Criar nova clínica
     */
    public function storeClinic(Request $request): JsonResponse
    {
        $request->validate([
            'corporate_name' => 'required|string|max:255',
            'trade_name' => 'nullable|string|max:255',
            'cnpj' => 'required|string|max:18|unique:clinics,cnpj',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'address_number' => 'nullable|string|max:20',
            'address_complement' => 'nullable|string|max:100',
            'neighborhood' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:2',
            'zip_code' => 'nullable|string|max:10',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        try {
            $user = Auth::user();
            
            // Limpar CNPJ (remover formatação)
            $cnpj = preg_replace('/\D/', '', $request->input('cnpj'));
            
            // Limpar CEP
            $zipCode = $request->input('zip_code') ? preg_replace('/\D/', '', $request->input('zip_code')) : null;

            $clinic = Clinic::create([
                'corporate_name' => $request->input('corporate_name'),
                'trade_name' => $request->input('trade_name'),
                'cnpj' => $cnpj,
                'email' => $request->input('email'),
                'phone' => $request->input('phone'),
                'address' => $request->input('address'),
                'address_number' => $request->input('address_number'),
                'address_complement' => $request->input('address_complement'),
                'neighborhood' => $request->input('neighborhood'),
                'city' => $request->input('city'),
                'state' => strtoupper($request->input('state') ?? ''),
                'zip_code' => $zipCode,
                'notes' => $request->input('notes'),
                'is_active' => $request->input('is_active', true),
                'created_at' => now(),
                'created_by' => $user->user_name ?? 'system',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Clínica cadastrada com sucesso!',
                'data' => $clinic
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao cadastrar clínica: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Buscar clínica por ID
     */
    public function getClinic($id): JsonResponse
    {
        $clinic = Clinic::whereNull('deleted_at')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'clinic_id' => $clinic->clinic_id,
                'corporate_name' => $clinic->corporate_name,
                'trade_name' => $clinic->trade_name,
                'cnpj' => $clinic->cnpj,
                'email' => $clinic->email,
                'phone' => $clinic->phone,
                'address' => $clinic->address,
                'address_number' => $clinic->address_number,
                'address_complement' => $clinic->address_complement,
                'neighborhood' => $clinic->neighborhood,
                'city' => $clinic->city,
                'state' => $clinic->state,
                'zip_code' => $clinic->zip_code,
                'notes' => $clinic->notes,
                'is_active' => $clinic->is_active,
            ]
        ]);
    }

    /**
     * Atualizar clínica
     */
    public function updateClinic(Request $request, $id): JsonResponse
    {
        $clinic = Clinic::whereNull('deleted_at')->findOrFail($id);

        $request->validate([
            'corporate_name' => 'required|string|max:255',
            'trade_name' => 'nullable|string|max:255',
            'cnpj' => 'required|string|max:18|unique:clinics,cnpj,' . $id . ',clinic_id',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'address_number' => 'nullable|string|max:20',
            'address_complement' => 'nullable|string|max:100',
            'neighborhood' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:2',
            'zip_code' => 'nullable|string|max:10',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        try {
            $user = Auth::user();
            
            // Limpar CNPJ (remover formatação)
            $cnpj = preg_replace('/\D/', '', $request->input('cnpj'));
            
            // Limpar CEP
            $zipCode = $request->input('zip_code') ? preg_replace('/\D/', '', $request->input('zip_code')) : null;

            $clinic->update([
                'corporate_name' => $request->input('corporate_name'),
                'trade_name' => $request->input('trade_name'),
                'cnpj' => $cnpj,
                'email' => $request->input('email'),
                'phone' => $request->input('phone'),
                'address' => $request->input('address'),
                'address_number' => $request->input('address_number'),
                'address_complement' => $request->input('address_complement'),
                'neighborhood' => $request->input('neighborhood'),
                'city' => $request->input('city'),
                'state' => strtoupper($request->input('state') ?? ''),
                'zip_code' => $zipCode,
                'notes' => $request->input('notes'),
                'is_active' => $request->input('is_active', true),
                'updated_at' => now(),
                'updated_by' => $user->user_name ?? 'system',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Clínica atualizada com sucesso!',
                'data' => $clinic
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar clínica: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Excluir clínica (soft delete)
     */
    public function deleteClinic($id): JsonResponse
    {
        $clinic = Clinic::whereNull('deleted_at')->findOrFail($id);

        try {
            $clinic->delete();

            return response()->json([
                'success' => true,
                'message' => 'Clínica excluída com sucesso!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao excluir clínica: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Buscar todos os exames admissionais
     */
    public function getAdmissionalExamsData(): JsonResponse
    {
        $exams = AdmissionalExam::whereNull('deleted_at')
            ->with(['candidate', 'selectionProcess.vacancy', 'clinic'])
            ->orderBy('exam_date', 'desc')
            ->orderBy('exam_time', 'desc')
            ->get()
            ->map(function($exam) {
                return [
                    'admissional_exam_id' => $exam->admissional_exam_id,
                    'candidate_name' => $exam->candidate->candidate_name ?? '-',
                    'process_number' => $exam->selectionProcess->process_number ?? '-',
                    'vacancy_title' => $exam->selectionProcess->vacancy->vacancy_title ?? '-',
                    'clinic_name' => $exam->clinic->corporate_name ?? '-',
                    'exam_date' => $exam->exam_date?->format('d/m/Y') ?? '-',
                    'exam_time' => $exam->exam_time ? \Carbon\Carbon::parse($exam->exam_time)->format('H:i') : '-',
                    'status' => $exam->status,
                    'exam_performed' => $exam->exam_performed ?? false,
                    'exam_file_name' => $exam->exam_file_name ?? null,
                    'performed_at' => $exam->performed_at?->format('d/m/Y H:i') ?? null,
                    'created_at' => $exam->created_at?->format('d/m/Y H:i') ?? '-',
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $exams
        ]);
    }

    /**
     * Buscar candidatos aprovados (de todos os processos)
     */
    public function getApprovedCandidates(): JsonResponse
    {
        $candidates = DB::table('selection_process_candidates')
            ->where('selection_process_candidates.status', 'aprovado')
            ->join('candidates', 'selection_process_candidates.candidate_id', '=', 'candidates.candidate_id')
            ->join('selection_processes', 'selection_process_candidates.selection_process_id', '=', 'selection_processes.selection_process_id')
            ->leftJoin('vacancies', 'selection_processes.vacancy_id', '=', 'vacancies.vacancy_id')
            ->select(
                'candidates.candidate_id',
                'candidates.candidate_name',
                'candidates.candidate_email',
                'candidates.candidate_phone',
                'selection_processes.selection_process_id',
                'selection_processes.process_number',
                'vacancies.vacancy_title'
            )
            ->whereNull('selection_processes.deleted_at')
            ->whereNull('candidates.deleted_at')
            ->get()
            ->map(function($item) {
                return [
                    'candidate_id' => $item->candidate_id,
                    'candidate_name' => $item->candidate_name,
                    'candidate_email' => $item->candidate_email ?? '-',
                    'candidate_phone' => $item->candidate_phone ?? '-',
                    'selection_process_id' => $item->selection_process_id,
                    'process_number' => $item->process_number,
                    'vacancy_title' => $item->vacancy_title ?? 'N/A',
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $candidates
        ]);
    }

    /**
     * Buscar clínicas ativas
     */
    public function getActiveClinics(): JsonResponse
    {
        $clinics = Clinic::whereNull('deleted_at')
            ->where('is_active', true)
            ->orderBy('corporate_name', 'asc')
            ->get()
            ->map(function($clinic) {
                return [
                    'clinic_id' => $clinic->clinic_id,
                    'corporate_name' => $clinic->corporate_name,
                    'trade_name' => $clinic->trade_name ?? $clinic->corporate_name,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $clinics
        ]);
    }

    /**
     * Criar novo agendamento de exame
     */
    public function storeAdmissionalExam(Request $request): JsonResponse
    {
        $request->validate([
            'candidate_id' => 'required|integer|exists:candidates,candidate_id',
            'selection_process_id' => 'required|integer|exists:selection_processes,selection_process_id',
            'clinic_id' => 'required|integer|exists:clinics,clinic_id',
            'exam_date' => 'required|date',
            'exam_time' => 'nullable|date_format:H:i',
            'notes' => 'nullable|string',
        ]);

        try {
            $user = Auth::user();

            // Verificar se candidato está aprovado no processo
            $pivot = DB::table('selection_process_candidates')
                ->where('selection_process_id', $request->input('selection_process_id'))
                ->where('candidate_id', $request->input('candidate_id'))
                ->where('status', 'aprovado')
                ->first();

            if (!$pivot) {
                return response()->json([
                    'success' => false,
                    'message' => 'Candidato não está aprovado neste processo seletivo.'
                ], 400);
            }

            $exam = AdmissionalExam::create([
                'candidate_id' => $request->input('candidate_id'),
                'selection_process_id' => $request->input('selection_process_id'),
                'clinic_id' => $request->input('clinic_id'),
                'exam_date' => $request->input('exam_date'),
                'exam_time' => $request->input('exam_time') ? \Carbon\Carbon::parse($request->input('exam_time'))->format('H:i:s') : null,
                'status' => 'agendado',
                'notes' => $request->input('notes'),
                'created_at' => now(),
                'created_by' => $user->user_name ?? 'system',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Exame agendado com sucesso!',
                'data' => $exam
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao agendar exame: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Atualizar status do exame
     */
    public function updateAdmissionalExamStatus(Request $request, $id): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:agendado,cancelado,finalizado',
            'cancellation_reason' => 'nullable|string|required_if:status,cancelado',
            'exam_result' => 'nullable|string',
        ]);

        $exam = AdmissionalExam::whereNull('deleted_at')->findOrFail($id);

        try {
            $user = Auth::user();

            $updateData = [
                'status' => $request->input('status'),
                'updated_at' => now(),
                'updated_by' => $user->user_name ?? 'system',
            ];

            if ($request->input('status') === 'cancelado') {
                $updateData['cancellation_reason'] = $request->input('cancellation_reason');
            }

            if ($request->input('status') === 'finalizado') {
                $updateData['exam_result'] = $request->input('exam_result');
            }

            $exam->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Status do exame atualizado com sucesso!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Gerar PDF com informações do candidato e função
     */
    public function generateExamPDF($id)
    {
        $exam = AdmissionalExam::whereNull('deleted_at')
            ->with(['candidate', 'selectionProcess.vacancy', 'clinic'])
            ->findOrFail($id);

        $data = [
            'exam' => $exam,
            'candidate' => $exam->candidate,
            'process' => $exam->selectionProcess,
            'vacancy' => $exam->selectionProcess->vacancy ?? null,
            'clinic' => $exam->clinic,
        ];

        $pdf = Pdf::loadView('exams.pdf.exam-document', $data);
        
        $filename = 'exame_admissional_' . $exam->candidate->candidate_name . '_' . date('YmdHis') . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * Marcar exame admissional como realizado
     */
    public function markExamAsPerformed(Request $request, $id): JsonResponse
    {
        $request->validate([
            'exam_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240', // 10MB max
            'performed_observations' => 'nullable|string',
        ]);

        $exam = AdmissionalExam::whereNull('deleted_at')->findOrFail($id);

        try {
            $user = Auth::user();
            
            $updateData = [
                'exam_performed' => true,
                'performed_at' => now(),
                'performed_by' => $user->user_name ?? 'system',
                'performed_observations' => $request->input('performed_observations'),
                'updated_at' => now(),
                'updated_by' => $user->user_name ?? 'system',
            ];

            // Upload do arquivo se fornecido
            if ($request->hasFile('exam_file')) {
                $file = $request->file('exam_file');
                $fileName = 'exam_' . $exam->admissional_exam_id . '_' . time() . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('admissional_exams', $fileName, 'public');
                
                $updateData['exam_file_path'] = $filePath;
                $updateData['exam_file_name'] = $file->getClientOriginalName();
            }

            $exam->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Exame marcado como realizado com sucesso!',
                'data' => [
                    'exam_performed' => true,
                    'exam_file_name' => $exam->exam_file_name,
                    'performed_at' => $exam->performed_at?->format('d/m/Y H:i'),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao marcar exame como realizado: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download do arquivo do exame
     */
    public function downloadExamFile($id)
    {
        $exam = AdmissionalExam::whereNull('deleted_at')
            ->where('exam_performed', true)
            ->findOrFail($id);

        if (!$exam->exam_file_path || !Storage::disk('public')->exists($exam->exam_file_path)) {
            abort(404, 'Arquivo não encontrado');
        }

        $filePath = Storage::disk('public')->path($exam->exam_file_path);
        $fileName = $exam->exam_file_name ?? 'exame.pdf';
        
        return response()->download($filePath, $fileName);
    }

    /**
     * Buscar todos os exames demissionais
     */
    public function getDismissalExamsData(): JsonResponse
    {
        $exams = DismissalExam::whereNull('deleted_at')
            ->with(['worker.department', 'worker.roles', 'clinic'])
            ->orderBy('exam_date', 'desc')
            ->orderBy('exam_time', 'desc')
            ->get()
            ->map(function($exam) {
                $roles = $exam->worker->roles->pluck('role_name')->implode(', ');
                
                return [
                    'dismissal_exam_id' => $exam->dismissal_exam_id,
                    'worker_name' => $exam->worker->worker_name ?? '-',
                    'worker_email' => $exam->worker->worker_email ?? '-',
                    'department' => $exam->worker->department?->department_name ?? '-',
                    'position' => $roles ?: '-',
                    'clinic_name' => $exam->clinic->corporate_name ?? '-',
                    'exam_date' => $exam->exam_date?->format('d/m/Y') ?? '-',
                    'exam_time' => $exam->exam_time ? \Carbon\Carbon::parse($exam->exam_time)->format('H:i') : '-',
                    'status' => $exam->status,
                    'created_at' => $exam->created_at?->format('d/m/Y H:i') ?? '-',
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $exams
        ]);
    }

    /**
     * Buscar funcionários desligados
     */
    public function getDismissedWorkers(): JsonResponse
    {
        $workers = Worker::whereNull('deleted_at')
            ->where('worker_status', 0)
            ->whereHas('layoffs', function($query) {
                $query->whereNull('deleted_at');
            })
            ->with(['department', 'roles', 'layoffs' => function($query) {
                $query->whereNull('deleted_at')->orderBy('layoff_date', 'desc');
            }])
            ->orderBy('worker_name', 'asc')
            ->get()
            ->map(function($worker) {
                $roles = $worker->roles->pluck('role_name')->implode(', ');
                $latestLayoff = $worker->layoffs->first();
                
                return [
                    'worker_id' => $worker->worker_id,
                    'worker_name' => $worker->worker_name,
                    'worker_email' => $worker->worker_email ?? '-',
                    'worker_phone' => '-',
                    'department' => $worker->department?->department_name ?? '-',
                    'position' => $roles ?: '-',
                    'layoff_date' => $latestLayoff?->layoff_date?->format('d/m/Y') ?? '-',
                    'layoff_type' => $latestLayoff?->layoff_type ?? '-',
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $workers
        ]);
    }

    /**
     * Criar novo agendamento de exame demissional
     */
    public function storeDismissalExam(Request $request): JsonResponse
    {
        $request->validate([
            'worker_id' => 'required|integer|exists:workers,worker_id',
            'clinic_id' => 'required|integer|exists:clinics,clinic_id',
            'exam_date' => 'required|date',
            'exam_time' => 'nullable|date_format:H:i',
            'notes' => 'nullable|string',
        ]);

        try {
            $user = Auth::user();

            // Verificar se funcionário foi desligado
            $worker = Worker::whereNull('deleted_at')
                ->where('worker_status', 0)
                ->whereHas('layoffs', function($query) {
                    $query->whereNull('deleted_at');
                })
                ->findOrFail($request->input('worker_id'));

            $exam = DismissalExam::create([
                'worker_id' => $request->input('worker_id'),
                'clinic_id' => $request->input('clinic_id'),
                'exam_date' => $request->input('exam_date'),
                'exam_time' => $request->input('exam_time') ? \Carbon\Carbon::parse($request->input('exam_time'))->format('H:i:s') : null,
                'status' => 'agendado',
                'notes' => $request->input('notes'),
                'created_at' => now(),
                'created_by' => $user->user_name ?? 'system',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Exame demissional agendado com sucesso!',
                'data' => $exam
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao agendar exame: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Atualizar status do exame demissional
     */
    public function updateDismissalExamStatus(Request $request, $id): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:agendado,cancelado,finalizado',
            'cancellation_reason' => 'nullable|string|required_if:status,cancelado',
            'exam_result' => 'nullable|string',
        ]);

        $exam = DismissalExam::whereNull('deleted_at')->findOrFail($id);

        try {
            $user = Auth::user();

            $updateData = [
                'status' => $request->input('status'),
                'updated_at' => now(),
                'updated_by' => $user->user_name ?? 'system',
            ];

            if ($request->input('status') === 'cancelado') {
                $updateData['cancellation_reason'] = $request->input('cancellation_reason');
            }

            if ($request->input('status') === 'finalizado') {
                $updateData['exam_result'] = $request->input('exam_result');
            }

            $exam->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Status do exame atualizado com sucesso!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Gerar PDF com informações do funcionário desligado
     */
    public function generateDismissalExamPDF($id)
    {
        $exam = DismissalExam::whereNull('deleted_at')
            ->with(['worker.department', 'worker.roles', 'clinic', 'worker.layoffs' => function($query) {
                $query->whereNull('deleted_at')->orderBy('layoff_date', 'desc');
            }])
            ->findOrFail($id);

        $data = [
            'exam' => $exam,
            'worker' => $exam->worker,
            'clinic' => $exam->clinic,
            'layoff' => $exam->worker->layoffs->first(),
        ];

        $pdf = Pdf::loadView('exams.pdf.dismissal-exam-document', $data);
        
        $filename = 'exame_demissional_' . $exam->worker->worker_name . '_' . date('YmdHis') . '.pdf';
        
        return $pdf->download($filename);
    }
}
