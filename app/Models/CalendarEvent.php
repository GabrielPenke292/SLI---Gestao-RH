<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CalendarEvent extends Model
{
    use SoftDeletes;

    /**
     * Nome da tabela
     */
    protected $table = 'calendar_events';

    /**
     * Nome da chave primária
     */
    protected $primaryKey = 'event_id';

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
        'event_title',
        'event_description',
        'event_date',
        'event_start_time',
        'event_end_time',
        'event_type',
        'worker_id',
        'event_color',
        'event_status',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'event_date' => 'date',
        'event_status' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Relacionamento: Um evento pode pertencer a um funcionário
     */
    public function worker(): BelongsTo
    {
        return $this->belongsTo(Worker::class, 'worker_id', 'worker_id');
    }
}
