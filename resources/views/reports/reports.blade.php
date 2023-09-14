@extends('layouts.app')

@section('title', 'Dashboard | ' . (new \App\Helper\Helper())->showEnvironment())

@section('pageTitle', 'Evaluation Reports')

@section('content')
    @if (count($evaluatedTeacher) > 0)
        @foreach ($evaluatedTeacher as $academicYear => $groupedData)
            <div class="report-list mb-4">
                <h4>Academic Year {{ $academicYear }}</h4>
                <hr>
                <div class="row">
                    @foreach ($groupedData as $record)
                        <div class="col-lg-3">
                            <div class="card mb-3">
                                <div class="card-body p-0">
                                    <div class="text-center">
                                        <img src="{{ asset((new \App\Helper\Helper())->avatarPathOnProduction($record->teacherAvatar, 'teachersAvatar')) }}"
                                        class="rl-avatar" alt="{{$record->teacher}}" />
                                    </div>
                                    <div class="card-content">
                                        <h5>{{$record->teacher}}</h5>
                                        <p>{{$record->teacherEmail}}</p>
                                        <p>{{$record->subjectCode}} | {{$record->academicYear}}</p>
                                        <a href="/dashboard/evaluation-reports/{{$record->academic_id}}/{{$record->teacher_id}}/{{$record->course_id}}/{{$record->subject_id}}" class="btn btn-sm btn-outline-info p-2 px-3">View Responses</a>
                                    </div>
                                </div>
                                <hr class="m-0 p-0">
                                <p class="text-text text-dark p-1">{{$record->course}}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    @else
        <div class="row report-list">
            <div class="col-lg-12">
                <div class="card shadow-sm" data-aos="fade-up" data-aos-delay="300">
                    <div class="card-body text-center">
                        <img class="no-data" src="{{ asset('/assets/images/logo.png') }}" alt="welcom-logo">
                        <div>
                            <h4>There is no current teacher evaluated yet.</h4>
                            <p>Enhancing Educator Performance Assessment for the Upcoming School Year</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection
