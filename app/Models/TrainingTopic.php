<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TrainingTopic extends Model
{
    use SoftDeletes;

    /**
     * Nome da tabela
     */
    protected $table = 'training_topics';

    /**
     * Nome da chave primária
     */
    protected $primaryKey = 'training_topic_id';

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
        'training_class_id',
        'title',
        'description',
        'order',
        'duration_minutes',
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
        'order' => 'integer',
        'duration_minutes' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relacionamento: Um tópico pertence a uma turma
     */
    public function trainingClass(): BelongsTo
    {
        return $this->belongsTo(TrainingClass::class, 'training_class_id', 'training_class_id');
    }

    /**
     * Relacionamento: Um tópico tem muitos conteúdos
     */
    public function contents(): BelongsToMany
    {
        return $this->belongsToMany(
            TrainingContent::class,
            'training_topic_contents',
            'training_topic_id',
            'training_content_id',
            'training_topic_id',
            'training_content_id'
        )->withPivot('order', 'created_at', 'created_by')
          ->orderBy('training_topic_contents.order');
    }
}
