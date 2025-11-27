<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\TrainingContent;
use App\Models\TrainingClass;
use App\Models\TrainingTopic;

class TrainingController extends Controller
{
    public function index(){
        return view('training.index');
    }

    public function contents(){
        return view('training.contents');
    }

    /**
     * Buscar todos os conteúdos de treinamento
     */
    public function getContentsData(Request $request): JsonResponse
    {
        $search = $request->input('search', '');
        $contentType = $request->input('content_type', '');
        $category = $request->input('category', '');

        $query = TrainingContent::whereNull('deleted_at');

        // Filtro de busca
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%')
                  ->orWhere('category', 'like', '%' . $search . '%');
            });
        }

        // Filtro por tipo
        if (!empty($contentType)) {
            $query->where('content_type', $contentType);
        }

        // Filtro por categoria
        if (!empty($category)) {
            $query->where('category', $category);
        }

        $contents = $query->orderBy('created_at', 'desc')
            ->get()
            ->map(function($content) {
                $typeLabels = [
                    'pdf' => 'PDF',
                    'excel' => 'Excel',
                    'powerpoint' => 'PowerPoint',
                    'video_file' => 'Vídeo',
                    'youtube_link' => 'YouTube'
                ];

                $typeIcons = [
                    'pdf' => 'fa-file-pdf',
                    'excel' => 'fa-file-excel',
                    'powerpoint' => 'fa-file-powerpoint',
                    'video_file' => 'fa-video',
                    'youtube_link' => 'fa-youtube'
                ];

                return [
                    'training_content_id' => $content->training_content_id,
                    'title' => $content->title,
                    'description' => $content->description,
                    'content_type' => $content->content_type,
                    'content_type_label' => $typeLabels[$content->content_type] ?? $content->content_type,
                    'content_type_icon' => $typeIcons[$content->content_type] ?? 'fa-file',
                    'file_name' => $content->file_name,
                    'file_size' => $content->file_size,
                    'youtube_url' => $content->youtube_url,
                    'youtube_video_id' => $content->youtube_video_id,
                    'category' => $content->category,
                    'duration_minutes' => $content->duration_minutes,
                    'is_active' => $content->is_active,
                    'views_count' => $content->views_count,
                    'created_at' => $content->created_at?->format('d/m/Y H:i') ?? '-',
                    'created_by' => $content->created_by ?? '-',
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $contents
        ]);
    }

    /**
     * Buscar categorias únicas
     */
    public function getCategories(): JsonResponse
    {
        $categories = TrainingContent::whereNull('deleted_at')
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->pluck('category')
            ->sort()
            ->values();

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    /**
     * Salvar novo conteúdo
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'content_type' => 'required|in:pdf,excel,powerpoint,video_file,youtube_link',
            'file' => 'required_if:content_type,pdf,excel,powerpoint,video_file|file|mimes:pdf,xlsx,xls,pptx,ppt,mp4,avi,mov,wmv|max:102400', // 100MB max
            'youtube_url' => 'required_if:content_type,youtube_link|nullable|url',
            'category' => 'nullable|string|max:100',
            'duration_minutes' => 'nullable|integer|min:0',
        ], [
            'title.required' => 'O título é obrigatório.',
            'content_type.required' => 'O tipo de conteúdo é obrigatório.',
            'file.required_if' => 'O arquivo é obrigatório para este tipo de conteúdo.',
            'youtube_url.required_if' => 'A URL do YouTube é obrigatória para links de vídeo.',
            'youtube_url.url' => 'A URL do YouTube deve ser uma URL válida.',
        ]);

        try {
            $user = Auth::user();
            $contentType = $request->input('content_type');

            $data = [
                'title' => $request->input('title'),
                'description' => $request->input('description'),
                'content_type' => $contentType,
                'category' => $request->input('category'),
                'duration_minutes' => $request->input('duration_minutes'),
                'is_active' => true,
                'views_count' => 0,
                'created_at' => now(),
                'created_by' => $user->user_name ?? 'system',
                'updated_at' => now(),
                'updated_by' => $user->user_name ?? 'system',
            ];

            // Processar arquivo ou URL do YouTube
            if (in_array($contentType, ['pdf', 'excel', 'powerpoint', 'video_file'])) {
                if ($request->hasFile('file')) {
                    $file = $request->file('file');
                    $fileName = 'content_' . time() . '_' . $file->getClientOriginalName();
                    
                    // Determinar pasta baseado no tipo
                    $folder = 'training_contents';
                    if ($contentType === 'pdf') {
                        $folder = 'training_contents/pdf';
                    } elseif ($contentType === 'excel') {
                        $folder = 'training_contents/excel';
                    } elseif ($contentType === 'powerpoint') {
                        $folder = 'training_contents/powerpoint';
                    } elseif ($contentType === 'video_file') {
                        $folder = 'training_contents/videos';
                    }
                    
                    $filePath = $file->storeAs($folder, $fileName, 'public');
                    
                    $data['file_path'] = $filePath;
                    $data['file_name'] = $file->getClientOriginalName();
                    $data['file_size'] = $this->formatFileSize($file->getSize());
                }
            } elseif ($contentType === 'youtube_link') {
                $youtubeUrl = $request->input('youtube_url');
                $youtubeId = TrainingContent::extractYoutubeId($youtubeUrl);
                
                if (!$youtubeId) {
                    return response()->json([
                        'success' => false,
                        'message' => 'URL do YouTube inválida. Por favor, verifique o link.'
                    ], 422);
                }

                $data['youtube_url'] = $youtubeUrl;
                $data['youtube_video_id'] = $youtubeId;
            }

            $content = TrainingContent::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Conteúdo criado com sucesso!',
                'data' => $content
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar conteúdo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Buscar um conteúdo específico
     */
    public function getContent($id): JsonResponse
    {
        $content = TrainingContent::whereNull('deleted_at')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'training_content_id' => $content->training_content_id,
                'title' => $content->title,
                'description' => $content->description,
                'content_type' => $content->content_type,
                'file_name' => $content->file_name,
                'youtube_url' => $content->youtube_url,
                'category' => $content->category,
                'duration_minutes' => $content->duration_minutes,
                'is_active' => $content->is_active,
            ]
        ]);
    }

    /**
     * Atualizar conteúdo
     */
    public function update(Request $request, $id): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'duration_minutes' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        try {
            $user = Auth::user();
            $content = TrainingContent::whereNull('deleted_at')->findOrFail($id);

            $content->update([
                'title' => $request->input('title'),
                'description' => $request->input('description'),
                'category' => $request->input('category'),
                'duration_minutes' => $request->input('duration_minutes'),
                'is_active' => $request->input('is_active', true),
                'updated_at' => now(),
                'updated_by' => $user->user_name ?? 'system',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Conteúdo atualizado com sucesso!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar conteúdo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Excluir conteúdo (soft delete)
     */
    public function delete($id): JsonResponse
    {
        try {
            $content = TrainingContent::whereNull('deleted_at')->findOrFail($id);
            
            // Opcional: deletar arquivo físico também
            if ($content->file_path && Storage::disk('public')->exists($content->file_path)) {
                Storage::disk('public')->delete($content->file_path);
            }

            $content->delete();

            return response()->json([
                'success' => true,
                'message' => 'Conteúdo excluído com sucesso!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao excluir conteúdo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download do arquivo
     */
    public function downloadFile($id)
    {
        $content = TrainingContent::whereNull('deleted_at')
            ->whereIn('content_type', ['pdf', 'excel', 'powerpoint', 'video_file'])
            ->findOrFail($id);

        if (!$content->file_path || !Storage::disk('public')->exists($content->file_path)) {
            abort(404, 'Arquivo não encontrado');
        }

        $filePath = Storage::disk('public')->path($content->file_path);
        $fileName = $content->file_name ?? 'conteudo.' . pathinfo($content->file_path, PATHINFO_EXTENSION);
        
        return response()->download($filePath, $fileName);
    }

    /**
     * Formatar tamanho do arquivo
     */
    private function formatFileSize($bytes)
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    public function trainings(){
        return view('training.trainings');
    }

    /**
     * Buscar todas as turmas
     */
    public function getClassesData(Request $request): JsonResponse
    {
        $search = $request->input('search', '');

        $query = TrainingClass::whereNull('deleted_at');

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%')
                  ->orWhere('instructor', 'like', '%' . $search . '%');
            });
        }

        $classes = $query->withCount('topics')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($class) {
                $statusLabels = [
                    'planejado' => 'Planejado',
                    'em_andamento' => 'Em Andamento',
                    'concluido' => 'Concluído',
                    'cancelado' => 'Cancelado'
                ];

                $statusColors = [
                    'planejado' => 'secondary',
                    'em_andamento' => 'primary',
                    'concluido' => 'success',
                    'cancelado' => 'danger'
                ];

                return [
                    'training_class_id' => $class->training_class_id,
                    'title' => $class->title,
                    'description' => $class->description,
                    'start_date' => $class->start_date?->format('d/m/Y') ?? '-',
                    'end_date' => $class->end_date?->format('d/m/Y') ?? '-',
                    'status' => $class->status,
                    'status_label' => $statusLabels[$class->status] ?? $class->status,
                    'status_color' => $statusColors[$class->status] ?? 'secondary',
                    'max_participants' => $class->max_participants,
                    'instructor' => $class->instructor,
                    'topics_count' => $class->topics_count ?? 0,
                    'created_at' => $class->created_at?->format('d/m/Y H:i') ?? '-',
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $classes
        ]);
    }

    /**
     * Salvar nova turma
     */
    public function storeClass(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:planejado,em_andamento,concluido,cancelado',
            'max_participants' => 'nullable|integer|min:1',
            'instructor' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        try {
            $user = Auth::user();

            $class = TrainingClass::create([
                'title' => $request->input('title'),
                'description' => $request->input('description'),
                'start_date' => $request->input('start_date'),
                'end_date' => $request->input('end_date'),
                'status' => $request->input('status'),
                'max_participants' => $request->input('max_participants'),
                'instructor' => $request->input('instructor'),
                'notes' => $request->input('notes'),
                'created_at' => now(),
                'created_by' => $user->user_name ?? 'system',
                'updated_at' => now(),
                'updated_by' => $user->user_name ?? 'system',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Turma criada com sucesso!',
                'data' => $class
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar turma: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Buscar uma turma específica com tópicos
     */
    public function getClass($id): JsonResponse
    {
        $class = TrainingClass::whereNull('deleted_at')
            ->with(['topics.contents' => function($query) {
                $query->whereNull('training_contents.deleted_at');
            }])
            ->findOrFail($id);

        $topics = $class->topics->map(function($topic) {
            return [
                'training_topic_id' => $topic->training_topic_id,
                'title' => $topic->title,
                'description' => $topic->description,
                'order' => $topic->order,
                'duration_minutes' => $topic->duration_minutes,
                'contents' => $topic->contents->map(function($content) {
                    return [
                        'training_content_id' => $content->training_content_id,
                        'title' => $content->title,
                        'content_type' => $content->content_type,
                        'order' => $content->pivot->order ?? 0,
                    ];
                })->sortBy('order')->values(),
            ];
        })->sortBy('order')->values();

        return response()->json([
            'success' => true,
            'data' => [
                'training_class_id' => $class->training_class_id,
                'title' => $class->title,
                'description' => $class->description,
                'start_date' => $class->start_date?->format('Y-m-d'),
                'end_date' => $class->end_date?->format('Y-m-d'),
                'status' => $class->status,
                'max_participants' => $class->max_participants,
                'instructor' => $class->instructor,
                'notes' => $class->notes,
                'topics' => $topics,
            ]
        ]);
    }

    /**
     * Atualizar turma
     */
    public function updateClass(Request $request, $id): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:planejado,em_andamento,concluido,cancelado',
            'max_participants' => 'nullable|integer|min:1',
            'instructor' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        try {
            $user = Auth::user();
            $class = TrainingClass::whereNull('deleted_at')->findOrFail($id);

            $class->update([
                'title' => $request->input('title'),
                'description' => $request->input('description'),
                'start_date' => $request->input('start_date'),
                'end_date' => $request->input('end_date'),
                'status' => $request->input('status'),
                'max_participants' => $request->input('max_participants'),
                'instructor' => $request->input('instructor'),
                'notes' => $request->input('notes'),
                'updated_at' => now(),
                'updated_by' => $user->user_name ?? 'system',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Turma atualizada com sucesso!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar turma: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Excluir turma
     */
    public function deleteClass($id): JsonResponse
    {
        try {
            $class = TrainingClass::whereNull('deleted_at')->findOrFail($id);
            $class->delete();

            return response()->json([
                'success' => true,
                'message' => 'Turma excluída com sucesso!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao excluir turma: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Salvar tópico
     */
    public function storeTopic(Request $request): JsonResponse
    {
        $request->validate([
            'training_class_id' => 'required|exists:training_classes,training_class_id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
            'duration_minutes' => 'nullable|integer|min:0',
        ]);

        try {
            $user = Auth::user();

            $topic = TrainingTopic::create([
                'training_class_id' => $request->input('training_class_id'),
                'title' => $request->input('title'),
                'description' => $request->input('description'),
                'order' => $request->input('order', 0),
                'duration_minutes' => $request->input('duration_minutes'),
                'created_at' => now(),
                'created_by' => $user->user_name ?? 'system',
                'updated_at' => now(),
                'updated_by' => $user->user_name ?? 'system',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tópico criado com sucesso!',
                'data' => $topic
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar tópico: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Atualizar tópico
     */
    public function updateTopic(Request $request, $id): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
            'duration_minutes' => 'nullable|integer|min:0',
        ]);

        try {
            $user = Auth::user();
            $topic = TrainingTopic::whereNull('deleted_at')->findOrFail($id);

            $topic->update([
                'title' => $request->input('title'),
                'description' => $request->input('description'),
                'order' => $request->input('order', $topic->order),
                'duration_minutes' => $request->input('duration_minutes'),
                'updated_at' => now(),
                'updated_by' => $user->user_name ?? 'system',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tópico atualizado com sucesso!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar tópico: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Excluir tópico
     */
    public function deleteTopic($id): JsonResponse
    {
        try {
            $topic = TrainingTopic::whereNull('deleted_at')->findOrFail($id);
            $topic->delete();

            return response()->json([
                'success' => true,
                'message' => 'Tópico excluído com sucesso!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao excluir tópico: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Adicionar conteúdo a um tópico
     */
    public function addContentToTopic(Request $request): JsonResponse
    {
        $request->validate([
            'training_topic_id' => 'required|exists:training_topics,training_topic_id',
            'training_content_id' => 'required|exists:training_contents,training_content_id',
            'order' => 'nullable|integer|min:0',
        ]);

        try {
            $user = Auth::user();
            $topic = TrainingTopic::whereNull('deleted_at')->findOrFail($request->input('training_topic_id'));
            $content = TrainingContent::whereNull('deleted_at')->findOrFail($request->input('training_content_id'));

            // Verificar se já existe
            if ($topic->contents()->where('training_content_id', $content->training_content_id)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este conteúdo já está vinculado a este tópico.'
                ], 422);
            }

            $topic->contents()->attach($content->training_content_id, [
                'order' => $request->input('order', 0),
                'created_at' => now(),
                'created_by' => $user->user_name ?? 'system',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Conteúdo adicionado ao tópico com sucesso!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao adicionar conteúdo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remover conteúdo de um tópico
     */
    public function removeContentFromTopic(Request $request, $topicId, $contentId): JsonResponse
    {
        try {
            $topic = TrainingTopic::whereNull('deleted_at')->findOrFail($topicId);
            $topic->contents()->detach($contentId);

            return response()->json([
                'success' => true,
                'message' => 'Conteúdo removido do tópico com sucesso!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao remover conteúdo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Buscar conteúdos disponíveis (para seleção em tópicos)
     */
    public function getAvailableContents(): JsonResponse
    {
        $contents = TrainingContent::whereNull('deleted_at')
            ->where('is_active', true)
            ->orderBy('title')
            ->get()
            ->map(function($content) {
                $typeLabels = [
                    'pdf' => 'PDF',
                    'excel' => 'Excel',
                    'powerpoint' => 'PowerPoint',
                    'video_file' => 'Vídeo',
                    'youtube_link' => 'YouTube'
                ];

                return [
                    'training_content_id' => $content->training_content_id,
                    'title' => $content->title,
                    'content_type' => $content->content_type,
                    'content_type_label' => $typeLabels[$content->content_type] ?? $content->content_type,
                    'category' => $content->category,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $contents
        ]);
    }
}
