<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\Worker;
use App\Models\Department;
use App\Models\Role;

class EmployeeUploadController extends Controller
{
    /**
     * Colunas esperadas na planilha (ordem e nomes)
     */
    private $expectedColumns = [
        'Nome',
        'Email',
        'CPF',
        'RG',
        'Data de Nascimento',
        'Data de Admissão',
        'Salário',
        'Status',
        'Departamento',
        'Cargo'
    ];

    public function index()
    {
        return view('employees.upload');
    }

    /**
     * Processa o upload da planilha e retorna preview dos dados
     */
    public function processUpload(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240', // 10MB max
        ], [
            'file.required' => 'Por favor, selecione um arquivo.',
            'file.mimes' => 'O arquivo deve ser do tipo Excel (.xlsx, .xls) ou CSV (.csv).',
            'file.max' => 'O arquivo não pode ser maior que 10MB.',
        ]);

        try {
            $file = $request->file('file');
            $spreadsheet = IOFactory::load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            if (count($rows) < 2) {
                return back()->with('error', 'A planilha deve conter pelo menos uma linha de dados (além do cabeçalho).');
            }

            // Primeira linha é o cabeçalho
            $header = array_map('trim', array_map('strval', $rows[0]));
            
            // Validar cabeçalho
            $headerValidation = $this->validateHeader($header);
            if (!$headerValidation['valid']) {
                return back()->with('error', 'Cabeçalho inválido: ' . $headerValidation['message']);
            }

            // Mapear índices das colunas
            $columnMap = $this->mapColumns($header);

            // Processar linhas de dados
            $processedData = [];
            $stats = [
                'total' => 0,
                'to_insert' => 0,
                'already_exists' => 0,
                'with_errors' => 0,
            ];

            // Buscar departamentos e roles existentes
            $departments = Department::whereNull('deleted_at')
                ->where('department_status', 1)
                ->pluck('department_id', 'department_name')
                ->mapWithKeys(function ($id, $name) {
                    return [strtolower(trim($name)) => $id];
                });

            $roles = Role::whereNull('deleted_at')
                ->where('role_status', 1)
                ->pluck('role_id', 'role_name')
                ->mapWithKeys(function ($id, $name) {
                    return [strtolower(trim($name)) => $id];
                });

            // Buscar emails existentes
            $existingEmails = Worker::whereNull('deleted_at')
                ->pluck('worker_email')
                ->map(function ($email) {
                    return strtolower(trim($email));
                })
                ->toArray();

            // Processar cada linha
            for ($i = 1; $i < count($rows); $i++) {
                $row = $rows[$i];
                $stats['total']++;

                $data = $this->processRow($row, $columnMap, $departments, $roles, $existingEmails, $i + 1);
                
                if ($data['status'] === 'error') {
                    $stats['with_errors']++;
                } elseif ($data['status'] === 'exists') {
                    $stats['already_exists']++;
                } else {
                    $stats['to_insert']++;
                }

                $processedData[] = $data;
            }

            // Salvar dados processados na sessão
            Session::put('upload_data', $processedData);
            Session::put('upload_stats', $stats);

            return view('employees.upload-preview', compact('processedData', 'stats'));

        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao processar arquivo: ' . $e->getMessage());
        }
    }

    /**
     * Valida o cabeçalho da planilha
     */
    private function validateHeader($header)
    {
        $headerLower = array_map('strtolower', $header);
        
        foreach ($this->expectedColumns as $expected) {
            $expectedLower = strtolower($expected);
            if (!in_array($expectedLower, $headerLower)) {
                return [
                    'valid' => false,
                    'message' => "Coluna '{$expected}' não encontrada no cabeçalho."
                ];
            }
        }

        return ['valid' => true];
    }

    /**
     * Mapeia os índices das colunas
     */
    private function mapColumns($header)
    {
        $map = [];
        $headerLower = array_map('strtolower', $header);

        foreach ($this->expectedColumns as $expected) {
            $expectedLower = strtolower($expected);
            $index = array_search($expectedLower, $headerLower);
            if ($index !== false) {
                $map[$expected] = $index;
            }
        }

        return $map;
    }

    /**
     * Processa uma linha da planilha
     */
    private function processRow($row, $columnMap, $departments, $roles, $existingEmails, $lineNumber)
    {
        $data = [
            'line' => $lineNumber,
            'status' => 'ok',
            'errors' => [],
            'worker_name' => trim($row[$columnMap['Nome']] ?? ''),
            'worker_email' => trim($row[$columnMap['Email']] ?? ''),
            'worker_document' => trim($row[$columnMap['CPF']] ?? ''),
            'worker_rg' => trim($row[$columnMap['RG']] ?? ''),
            'worker_birth_date' => $this->parseDate($row[$columnMap['Data de Nascimento']] ?? ''),
            'worker_start_date' => $this->parseDate($row[$columnMap['Data de Admissão']] ?? ''),
            'worker_salary' => $this->parseSalary($row[$columnMap['Salário']] ?? ''),
            'worker_status' => $this->parseStatus($row[$columnMap['Status']] ?? ''),
            'department_name' => trim($row[$columnMap['Departamento']] ?? ''),
            'role_names' => $this->parseRoles($row[$columnMap['Cargo']] ?? ''),
        ];

        // Validações
        if (empty($data['worker_name'])) {
            $data['errors'][] = 'Nome é obrigatório';
        }

        if (empty($data['worker_email'])) {
            $data['errors'][] = 'Email é obrigatório';
        } elseif (!filter_var($data['worker_email'], FILTER_VALIDATE_EMAIL)) {
            $data['errors'][] = 'Email inválido';
        } elseif (in_array(strtolower($data['worker_email']), $existingEmails)) {
            $data['status'] = 'exists';
            $data['errors'][] = 'Email já cadastrado';
        }

        if (empty($data['worker_document'])) {
            $data['errors'][] = 'CPF é obrigatório';
        }

        if (empty($data['worker_birth_date'])) {
            $data['errors'][] = 'Data de nascimento inválida';
        }

        if (empty($data['worker_start_date'])) {
            $data['errors'][] = 'Data de admissão inválida';
        }

        if ($data['worker_salary'] === null) {
            $data['errors'][] = 'Salário inválido';
        }

        if ($data['worker_status'] === null) {
            $data['errors'][] = 'Status inválido (deve ser Ativo ou Inativo)';
        }

        // Validar departamento
        $departmentKey = strtolower(trim($data['department_name']));
        if (empty($data['department_name'])) {
            $data['errors'][] = 'Departamento é obrigatório';
        } elseif (!isset($departments[$departmentKey])) {
            $data['errors'][] = 'Departamento não encontrado: ' . $data['department_name'];
        } else {
            $data['department_id'] = $departments[$departmentKey];
        }

        // Validar cargo(s)
        if (empty($data['role_names'])) {
            $data['errors'][] = 'Cargo é obrigatório';
        } else {
            $roleIds = [];
            foreach ($data['role_names'] as $roleName) {
                $roleKey = strtolower(trim($roleName));
                if (!isset($roles[$roleKey])) {
                    $data['errors'][] = 'Cargo não encontrado: ' . $roleName;
                } else {
                    $roleIds[] = $roles[$roleKey];
                }
            }
            if (empty($roleIds)) {
                $data['errors'][] = 'Nenhum cargo válido encontrado';
            } else {
                $data['role_ids'] = $roleIds;
            }
        }

        // Se houver erros, marcar como erro
        if (!empty($data['errors']) && $data['status'] !== 'exists') {
            $data['status'] = 'error';
        }

        return $data;
    }

    /**
     * Converte data de vários formatos para Y-m-d
     */
    private function parseDate($value)
    {
        if (empty($value)) {
            return null;
        }

        // Se for um número (Excel serial date)
        if (is_numeric($value)) {
            try {
                $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);
                return $date->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        }

        // Tentar vários formatos
        $formats = ['Y-m-d', 'd/m/Y', 'd-m-Y', 'Y/m/d', 'd.m.Y'];
        
        foreach ($formats as $format) {
            $date = \DateTime::createFromFormat($format, trim($value));
            if ($date !== false) {
                return $date->format('Y-m-d');
            }
        }

        // Tentar strtotime como último recurso
        $timestamp = strtotime($value);
        if ($timestamp !== false) {
            return date('Y-m-d', $timestamp);
        }

        return null;
    }

    /**
     * Converte salário para decimal
     */
    private function parseSalary($value)
    {
        if (empty($value)) {
            return null;
        }

        // Remover formatação
        $value = str_replace(['R$', '$', ' ', '.'], '', $value);
        $value = str_replace(',', '.', $value);

        $salary = floatval($value);
        return $salary > 0 ? $salary : null;
    }

    /**
     * Converte status para inteiro
     */
    private function parseStatus($value)
    {
        $value = strtolower(trim($value));
        
        if (in_array($value, ['ativo', '1', 'sim', 'yes', 'true'])) {
            return 1;
        } elseif (in_array($value, ['inativo', '0', 'não', 'nao', 'no', 'false'])) {
            return 0;
        }

        return null;
    }

    /**
     * Processa cargo(s) - pode ser múltiplos separados por vírgula ou ponto e vírgula
     */
    private function parseRoles($value)
    {
        if (empty($value)) {
            return [];
        }

        // Separar por vírgula ou ponto e vírgula
        $roles = preg_split('/[,;]/', $value);
        return array_map('trim', array_filter($roles));
    }

    /**
     * Salva os dados após confirmação do usuário
     */
    public function confirmStore(Request $request)
    {
        $processedData = Session::get('upload_data');
        $stats = Session::get('upload_stats');

        if (!$processedData || empty($processedData)) {
            return redirect()->route('employees.upload')
                ->with('error', 'Nenhum dado para salvar. Por favor, faça o upload novamente.');
        }

        try {
            DB::beginTransaction();

            $saved = 0;
            $skipped = 0;
            $errors = 0;

            foreach ($processedData as $data) {
                // Pular registros com erro ou que já existem
                if ($data['status'] === 'error' || $data['status'] === 'exists') {
                    $skipped++;
                    continue;
                }

                try {
                    // Criar funcionário
                    $worker = Worker::create([
                        'worker_name' => $data['worker_name'],
                        'worker_email' => $data['worker_email'],
                        'worker_document' => $data['worker_document'],
                        'worker_rg' => $data['worker_rg'] ?: null,
                        'worker_birth_date' => $data['worker_birth_date'],
                        'worker_start_date' => $data['worker_start_date'],
                        'worker_status' => $data['worker_status'],
                        'worker_salary' => $data['worker_salary'],
                        'department_id' => $data['department_id'],
                        'created_by' => Auth::user()->name ?? 'system',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    // Associar roles
                    if (!empty($data['role_ids'])) {
                        foreach ($data['role_ids'] as $roleId) {
                            $worker->roles()->attach($roleId, [
                                'worker_role_status' => 1,
                                'created_at' => now(),
                                'created_by' => Auth::user()->name ?? 'system',
                            ]);
                        }
                    }

                    $saved++;
                } catch (\Exception $e) {
                    $errors++;
                    Log::error('Erro ao salvar funcionário da linha ' . $data['line'] . ': ' . $e->getMessage());
                }
            }

            DB::commit();

            // Limpar dados da sessão
            Session::forget('upload_data');
            Session::forget('upload_stats');

            return redirect()->route('employees.board')
                ->with('success', "Importação concluída! {$saved} funcionário(s) cadastrado(s) com sucesso. {$skipped} registro(s) ignorado(s).");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('employees.upload')
                ->with('error', 'Erro ao salvar dados: ' . $e->getMessage());
        }
    }
}
