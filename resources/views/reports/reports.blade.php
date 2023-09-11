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
                                <div class="text-center">
                                    <img src="{{ asset((new \App\Helper\Helper())->avatarPathOnProduction($record->teacherAvatar, 'teachersAvatar')) }}"
                                    class="rl-avatar" alt="{{$record->teacherName}}" />
                                </div>
                                <div class="card-content">
                                    <h4 class="card-title">{{$record->teacherName}}</h4>
                                    <h5 class="card-text text-info">{{$record->courseName}}</h5>
                                    <p>{{$record->subjectCode}}</p>
                                    <a href="/dashboard/evaluation-reports/{{$record->academicID}}/{{$record->teacherID}}/{{$record->courseID}}/{{$record->subjectID}}" class="btn btn-sm tes-btn p-2 px-3">View Responses</a>
                                </div>
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
