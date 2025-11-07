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
                            <a href="{{ route('vacancies.open') }}" class="btn btn-outline-primary w-100 p-5">
                                <i class="fas fa-briefcase me-2"></i>Vagas abertas
                            </a>
                        </div>

                        <div class="col-md-3 mb-3 p-3">
                            <a href="#" class="btn btn-outline-primary w-100 p-5">
                                <i class="fas fa-archive me-2"></i>Vagas Encerradas
                            </a>
                        </div>


                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection