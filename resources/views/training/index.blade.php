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
                            <a href="{{ route('training.contents') }}" class="btn btn-outline-primary w-100 p-5">
                                <i class="fas fa-book me-2"></i>Conteúdos de treinamento
                            </a>
                        </div>

                        <div class="col-md-3 mb-3 p-3">
                            <a href="{{ route('training.trainings') }}" class="btn btn-outline-primary w-100 p-5">
                                <i class="fas fa-university me-2"></i>Treinamentos
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection