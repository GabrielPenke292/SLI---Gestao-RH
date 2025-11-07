@extends('template.layout')

@section('content')

<div class="container mt-4">

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                
                <div class="card-body">
                    
                    <div class="row">
                        <div class="col-md-3 mb-3 p-3">
                            <a href="{{ route('employees.board') }}" class="btn btn-outline-primary w-100 p-5">
                                <i class="fas fa-users me-2"></i>Quadro de Funcionários
                            </a>
                        </div>

                        <div class="col-md-3 mb-3 p-3">
                            <a href="{{ route('employees.upload') }}" class="btn btn-outline-primary w-100 p-5">
                                <i class="fas fa-upload me-2"></i>Upload de base de funcionários
                            </a>
                        </div>

                        <div class="col-md-3 mb-3 p-3">
                            <a href="{{ route('employees.history') }}" class="btn btn-outline-primary w-100 p-5">
                                <i class="fas fa-history me-2"></i>Histórico de Funcionários antigos
                            </a>
                        </div>

                        <div class="col-md-3 mb-3 p-3">
                            <a href="{{ route('employees.calendar') }}" class="btn btn-outline-primary w-100 p-5">
                                <i class="fas fa-calendar-alt me-2"></i>Calendário de datas importantes
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection