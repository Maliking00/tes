@extends('layouts.app')

@section('title', 'Academic Info | ' . (new \App\Helper\Helper())->showEnvironment()))

@section('pageTitle', 'Academic Info | '. $academic->academicYear)

@section('uuid', $academic->academicYear)

@section('content')
    <div class="row">
        <div class="col-lg-4">
            <div class="card py-4" data-aos="fade-up" data-aos-delay="200">
                <div class="card-body p-4 text-right">
                    <br>
                    <form method="POST" action="{{ route('update.academic', $academic->id) }}">
                        @csrf
                        <div class="form-group text-left">
                            <label for="academicYear">Year</label>
                            <div>
                                <input type="text" id="academicYear" class="form-control" placeholder="Year (xxxx-xxxx)"
                                    name="academicYear" maxlength="9" value="{{ $academic->academicYear }}" />
                            </div>
                        </div>
                        <div class="form-group text-left">
                            <label for="academicSemester">Semester</label>
                            <div>
                                <select id="academicSemester" class="form-select form-control" name="academicSemester">
                                    <option value="1" {{ $academic->academicSemester == 1 ? 'selected' : '' }}>1st Semester</option>
                                    <option value="2" {{ $academic->academicSemester == 2 ? 'selected' : '' }}>2nd Semester</option>
                                    <option value="3" {{ $academic->academicSemester == 3 ? 'selected' : '' }}>3rd Semester</option>
                                    <option value="4" {{ $academic->academicSemester == 4 ? 'selected' : '' }}>4th Semester</option>
                                </select>
                            </div>
                        </div>
                        <hr class="hr-divider">
                        <button type="submit" class="btn tes-btn">Update Academic</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-8 grid-margin">
            <div class="v-100 text-center mb-3" data-aos="fade-up" data-aos-delay="400">
                <div class="card">
                    <div class="card-body">
                        <img class="img-fluid" src="{{ asset('assets/images/dashboard/bg-wave-logo.jpg') }}"
                            alt="Not found">
                        <h3 class="font-weight-normal mt-4">Academic Information</h3>
                        <p>Please review the information below and make any necessary updates</p>
                    </div>
                </div>
            </div>
            <div class="v-100" data-aos="fade-up" data-aos-delay="400">
                <h5 class="font-weight-normal mt-4 aos-init aos-animate" data-aos="fade-up" data-aos-delay="100">System
                    Default
                </h5>
                <div class="card border">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <p class="font-weight-bold">Set <span
                                    style="font-weight: 700;color:#aad1f8;">{{ $academic->academicYear }}</span> as a
                                default</p>
                            <p>This will set the academic year to its default.</p>
                        </div>
                        <form action="{{ route('update.academic.default.year', $academic->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="academicSystemDefault"
                                value="{{ $academic->academicSystemDefault }}">
                            @if ($academic->academicSystemDefault == 0)
                                <button class="btn btn-outline-info">Make it Default</button>
                            @else
                                <button class="btn btn-outline-warning">Unset</button>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
            <div class="v-100" data-aos="fade-up" data-aos-delay="400">
                <h5 class="font-weight-normal mt-4 aos-init aos-animate" data-aos="fade-up" data-aos-delay="100">Evaluation
                    Status
                </h5>
                <div class="card border">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <p class="font-weight-bold">Select Evaluation Status</p>
                            <p>This will set the academic evaluation status.</p>
                        </div>
                        <form id="evaluationStatus" action="{{ route('update.academic.evaluation.status', $academic->id) }}" method="POST">
                            @csrf
                            <select name="academicEvaluationStatus" class="form-select form-control"
                                aria-label="Default select example" style="padding-left:40px;padding-right:40px;" onchange="document.querySelector('#evaluationStatus').submit()">
                                <option selected>Choose Status</option>
                                <option value="Starting">Starting</option>
                                <option value="Closed">Closed</option>
                                <option value="Not started">Not started</option>
                            </select>
                        </form>
                    </div>
                </div>
            </div>
            <div class="v-100" data-aos="fade-up" data-aos-delay="400">
                <h5 class="font-weight-normal mt-4 aos-init aos-animate" data-aos="fade-up" data-aos-delay="100">Danger Zone
                </h5>
                <div class="card danger-zone">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <p class="font-weight-bold">Delete this academic</p>
                            <p>Once you delete this, there is no going back. Please be certain.</p>
                        </div>
                        <form action="{{ route('delete.academic', $academic->id) }}" method="POST">
                            @csrf
                            <button class="btn btn-outline-danger">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
