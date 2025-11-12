<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Clinic extends Model
{
    use SoftDeletes;

    /**
     * Nome da tabela
     */
    protected $table = 'clinics';

    /**
     * Nome da chave primária
     */
    protected $primaryKey = 'clinic_id';

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
        'corporate_name',
        'trade_name',
        'cnpj',
        'email',
        'phone',
        'address',
        'address_number',
        'address_complement',
        'neighborhood',
        'city',
        'state',
        'zip_code',
        'notes',
        'is_active',
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
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Formatar CNPJ
     */
    public function getFormattedCnpjAttribute(): string
    {
        $cnpj = preg_replace('/\D/', '', $this->cnpj);
        return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $cnpj);
    }

    /**
     * Formatar telefone
     */
    public function getFormattedPhoneAttribute(): string
    {
        $phone = preg_replace('/\D/', '', $this->phone ?? '');
        if (strlen($phone) === 11) {
            return preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $phone);
        } elseif (strlen($phone) === 10) {
            return preg_replace('/(\d{2})(\d{4})(\d{4})/', '($1) $2-$3', $phone);
        }
        return $this->phone ?? '';
    }

    /**
     * Formatar CEP
     */
    public function getFormattedZipCodeAttribute(): string
    {
        $zipCode = preg_replace('/\D/', '', $this->zip_code ?? '');
        if (strlen($zipCode) === 8) {
            return preg_replace('/(\d{5})(\d{3})/', '$1-$2', $zipCode);
        }
        return $this->zip_code ?? '';
    }
}
