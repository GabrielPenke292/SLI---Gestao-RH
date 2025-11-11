<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    /**
     * Nome da tabela
     */
    protected $table = 'activity_logs';

    /**
     * Nome da chave primária
     */
    protected $primaryKey = 'log_id';

    /**
     * Indica se o model deve usar timestamps automáticos
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'log_type',
        'entity_type',
        'entity_id',
        'action',
        'description',
        'metadata',
        'user_id',
        'user_name',
        'ip_address',
        'created_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Relacionamento: Um log pertence a um usuário (opcional)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'users_id');
    }
}
