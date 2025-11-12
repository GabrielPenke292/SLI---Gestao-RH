<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documento de Exame Demissional</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            color: #333;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .section {
            margin-bottom: 25px;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
        }
        .info-row {
            margin-bottom: 8px;
        }
        .info-label {
            font-weight: bold;
            display: inline-block;
            width: 150px;
        }
        .info-value {
            display: inline-block;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ccc;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>HERO - Human Engagement & Resource Optimizer</h1>
        <p>Sistema de Gestão de Recursos Humanos</p>
        <p><strong>DOCUMENTO PARA EXAME DEMISSIONAL</strong></p>
    </div>

    <div class="section">
        <div class="section-title">DADOS DO FUNCIONÁRIO</div>
        <div class="info-row">
            <span class="info-label">Nome:</span>
            <span class="info-value">{{ $worker->worker_name ?? 'N/A' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">E-mail:</span>
            <span class="info-value">{{ $worker->worker_email ?? 'N/A' }}</span>
        </div>
        @if($worker->worker_document)
        <div class="info-row">
            <span class="info-label">CPF:</span>
            <span class="info-value">{{ $worker->worker_document }}</span>
        </div>
        @endif
        @if($worker->worker_birth_date)
        <div class="info-row">
            <span class="info-label">Data de Nascimento:</span>
            <span class="info-value">{{ $worker->worker_birth_date->format('d/m/Y') }}</span>
        </div>
        @endif
        @if($worker->department)
        <div class="info-row">
            <span class="info-label">Departamento:</span>
            <span class="info-value">{{ $worker->department->department_name }}</span>
        </div>
        @endif
        @if($worker->roles && $worker->roles->count() > 0)
        <div class="info-row">
            <span class="info-label">Cargo(s):</span>
            <span class="info-value">{{ $worker->roles->pluck('role_name')->implode(', ') }}</span>
        </div>
        @endif
    </div>

    @if($layoff)
    <div class="section">
        <div class="section-title">DADOS DO DESLIGAMENTO</div>
        <div class="info-row">
            <span class="info-label">Data de Desligamento:</span>
            <span class="info-value">{{ $layoff->layoff_date->format('d/m/Y') ?? 'N/A' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Tipo de Desligamento:</span>
            <span class="info-value">
                @php
                    $typeLabels = [
                        'pedido_demissao' => 'Pedido de Demissão',
                        'demitido' => 'Demitido',
                        'rescisao_indireta' => 'Rescisão Indireta',
                        'justa_causa' => 'Justa Causa',
                        'outro' => 'Outro'
                    ];
                @endphp
                {{ $typeLabels[$layoff->layoff_type] ?? $layoff->layoff_type }}
            </span>
        </div>
        @if($layoff->reason)
        <div class="info-row">
            <span class="info-label">Motivo:</span>
            <span class="info-value">{{ $layoff->reason }}</span>
        </div>
        @endif
    </div>
    @endif

    <div class="section">
        <div class="section-title">DADOS DO AGENDAMENTO</div>
        <div class="info-row">
            <span class="info-label">Data do Exame:</span>
            <span class="info-value">{{ $exam->exam_date->format('d/m/Y') ?? 'N/A' }}</span>
        </div>
        @if($exam->exam_time)
        <div class="info-row">
            <span class="info-label">Horário:</span>
            <span class="info-value">{{ \Carbon\Carbon::parse($exam->exam_time)->format('H:i') }}</span>
        </div>
        @endif
        <div class="info-row">
            <span class="info-label">Clínica:</span>
            <span class="info-value">{{ $clinic->corporate_name ?? 'N/A' }}</span>
        </div>
        @if($clinic->trade_name)
        <div class="info-row">
            <span class="info-label">Nome Fantasia:</span>
            <span class="info-value">{{ $clinic->trade_name }}</span>
        </div>
        @endif
        @if($clinic->address)
        <div class="info-row">
            <span class="info-label">Endereço:</span>
            <span class="info-value">
                {{ $clinic->address }}
                @if($clinic->address_number), {{ $clinic->address_number }}@endif
                @if($clinic->address_complement) - {{ $clinic->address_complement }}@endif
                @if($clinic->neighborhood) - {{ $clinic->neighborhood }}@endif
                @if($clinic->city) - {{ $clinic->city }}@endif
                @if($clinic->state)/{{ $clinic->state }}@endif
                @if($clinic->zip_code) - CEP: {{ $clinic->formatted_zip_code }}@endif
            </span>
        </div>
        @endif
        @if($clinic->phone)
        <div class="info-row">
            <span class="info-label">Telefone da Clínica:</span>
            <span class="info-value">{{ $clinic->formatted_phone }}</span>
        </div>
        @endif
    </div>

    @if($exam->notes)
    <div class="section">
        <div class="section-title">OBSERVAÇÕES</div>
        <p>{{ $exam->notes }}</p>
    </div>
    @endif

    <div class="footer">
        <p>Documento gerado em {{ now()->format('d/m/Y H:i') }}</p>
        <p>HERO - Human Engagement & Resource Optimizer</p>
    </div>
</body>
</html>

