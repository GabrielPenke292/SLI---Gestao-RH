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
                            <a href="{{ route('selections.awaiting') }}" class="btn btn-outline-primary w-100 p-5">
                                <i class="fas fa-clock me-2"></i>Aguardando Aprovação
                            </a>
                        </div>

                        <div class="col-md-3 mb-3 p-3">
                            <a href="{{ route('selections.in-progress') }}" class="btn btn-outline-primary w-100 p-5">
                                <i class="fas fa-spinner me-2"></i>Em Andamento
                            </a>
                        </div>

                        <div class="col-md-3 mb-3 p-3">
                            <a href="{{ route('selections.finished') }}" class="btn btn-outline-primary w-100 p-5">
                                <i class="fas fa-check-circle me-2"></i>Encerrados/Congelados
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
