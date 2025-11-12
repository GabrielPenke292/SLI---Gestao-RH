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
                            <a href="#" class="btn btn-outline-primary w-100 p-5">
                                <i class="fas fa-file-alt me-2"></i>Exames Admissionais
                            </a>
                        </div>

                        <div class="col-md-3 mb-3 p-3">
                            <a href="#" class="btn btn-outline-primary w-100 p-5">
                                <i class="fas fa-file-medical me-2"></i>Exames Demissionais
                            </a>
                        </div>

                        <div class="col-md-3 mb-3 p-3">
                            <a href="{{ route('exams.clinics') }}" class="btn btn-outline-primary w-100 p-5">
                                <i class="fas fa-stethoscope me-2"></i>Clínicas
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection