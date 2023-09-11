@extends('layouts.app')

@section('title', 'Dashboard | ' . (new \App\Helper\Helper())->showEnvironment())

@section('pageTitle', 'Evaluation Report Response')

@section('param')
    <a class="active" href="#">{{ $data->teacherName }}</a>
@endsection

@section('param')
    <link rel="stylesheet" href="https://printjs-4de6.kxcdn.com/print.min.css">
@endsection


@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-lg-10"></div>
                <div class="col-lg-2">
                    <button class="btn tes-btn btn-block mb-4 d-flex justify-content-center gap-3" type="button"
                        onclick="printJS({ 
                        printable: 'evaluationResponse', 
                        type: 'html', 
                        css: '{{ asset('css/app.css') }}',
                        documentTitle: '{{ $data->teacherName }}',
                    })">
                        <span>Print</span> <i class="ti-printer"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="card" id="evaluationResponse">
                <div class="card-body">
                    <div class="text-center">
                        <img id="teacher_avatar" class="eval-img"
                            src="{{ asset((new \App\Helper\Helper())->avatarPathOnProduction($data->teacherAvatar, 'teachersAvatar')) }}"
                            alt="{{ $data->teacherName }}">
                        <h4 class="text-center my-2">{{ $data->teacherName }}</h4>
                        <p class="text-center my-2">
                            {{ $data->courseName . '-' . $data->courseYearLevel . $data->courseSection }} Teacher <span
                                style="font-weight: 600;">|</span> {{ $data->subjectCode }} Subject</p>
                        <p>{{ $data->academicYear }}
                            {{ (new \App\Helper\Helper())->academicFormat($data->academicSemester) }}</p>
                        <hr>
                    </div>
                    <h3 class="mb-4">Rating Legend</h3>
                    <div class="d-flex align-items-center justify-content-between">
                        <small class="badge text-black bg-white shadow-sm">1 = Strongly Disagree</small>
                        <small class="badge text-black bg-white shadow-sm">2 = Disagree</small>
                        <small class="badge text-black bg-white shadow-sm">3 = Uncertain</small>
                        <small class="badge text-black bg-white shadow-sm">4 = Agree</small>
                        <small class="badge text-black bg-white shadow-sm">5 = Strongly Agree</small>
                    </div>
                    <hr>

                    @foreach ($evalResponses as $criteria => $items)
                        <div class="mb-4">
                            <h4 class="my-2">{{ $criteria }}</h4>
                            @php
                                $uniqueQuestions = [];
                            @endphp
                            @foreach ($items as $item)
                                @php
                                    $stronglyDisagree = (new \App\Models\EvaluationList())
                                        ->where('teacher_id', $item->teacher_id)
                                        ->where('question', $item->question)
                                        ->where('answer', 1)
                                        ->count();
                                    $disagree = (new \App\Models\EvaluationList())
                                        ->where('teacher_id', $item->teacher_id)
                                        ->where('question', $item->question)
                                        ->where('answer', 2)
                                        ->count();
                                    $uncertain = (new \App\Models\EvaluationList())
                                        ->where('teacher_id', $item->teacher_id)
                                        ->where('question', $item->question)
                                        ->where('answer', 3)
                                        ->count();
                                    $agree = (new \App\Models\EvaluationList())
                                        ->where('teacher_id', $item->teacher_id)
                                        ->where('question', $item->question)
                                        ->where('answer', 4)
                                        ->count();
                                    $stronglyAgree = (new \App\Models\EvaluationList())
                                        ->where('teacher_id', $item->teacher_id)
                                        ->where('question', $item->question)
                                        ->where('answer', 5)
                                        ->count();
                                @endphp
                                @if (!in_array($item->question, $uniqueQuestions))
                                    <div class="card mb-2 border py-3 px-4">
                                        <h6 class="my-2">Questions</h6>
                                        <p>{{ $item->question }}</p>
                                        <h6 class="my-2">Answers</h6>
                                        <div class="d-flex align-items-center gap-3">
                                            <small class="badge border text-dark">Strongly Disagree: <span
                                                    class="text-info font-weight-normal">{{ $stronglyDisagree }}</span></small>
                                            <small class="badge border text-dark">Disagree: <span
                                                    class="text-info font-weight-normal">{{ $disagree }}</span></small>
                                            <small class="badge border text-dark">Uncertain: <span
                                                    class="text-info font-weight-normal">{{ $uncertain }}</span></small>
                                            <small class="badge border text-dark">Agree: <span
                                                    class="text-info font-weight-normal">{{ $agree }}</span></small>
                                            <small class="badge border text-dark">Strongly Agree: <span
                                                    class="text-info font-weight-normal">{{ $stronglyAgree }}</span></small>
                                        </div>
                                    </div>
                                    @php
                                        $uniqueQuestions[] = $item->question;
                                    @endphp
                                @endif
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection


@section('scripts')
    <script src="https://printjs-4de6.kxcdn.com/print.min.js"></script>
@endsection
