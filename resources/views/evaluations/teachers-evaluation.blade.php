@extends('layouts.app')

@section('title', 'Teacher Evaluation | ' . (new \App\Helper\Helper())->showEnvironment())

@section('pageTitle', 'Teacher Evaluation')

@section('content')
    <div class="row teacher-evaluation">
        <div class="col-lg-3 te-flex">
            @if (count($restrictions) > 0)
                @foreach ($restrictions as $restriction)
                    @php
                        $isTeacherEvaluatedFromThisAcademicYear = (new \App\Models\TeacherEvaluationStatus())
                            ->where('restriction_id', $restriction->restrictionID)
                            ->where('academic_id', $restriction->academicID)
                            ->where('course_id', $restriction->courseID)
                            ->where('subject_id', $restriction->subjectID)
                            ->where('evaluator_id', Auth::id())
                            ->exists();
                    @endphp
                    <a class="@if ($isTeacherEvaluatedFromThisAcademicYear) te-evaluated @endif"
                        href="/dashboard/teacher-evaluation/{{ $restriction->academicID }}/{{ $restriction->courseID }}/{{ $restriction->teacherID }}/{{ $restriction->subjectID }}">
                        <div class="card shadow-sm p-1 mb-4">
                            <div class="card-body p-1">
                                <img class="t-e-avatar"
                                    src="{{ asset((new \App\Helper\Helper())->avatarPathOnProduction($restriction->teacherAvatar, 'teachersAvatar')) }}"
                                    alt="{{ $restriction->teacherName }}">
                                <div class="t-e-content">
                                    <h6>{{ $restriction->teacherName }}</h6>
                                    <p>{{ $restriction->subjectCode . ' ' . $restriction->subjectName }}</p>
                                    <small class="badge border text-dark">
                                        @if ($isTeacherEvaluatedFromThisAcademicYear)
                                            <i class="ti-check text-success"></i> Evaluated
                                        @else
                                            <i class="ti-na text-danger"></i> <span class="text-danger">Not Evaluated</span>
                                        @endif
                                    </small>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            @else
                <div class="card shadow-sm py-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="card-body p-4 text-center">
                        <h5>Nothing to evaluate.</h5>
                    </div>
                </div>
            @endif
        </div>
        <div class="col-md-9 grid-margin">
            <div class="card shadow-sm" data-aos="fade-up" data-aos-delay="300">
                <div class="card-body d-flex align-items-center gap-4">
                    <img class="w-25" src="{{ asset('/assets/images/logo.png') }}" alt="welcom-logo">
                    <div>
                        <h5>Welcome to</h5>
                        <h2>Teacher Evaluation System</h2>
                        <h3 class="font-weight-bold">{{ $academicDefault->academicYear }}
                            {{ (new \App\Helper\Helper())->academicFormat($academicDefault->academicSemester) }}</h3>
                        <p
                            class="badge bg-{{ $academicDefault->academicEvaluationStatus == 'Starting' ? 'success' : 'secondary' }}">
                            {{ $academicDefault->academicEvaluationStatus }}</p>
                        <p>Secure and Reliable Web-based Teacher Evaluation System <br /> with Multi-Factor Authentication
                            and Advance Encryption Standard</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        window.onload = function() {

            if (document.querySelector('.te-evaluated')) {
                const aTags = document.querySelectorAll('.te-flex a');
                const teFlex = document.querySelector('.te-flex');
                const topItems = teFlex.querySelectorAll('a.te-evaluated');
                const fragment = document.createDocumentFragment();
                topItems.forEach(function(item) {
                    fragment.appendChild(item);
                });
                
                if (aTags.length > 1) {
                    const hr = document.createElement('hr');
                    teFlex.appendChild(hr);
                    const h4 = document.createElement('h4');
                    h4.classList.add('font-weight-normal')
                    h4.textContent = 'Evaluated Teachers';
                    teFlex.appendChild(h4);
                }
                teFlex.appendChild(fragment);
            }
        }
    </script>
@endsection
