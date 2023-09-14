@extends('layouts.app')

@section('title', 'Dashboard | ' . (new \App\Helper\Helper())->showEnvironment())

@section('content')
    <div class="row">
        <div class="col-md-6 grid-margin stretch-card">
            <div class="card tale-bg">
                <div class="card-people mt-auto">
                    <img src="{{ asset('assets/images/dashboard/dashboard-temp.svg') }}" alt="people">
                    <div class="weather-info">
                        <div class="d-flex">
                            <div>
                                <h2 class="mb-0 font-weight-normal"><i class="icon-sun mr-2"></i><span
                                        class="weather-temp">0</span><sup>C</sup>
                                </h2>
                            </div>
                            <div class="ml-2">
                                <h4 class="location font-weight-normal weather-location">-----</h4>
                                <h6 class="font-weight-normal weather-type">----</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 grid-margin transparent">
            <div class="row">
                <div class="col-lg-12 mb-4 stretch-card transparent">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            @if(!empty($models['academicDefault']))
                                <h3>Academic Year: <span class="text-info font-weight-bold">{{$models['academicDefault']->academicYear}}</span></h3>
                                <h4>{{(new \App\Helper\Helper())->academicFormat($models['academicDefault']->academicSemester)}}</h4>
                                <p>Evaluation Status: @if($models['academicDefault']->academicEvaluationStatus == 'Starting') <span class="badge bg-success text-white">Starting</span> @elseif($models['academicDefault']->academicEvaluationStatus == 'Closed') <span class="badge bg-secondary text-white">Closed</span> @else <span class="badge bg-info text-white">Not Started</span> @endif</p>
                                @if(Auth::user()->role == 'admin') <a href="{{route('show.edit.academic', $models['academicDefault']->id)}}" class="btn tes-btn btn-sm">Change Status</a> @endif
                            @else
                                <h4>There has been no academic year set yet.</h4>
                                <p>We will provide updates as soon as the schedule is finalized.</p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4 stretch-card transparent">
                    <div class="card card-tale">
                        <div class="card-body">
                            <p class="mb-4">Users</p>
                            <p class="fs-30 mb-2">{{ $models['usersOverAll'] }}</p>
                            <p>Overall total users</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4 stretch-card transparent">
                    <div class="card card-dark-blue">
                        <div class="card-body">
                            <p class="mb-4">Students</p>
                            <p class="fs-30 mb-2">{{ $models['students'] }} <small style="font-size: 16px;">Approved | {{ $models['pendingStudents'] }} Pending</small></p>
                            <p>Number of students</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-4 mb-lg-0 stretch-card transparent">
                    <div class="card card-light-blue">
                        <div class="card-body">
                            <p class="mb-4">Teachers</p>
                            <p class="fs-30 mb-2">{{ $models['teachers'] }}</p>
                            <p>Number of teachers</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 stretch-card transparent">
                    <div class="card card-light-danger">
                        <div class="card-body">
                            <p class="mb-4">Courses</p>
                            <p class="fs-30 mb-2">{{ $models['courses'] }}</p>
                            <p>Number of courses</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
