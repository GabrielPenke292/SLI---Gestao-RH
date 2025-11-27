<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TrainingContent extends Model
{
    use SoftDeletes;

    /**
     * Nome da tabela
     */
    protected $table = 'training_contents';

    /**
     * Nome da chave primária
     */
    protected $primaryKey = 'training_content_id';

    /**
     * Indica se o model deve usar timestamps automáticos
     */
    public $timestamps = false;

    /**
     * Nome da coluna de soft delete
     */
    const DELETED_AT = 'deleted_at';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'content_type',
        'file_path',
        'file_name',
        'file_size',
        'youtube_url',
        'youtube_video_id',
        'category',
        'duration_minutes',
        'is_active',
        'views_count',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'views_count' => 'integer',
        'duration_minutes' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Extrair ID do vídeo do YouTube da URL
     */
    public static function extractYoutubeId($url)
    {
        if (empty($url)) {
            return null;
        }

        // Padrões comuns de URLs do YouTube
        $patterns = [
            '/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([a-zA-Z0-9_-]{11})/',
            '/youtube\.com\/watch\?.*v=([a-zA-Z0-9_-]{11})/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url, $matches)) {
                return $matches[1];
            }
        }

        return null;
    }

    /**
     * Obter URL de embed do YouTube
     */
    public function getYoutubeEmbedUrlAttribute()
    {
        if ($this->content_type === 'youtube_link' && $this->youtube_video_id) {
            return 'https://www.youtube.com/embed/' . $this->youtube_video_id;
        }
        return null;
    }

    /**
     * Relacionamento: Um conteúdo pode estar em muitos tópicos
     */
    public function topics(): BelongsToMany
    {
        return $this->belongsToMany(
            TrainingTopic::class,
            'training_topic_contents',
            'training_content_id',
            'training_topic_id',
            'training_content_id',
            'training_topic_id'
        )->withPivot('order');
    }
}
