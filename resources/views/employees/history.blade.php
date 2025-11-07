@extends('template.layout')

@push('styles')
<style>
    
</style>
@endpush

@section('content')

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0"><i class="fas fa-history me-2"></i>Histórico de Funcionários antigos</h4>
                        <small class="text-white">Veja dados de funcionários inativos</small>
                    </div>
                    <a href="{{ route('employees.index') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>Voltar
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
    
</script>
@endpush
