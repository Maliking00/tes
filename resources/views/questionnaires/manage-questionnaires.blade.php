@extends('layouts.app')

@section('title', 'Manage Questionnaires | Teacher Evaluation System')

@section('pageTitle', 'Questionnaires')

@section('uuid', $academics->academicYear . ' | ' . $academics->academicSemester)

@section('content')
    <div class="row">
        <div class="col-lg-4">
            <div class="card py-4" data-aos="fade-up" data-aos-delay="200">
                <div class="card-body p-4 text-right">
                    <br>
                    <form id="add_criteria_form">
                        @csrf
                        <input type="hidden" name="academic_id" value="{{ $academics->id }}">
                        <div class="form-group text-left">
                            <label for="criterias">Criteria</label>
                            <div>
                                <select name="criterias" id="criterias" class="form-select form-control"
                                    aria-label="Default select example">
                                    <option selected>Choose</option>
                                    @foreach ($criterias as $criteria)
                                        <option value="{{ $criteria->id }}">{{ $criteria->criterias }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <small class="text-danger" id="criterias-error"></small>
                        </div>
                        <div class="form-group text-left">
                            <label for="questions">Question for teacher</label>
                            <div>
                                <textarea name="questions" class="form-control" id="questions" cols="30" rows="5"
                                    placeholder="Write a question"></textarea>
                                <small class="text-danger" id="questions-error"></small>
                            </div>
                        </div>
                        <hr class="hr-divider">
                        <button type="submit" class="btn tes-btn">Add Question</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-8 grid-margin">
            <h5 class="my-3">
                Evaluation Questionnaire for Academic:
                <span class="badge bg-danger">
                    <form id="academicStatus" action="{{ route('update.academic.status', $academics->id) }}" method="POST">
                        @csrf
                        <span>{{ $academics->academicYear . ' | ' . (new \App\Helper\Helper())->academicFormat($academics->academicSemester) }} |</span>
                        <select name="academicEvaluationStatus" class="border-0 bg-danger text-white rounded" onchange="document.querySelector('#academicStatus').submit()">
                            <option selected>{{$academics->academicEvaluationStatus}}</option>
                            <option value="Starting">Starting</option>
                            <option value="Closed">Closed</option>
                            <option value="Not started">Not started</option>
                        </select>
                    </form>
                </span>
            </h5>
            <hr>
            <div class="table-responsive" id="loadQuestionnaires">

            </div>
            <div class="text-center mt-2 d-flex align-items-center justify-content-between">
                <div class="page_of" data-aos="fade-right" data-aos-delay="300"></div>
                <ul id="pagination_link" data-aos="fade-up" data-aos-delay="300"></ul>
                <div class="page_total" data-aos="fade-left" data-aos-delay="300"></div>
            </div>
        </div>
    </div>
@endsection

@section('questionnairesActions')
    <a href="{{ route('restrictions', $academics->id) }}"
        class="btn tes-btn d-flex align-items-center gap-4 justify-content-center">
        <i class="ti-shield"></i><span>Restriction</span>
    </a>
@endsection

@section('scripts')
    <script>
        let page = 0;

        async function loadManageQuestionnaire(url) {
            let criteriaOutput = document.querySelector('#loadQuestionnaires');
            const paginationLink = document.querySelector('#pagination_link')
            const page_of = document.querySelector('.page_of')
            const page_total = document.querySelector('.page_total')

            criteriaOutput.innerHTML = `<div class="text-center">
            <img src="{{ asset('assets/images/loading.gif') }}" alt="loading">
            <p>I am about to search for it.</p>
            </div>`;

            let URL = url;

            await axios.get(URL)
                .then(function(response) {
                    if (response.status == 200) {
                        let data = response.data;
                        criteriaOutput.innerHTML = data.table;
                        let pagination = data.pagination.links;
                        if (data.pagination.total > 0) {
                            paginationLink.innerHTML = '';
                            pagination.forEach(elem => {
                                if (elem.url != null) {
                                    paginationLink.innerHTML += `<li style="cursor:pointer;" class="page-item ${elem.active ? 'active' : '' } ">
                                <a onclick="loadManageQuestionnaire('${elem.url}')" class="page-link">${elem.label}</a>
                            </li>`;
                                }
                            });
                            page_of.innerHTML =
                                `Page ${data.pagination.current_page} of ${data.pagination.last_page}`;
                            page_total.innerHTML = `Total of ${data.pagination.total}`;
                        } else {
                            paginationLink.innerHTML = '';
                            page_of.innerHTML = '';
                            page_total.innerHTML = '';
                        }
                    }
                })
                .catch(function(error) {
                    console.log(error);
                })
        }

        loadManageQuestionnaire('/dashboard/load-manage-questionnaire/{{ $academics->id }}');

        const criteriaForm = document.querySelector('#add_criteria_form');
        criteriaForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            let formData = new FormData(criteriaForm);
            let errorOutput = document.querySelectorAll('.text-danger');

            await axios.post('/dashboard/store-questionnaires', formData)
                .then(function(response) {
                    if (response.status == 200) {
                        loadManageQuestionnaire(
                            '/dashboard/load-manage-questionnaire/{{ $academics->id }}');
                        criteriaForm.reset();
                        scrollToTop();
                        errorOutput.forEach(function(item) {
                            item.innerHTML = '';
                        });
                    }
                })
                .catch(function(error) {
                    const errors = error.response.data.errors;
                    if (errors) {
                        let errorOutput = document.querySelectorAll('.text-danger')
                        errorOutput.forEach(function(item) {
                            item.innerHTML = '';
                        });
                        scrollToTop();
                        for (let key in errors) {
                            if (errors.hasOwnProperty(key)) {
                                const errorMessages = errors[key];
                                for (let i = 0; i < errorMessages.length; i++) {
                                    document.querySelector(`#${key}-error`).innerHTML = errorMessages[i];
                                }
                            }
                        }
                    }
                });
        });

        function scrollToTop() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }
    </script>
@endsection
