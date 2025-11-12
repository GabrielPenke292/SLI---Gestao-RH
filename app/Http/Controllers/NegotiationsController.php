<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\SelectionProcess;
use App\Models\Candidate;
use App\Models\Proposal;

class NegotiationsController extends Controller
{
    /**
     * Exibir página de negociações
     */
    public function index()
    {
        return view('negotiations.index');
    }

    /**
     * Buscar processos seletivos com candidatos aprovados
     */
    public function getFinishedProcesses(): JsonResponse
    {
        // Buscar processos que tenham pelo menos um candidato aprovado
        $processIds = DB::table('selection_process_candidates')
            ->where('status', 'aprovado')
            ->distinct()
            ->pluck('selection_process_id');

        $processes = SelectionProcess::whereIn('selection_process_id', $processIds)
            ->whereNull('deleted_at')
            ->with('vacancy')
            ->orderBy('end_date', 'desc')
            ->orderBy('start_date', 'desc')
            ->get()
            ->map(function($process) {
                // Contar quantos candidatos aprovados tem neste processo
                $approvedCount = DB::table('selection_process_candidates')
                    ->where('selection_process_id', $process->selection_process_id)
                    ->where('status', 'aprovado')
                    ->count();

                return [
                    'id' => $process->selection_process_id,
                    'number' => $process->process_number,
                    'vacancy' => $process->vacancy->vacancy_title ?? 'N/A',
                    'status' => $process->status,
                    'end_date' => $process->end_date?->format('d/m/Y') ?? '-',
                    'approved_count' => $approvedCount,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $processes
        ]);
    }

    /**
     * Buscar candidatos aprovados de um processo
     */
    public function getApprovedCandidates($processId): JsonResponse
    {
        $process = SelectionProcess::whereNull('deleted_at')->findOrFail($processId);

        $candidates = DB::table('selection_process_candidates')
            ->where('selection_process_id', $processId)
            ->where('status', 'aprovado')
            ->join('candidates', 'selection_process_candidates.candidate_id', '=', 'candidates.candidate_id')
            ->select(
                'candidates.candidate_id',
                'candidates.candidate_name',
                'candidates.candidate_email',
                'candidates.candidate_phone',
                'selection_process_candidates.approved_at',
                'selection_process_candidates.approval_observation'
            )
            ->get()
            ->map(function($candidate) {
                return [
                    'candidate_id' => $candidate->candidate_id,
                    'candidate_name' => $candidate->candidate_name,
                    'candidate_email' => $candidate->candidate_email ?? '-',
                    'candidate_phone' => $candidate->candidate_phone ?? '-',
                    'approved_at' => $candidate->approved_at ? \Carbon\Carbon::parse($candidate->approved_at)->format('d/m/Y H:i') : '-',
                    'approval_observation' => $candidate->approval_observation ?? null,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $candidates
        ]);
    }

    /**
     * Criar nova proposta
     */
    public function storeProposal(Request $request, $processId): JsonResponse
    {
        $request->validate([
            'candidate_id' => 'required|integer|exists:candidates,candidate_id',
            'salary' => 'nullable|numeric|min:0',
            'contract_model' => 'nullable|string|max:100',
            'workload' => 'nullable|string|max:50',
            'benefits' => 'nullable|string',
            'start_date' => 'nullable|date',
            'additional_info' => 'nullable|string',
            'proposal_file' => 'nullable|file|mimes:pdf|max:10240', // 10MB max
        ]);

        $process = SelectionProcess::whereNull('deleted_at')->findOrFail($processId);
        
        // Verificar se candidato está aprovado no processo
        $pivot = DB::table('selection_process_candidates')
            ->where('selection_process_id', $processId)
            ->where('candidate_id', $request->input('candidate_id'))
            ->where('status', 'aprovado')
            ->first();

        if (!$pivot) {
            return response()->json([
                'success' => false,
                'message' => 'Candidato não está aprovado neste processo seletivo.'
            ], 400);
        }

        try {
            $user = Auth::user();
            $filePath = null;
            $fileName = null;

            // Upload do arquivo PDF se fornecido
            if ($request->hasFile('proposal_file')) {
                $file = $request->file('proposal_file');
                $fileName = $file->getClientOriginalName();
                $filePath = $file->store('proposals', 'public');
            }

            $proposal = Proposal::create([
                'selection_process_id' => $processId,
                'candidate_id' => $request->input('candidate_id'),
                'version' => 1,
                'parent_proposal_id' => null,
                'salary' => $request->input('salary'),
                'contract_model' => $request->input('contract_model'),
                'workload' => $request->input('workload'),
                'benefits' => $request->input('benefits'),
                'start_date' => $request->input('start_date'),
                'additional_info' => $request->input('additional_info'),
                'proposal_file_path' => $filePath,
                'proposal_file_name' => $fileName,
                'status' => 'pendente',
                'created_at' => now(),
                'created_by' => $user->user_name ?? 'system',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Proposta criada com sucesso!',
                'data' => $proposal
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar proposta: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Buscar propostas de um candidato
     */
    public function getProposals(Request $request, $processId): JsonResponse
    {
        $candidateId = $request->input('candidate_id');
        
        if (!$candidateId) {
            return response()->json([
                'success' => false,
                'message' => 'ID do candidato é obrigatório.'
            ], 400);
        }

        $proposals = Proposal::where('selection_process_id', $processId)
            ->where('candidate_id', $candidateId)
            ->orderBy('version', 'asc')
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function($proposal) {
                return [
                    'proposal_id' => $proposal->proposal_id,
                    'version' => $proposal->version,
                    'salary' => $proposal->salary ? number_format($proposal->salary, 2, ',', '.') : '-',
                    'contract_model' => $proposal->contract_model ?? '-',
                    'workload' => $proposal->workload ?? '-',
                    'benefits' => $proposal->benefits ?? '-',
                    'start_date' => $proposal->start_date?->format('d/m/Y') ?? '-',
                    'additional_info' => $proposal->additional_info ?? '-',
                    'proposal_file_name' => $proposal->proposal_file_name ?? null,
                    'proposal_file_path' => $proposal->proposal_file_path ? ('storage/' . $proposal->proposal_file_path) : null,
                    'status' => $proposal->status,
                    'rejection_observation' => $proposal->rejection_observation ?? null,
                    'created_at' => $proposal->created_at?->format('d/m/Y H:i') ?? '-',
                    'created_by' => $proposal->created_by ?? '-',
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $proposals
        ]);
    }

    /**
     * Aceitar proposta
     */
    public function acceptProposal(Request $request, $processId, $proposalId): JsonResponse
    {
        $proposal = Proposal::where('selection_process_id', $processId)
            ->findOrFail($proposalId);

        if ($proposal->status !== 'pendente') {
            return response()->json([
                'success' => false,
                'message' => 'Apenas propostas pendentes podem ser aceitas.'
            ], 400);
        }

        try {
            $user = Auth::user();

            $proposal->update([
                'status' => 'aceita',
                'accepted_at' => now(),
                'accepted_by' => $user->user_name ?? 'system',
                'updated_at' => now(),
                'updated_by' => $user->user_name ?? 'system',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Proposta aceita com sucesso!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao aceitar proposta: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Recusar proposta
     */
    public function rejectProposal(Request $request, $processId, $proposalId): JsonResponse
    {
        $request->validate([
            'observation' => 'nullable|string|max:1000',
        ]);

        $proposal = Proposal::where('selection_process_id', $processId)
            ->findOrFail($proposalId);

        if ($proposal->status !== 'pendente') {
            return response()->json([
                'success' => false,
                'message' => 'Apenas propostas pendentes podem ser recusadas.'
            ], 400);
        }

        try {
            $user = Auth::user();

            $proposal->update([
                'status' => 'recusada',
                'rejection_observation' => $request->input('observation'),
                'rejected_at' => now(),
                'rejected_by' => $user->user_name ?? 'system',
                'updated_at' => now(),
                'updated_by' => $user->user_name ?? 'system',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Proposta recusada com sucesso!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao recusar proposta: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Criar contraproposta
     */
    public function createCounterProposal(Request $request, $processId, $proposalId): JsonResponse
    {
        $request->validate([
            'salary' => 'nullable|numeric|min:0',
            'contract_model' => 'nullable|string|max:100',
            'workload' => 'nullable|string|max:50',
            'benefits' => 'nullable|string',
            'start_date' => 'nullable|date',
            'additional_info' => 'nullable|string',
            'proposal_file' => 'nullable|file|mimes:pdf|max:10240',
        ]);

        $originalProposal = Proposal::where('selection_process_id', $processId)
            ->findOrFail($proposalId);

        if ($originalProposal->status !== 'pendente') {
            return response()->json([
                'success' => false,
                'message' => 'Apenas propostas pendentes podem ter contrapropostas.'
            ], 400);
        }

        try {
            $user = Auth::user();
            $filePath = null;
            $fileName = null;

            // Upload do arquivo PDF se fornecido
            if ($request->hasFile('proposal_file')) {
                $file = $request->file('proposal_file');
                $fileName = $file->getClientOriginalName();
                $filePath = $file->store('proposals', 'public');
            }

            // Buscar última versão para determinar a próxima
            $lastVersion = Proposal::where('parent_proposal_id', $proposalId)
                ->orWhere('proposal_id', $proposalId)
                ->max('version');

            $newVersion = $lastVersion + 1;

            // Atualizar proposta original para contraproposta
            $originalProposal->update([
                'status' => 'contraproposta',
                'updated_at' => now(),
                'updated_by' => $user->user_name ?? 'system',
            ]);

            // Criar nova proposta (versão 2, 3, etc)
            $counterProposal = Proposal::create([
                'selection_process_id' => $originalProposal->selection_process_id,
                'candidate_id' => $originalProposal->candidate_id,
                'version' => $newVersion,
                'parent_proposal_id' => $proposalId,
                'salary' => $request->input('salary') ?? $originalProposal->salary,
                'contract_model' => $request->input('contract_model') ?? $originalProposal->contract_model,
                'workload' => $request->input('workload') ?? $originalProposal->workload,
                'benefits' => $request->input('benefits') ?? $originalProposal->benefits,
                'start_date' => $request->input('start_date') ?? $originalProposal->start_date,
                'additional_info' => $request->input('additional_info') ?? $originalProposal->additional_info,
                'proposal_file_path' => $filePath ?? $originalProposal->proposal_file_path,
                'proposal_file_name' => $fileName ?? $originalProposal->proposal_file_name,
                'status' => 'pendente',
                'created_at' => now(),
                'created_by' => $user->user_name ?? 'system',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Contraproposta criada com sucesso!',
                'data' => $counterProposal
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar contraproposta: ' . $e->getMessage()
            ], 500);
        }
    }
}
