@extends('layouts.app')

@section('title', 'Teacher Evaluation | ' . (new \App\Helper\Helper())->showEnvironment())

@section('pageTitle', 'Teacher Evaluation for ' . $restriction->teacherName)

@section('content')
    <div class="row teacher-evaluation">
        <div class="col-lg-3">
            <h4 class="mb-3">You are now evaluating</h4>
            <a href="#" style="pointer-events:none;cursor:not-allowed;">
                <div class="card shadow-sm p-1 mb-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="card-body p-1">
                        <img class="t-e-avatar"
                            src="{{ asset((new \App\Helper\Helper())->avatarPathOnProduction($restriction->teacherAvatar, 'teachersAvatar')) }}"
                            alt="{{ $restriction->teacherName }}">
                        <div class="t-e-content">
                            <h6>{{ $restriction->teacherName }}</h6>
                            <p>{{ $restriction->subjectCode . ' ' . $restriction->subjectName }}</p>
                            <small>Assigned {{\Carbon\Carbon::createFromTimestamp($restriction->dateAssigned)->diffForHumans()}}</small>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-9 grid-margin">
            <div class="table-responsive" data-aos="fade-up" data-aos-delay="300">
                <div class="mb-4 shadow-sm bg-white p-3 rounded">
                    <h2 class="text-center">Teacher Evaluation</h2>
                    <h4 class="text-center">{{ $restriction->academicYear }}
                        {{ (new \App\Helper\Helper())->academicFormat($restriction->academicSemester) }}</h4>
                    <h3 class="mb-3">Rating Legend</h3>
                    <div class="d-flex align-items-center justify-content-between">
                        <small class="badge text-black bg-white shadow-sm">1 = Strongly Disagree</small>
                        <small class="badge text-black bg-white shadow-sm">2 = Disagree</small>
                        <small class="badge text-black bg-white shadow-sm">3 = Uncertain</small>
                        <small class="badge text-black bg-white shadow-sm">4 = Agree</small>
                        <small class="badge text-black bg-white shadow-sm">5 = Strongly Agree</small>
                    </div>
                </div>
                @if (count($criterias) > 0)
                    <form action="{{ route('teacher.evaluation.academic.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="restriction_id" value="{{$restriction->restrictionID}}">
                        <input type="hidden" name="academic_id" value="{{$restriction->academicID}}">
                        <input type="hidden" name="teacher_id" value="{{$restriction->teacherID}}">
                        <input type="hidden" name="course_id" value="{{$restriction->courseID}}">
                        <input type="hidden" name="subject_id" value="{{$restriction->subjectID}}">
                        <input type="hidden" name="teacher" value="{{$restriction->teacherName}}">
                        @php $num1 = 0; @endphp
                        @foreach ($criterias as $criteria)
                            @php
                                $num1++;
                                $questionnaires = DB::table('questionnaires')
                                    ->where('criterias_id', $criteria->id)
                                    ->where('academic_id', $restriction->academicID)
                                    ->get();
                            @endphp

                            <input type="hidden" name="criteria{{$num1}}[]" value="{{ $criteria->criterias }}">
                            <table class="table text-left questionnaires-table">
                                <thead>
                                    <tr class="t-row-head aos-init aos-animate" data-aos="fade-up" data-aos-delay="200">
                                        <th colspan="8" class="text-capitalize">{{ $criteria->criterias }}</th>
                                        <th class="text-lowercase">1</th>
                                        <th class="text-lowercase">2</th>
                                        <th class="text-lowercase">3</th>
                                        <th class="text-lowercase">4</th>
                                        <th class="text-lowercase">5</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $num2 = 0; @endphp
                                    @foreach ($questionnaires as $key => $questionnaire)
                                        @php $num2++; @endphp
                                        <tr class="t-row aos-init aos-animate" data-aos="fade-up" data-aos-delay="300">
                                            <td colspan="8" style="white-space: normal; line-height: 20px;">
                                                {{ $questionnaire->questions }}
                                                <input type="hidden" name="questionID{{$num1}}{{$num2}}[]" value="{{ $questionnaire->id }}">
                                                <input type="hidden" name="question{{$num1}}{{$num2}}[]" value="{{ $questionnaire->questions }}">
                                            </td>
                                            <td class="text-lowercase">
                                                <input type="radio"
                                                    name="answer{{$num1}}{{$num2}}[]" value="1">
                                            </td>
                                            <td class="text-lowercase">
                                                <input type="radio"
                                                    name="answer{{$num1}}{{$num2}}[]" value="2">
                                            </td>
                                            <td class="text-lowercase">
                                                <input type="radio"
                                                    name="answer{{$num1}}{{$num2}}[]" value="3">
                                            </td>
                                            <td class="text-lowercase">
                                                <input type="radio"
                                                    name="answer{{$num1}}{{$num2}}[]" value="4">
                                            </td>
                                            <td class="text-lowercase">
                                                <input type="radio"
                                                    name="answer{{$num1}}{{$num2}}[]" value="5"
                                                    checked>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endforeach
                        <div class="form-group">
                            <button type="submit" class="btn tes-btn">Submit</button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
@endsection
