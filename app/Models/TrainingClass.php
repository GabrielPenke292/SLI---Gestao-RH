<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TrainingClass extends Model
{
    use SoftDeletes;

    /**
     * Nome da tabela
     */
    protected $table = 'training_classes';

    /**
     * Nome da chave primária
     */
    protected $primaryKey = 'training_class_id';

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
        'start_date',
        'end_date',
        'status',
        'max_participants',
        'instructor',
        'notes',
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
        'start_date' => 'date',
        'end_date' => 'date',
        'max_participants' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relacionamento: Uma turma tem muitos tópicos
     */
    public function topics(): HasMany
    {
        return $this->hasMany(TrainingTopic::class, 'training_class_id', 'training_class_id')
            ->whereNull('deleted_at')
            ->orderBy('order');
    }
}
