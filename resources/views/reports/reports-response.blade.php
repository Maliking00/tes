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
                            alt="{{ $data->teacher }}">
                        <h4 class="text-center my-2">{{ $data->teacher }}</h4>
                        <p class="text-center my-2">
                            {{ $data->course }} Teacher <span
                                style="font-weight: 600;">|</span> {{ $data->subjectCode }} Subject</p>
                        <p>{{ $data->academicYearAndSemester }}</p>
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
                                        ->where('subject_id', $item->subject_id)
                                        ->where('question', $item->question)
                                        ->where('answer', 1)
                                        ->count();
                                    $disagree = (new \App\Models\EvaluationList())
                                        ->where('teacher_id', $item->teacher_id)
                                        ->where('subject_id', $item->subject_id)
                                        ->where('question', $item->question)
                                        ->where('answer', 2)
                                        ->count();
                                    $uncertain = (new \App\Models\EvaluationList())
                                        ->where('teacher_id', $item->teacher_id)
                                        ->where('subject_id', $item->subject_id)
                                        ->where('question', $item->question)
                                        ->where('answer', 3)
                                        ->count();
                                    $agree = (new \App\Models\EvaluationList())
                                        ->where('teacher_id', $item->teacher_id)
                                        ->where('subject_id', $item->subject_id)
                                        ->where('question', $item->question)
                                        ->where('answer', 4)
                                        ->count();
                                    $stronglyAgree = (new \App\Models\EvaluationList())
                                        ->where('teacher_id', $item->teacher_id)
                                        ->where('subject_id', $item->subject_id)
                                        ->where('question', $item->question)
                                        ->where('answer', 5)
                                        ->count();
                                @endphp
                                @if (!in_array($item->question, $uniqueQuestions))
                                    <div class="card mb-2 border py-3 px-4">
                                        <div class="row">
                                            <div class="col-lg-7">
                                                <h6 class="my-2">Questions</h6>
                                                <p>{{ $item->question }}</p>
                                                <h6 class="my-2">Answers</h6>
                                                <div class="d-flex align-items-center gap-3">
                                                    <small class="badge text-white" style="background: #e63946;">Strongly Disagree: <span
                                                            class="font-weight-normal">{{ $stronglyDisagree }}</span></small>
                                                    <small class="badge text-white" style="background: #254BDD;">Disagree: <span
                                                            class="font-weight-normal">{{ $disagree }}</span></small>
                                                    <small class="badge text-white" style="background: #ffbe0b;">Uncertain: <span
                                                            class="font-weight-normal">{{ $uncertain }}</span></small>
                                                    <small class="badge text-white" style="background: #1d3557;">Agree: <span
                                                            class="font-weight-normal">{{ $agree }}</span></small>
                                                    <small class="badge text-white" style="background: #326998;">Strongly Agree: <span
                                                            class="font-weight-normal">{{ $stronglyAgree }}</span></small>
                                                </div>
                                            </div>
                                            <div class="col-lg-5">
                                                <canvas id="pie-chart{{$item->id}}" width="150" height="150"></canvas>
                                                <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                                                <script>
                                                    new Chart(document.getElementById('pie-chart{{$item->id}}'), {
                                                        type: 'pie',
                                                        data: {
                                                            labels: ["Strongly Disagree", "Disagree", "Uncertain", "Agree", "Strongly Agree"],
                                                            datasets: [{
                                                                backgroundColor: ["#e63946", "#254BDD",
                                                                    "#ffbe0b", "#1d3557", "#326998"
                                                                ],
                                                                data: [{{ $stronglyDisagree }}, {{ $disagree }}, {{ $uncertain }}, {{ $agree }}, {{ $stronglyAgree }}]
                                                            }]
                                                        },
                                                        options: {
                                                            responsive: true,
                                                            maintainAspectRatio: false,
                                                            aspectRatio: 1,
                                                        }
                                                    });
                                                </script>
                                            </div>
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
