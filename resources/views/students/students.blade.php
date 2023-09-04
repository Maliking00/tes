@extends('layouts.app')

@section('title', 'Students | Teacher Evaluation System')

@section('pageTitle', 'Students')

@section('content')
    <div class="row">
        <div class="col-lg-4">
            <div class="card py-4" data-aos="fade-up" data-aos-delay="200">
                <div class="card-body p-4 text-right">
                    <br>
                    <form id="add_{{ \Route::currentRouteName() }}_form" enctype="multipart/form-data">
                        @csrf
                        <p class="font-weight-bold text-left">Basic Info</p>
                        <div class="form-group mb-2 text-left">
                            <label for="fullname" class="form-label">Full Name</label>
                            <input type="text" name="name" id="fullname" class="form-control"
                                placeholder="Full Name">
                            <small class="text-danger" id="name-error"></small>
                        </div>
                        <div class="form-group mb-2 text-left">
                            <label for="email" class="form-label">Email</label>
                            <input type="text" name="email" id="email" class="form-control" placeholder="Email">
                            <small class="text-danger" id="email-error"></small>
                        </div>
                        <div class="form-group mb-2 text-left">
                            <label for="idNumber" class="form-label">ID Number</label>
                            <input type="text" name="idNumber" id="idNumber" class="form-control"
                                placeholder="ID Number">
                            <small class="text-danger" id="idNumber-error"></small>
                        </div>
                        <div class="form-group mb-2 text-left">
                            <label for="contactNumber" class="form-label">Contact Number</label>
                            <input type="text" name="contactNumber" id="contactNumber" class="form-control"
                                placeholder="Contact Number">
                            <small class="text-danger" id="contactNumber-error"></small>
                        </div>
                        <div class="form-group mb-4 text-left">
                            <label for="courses" class="form-label">Select a courses</label>
                            <select name="courses" id="courses"
                                class="form-select form-control @error('courses') is-invalid @enderror">
                                <option selected>Choose</option>
                                @foreach ($courses as $course)
                                    <option value="{{ $course->id }}">{{ $course->courseName . ' ' . $course->courseYearLevel . '-' . $course->courseSection}}</option>
                                @endforeach
                            </select>
                            <small class="text-danger" id="security_question-error"></small>
                        </div>
                        <div class="form-group mb-2 text-left">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" name="password" id="password" class="form-control"
                                placeholder="Password">
                            <small class="text-danger" id="password-error"></small>
                        </div>
                        <p class="font-weight-bold mt-4 text-left">Security Question and Answer</p>
                        <div class="form-group mb-4 text-left">
                            <label for="securityQuestion" class="form-label">Select a security question</label>
                            <select name="security_question" id="securityQuestion"
                                class="form-select form-control @error('security_question') is-invalid @enderror">
                                <option selected>Choose</option>
                                @foreach ($studentSecurityQuestions as $question)
                                    <option value="{{ $question->id }}">{{ $question->question }}</option>
                                @endforeach
                            </select>
                            <small class="text-danger" id="security_question-error"></small>
                        </div>
                        <div class="form-group mb-4 text-left">
                            <label for="security_answer" class="form-label text-left">Security Answer</label>
                            <input type="text" name="security_answer" id="security_answer" class="form-control"
                                placeholder="Security Answer">
                            <small class="text-danger" id="security_answer-error"></small>
                        </div>
                        <div class="form-group mb-4 text-left">
                            <label for="avatar" class="form-label">Upload avatar</label>
                            <input type="file" name="avatar" id="avatar" class="form-control"
                                placeholder="Security Answer">
                            <small class="text-danger" id="avatar-error"></small>
                        </div>
                        <hr class="hr-divider">
                        <button type="submit" class="btn tes-btn">Add Student</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-8 grid-margin">
            <div class="table-responsive" id="load{{ ucfirst(\Route::currentRouteName()) }}">

            </div>
            <div class="text-center mt-2 d-flex align-items-center justify-content-between">
                <div class="page_of" data-aos="fade-right" data-aos-delay="300"></div>
                <ul id="pagination_link" data-aos="fade-up" data-aos-delay="300"></ul>
                <div class="page_total" data-aos="fade-left" data-aos-delay="300"></div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        let page = 0;

        async function load{{ ucfirst(\Route::currentRouteName()) }}(url) {
            let {{ \Route::currentRouteName() }}Output = document.querySelector(
                '#load{{ ucfirst(\Route::currentRouteName()) }}');
            let searchInput = document.querySelector('#search_{{ \Route::currentRouteName() }}');
            const paginationLink = document.querySelector('#pagination_link')
            const page_of = document.querySelector('.page_of')
            const page_total = document.querySelector('.page_total')

            {{ \Route::currentRouteName() }}Output.innerHTML = `<div class="text-center">
            <img src="{{asset('assets/images/loading.gif')}}" alt="loading">
            <p>I am about to search for it.</p>
            </div>`;

            let URL = searchInput.value == '' ? url : url + `?search_input=${searchInput.value}`

            await axios.get(URL)
                .then(function(response) {
                    if (response.status == 200) {
                        let data = response.data;
                        {{ \Route::currentRouteName() }}Output.innerHTML = data.table;
                        let pagination = data.pagination.links;
                        if (data.pagination.total > 0) {
                            paginationLink.innerHTML = '';
                            pagination.forEach(elem => {
                                if (elem.url != null) {
                                    paginationLink.innerHTML += `<li style="cursor:pointer;" class="page-item ${elem.active ? 'active' : '' } ">
                                <a onclick="load{{ ucfirst(\Route::currentRouteName()) }}('${elem.url}')" class="page-link">${elem.label}</a>
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

        load{{ ucfirst(\Route::currentRouteName()) }}('/dashboard/load-{{ \Route::currentRouteName() }}');

        const {{ \Route::currentRouteName() }}Form = document.querySelector('#add_{{ \Route::currentRouteName() }}_form');
        {{ \Route::currentRouteName() }}Form.addEventListener('submit', async (e) => {
            e.preventDefault();
            let formData = new FormData({{ \Route::currentRouteName() }}Form);
            let errorOutput = document.querySelectorAll('.text-danger');

            await axios.post('/dashboard/store-{{ \Route::currentRouteName() }}', formData)
                .then(function(response) {
                    load{{ ucfirst(\Route::currentRouteName()) }}(
                        '/dashboard/load-{{ \Route::currentRouteName() }}');
                    {{ \Route::currentRouteName() }}Form.reset();
                    scrollToTop();
                    errorOutput.forEach(function(item) {
                        item.innerHTML = '';
                    });
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
                })
        });

        function scrollToTop() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }
    </script>
@endsection
