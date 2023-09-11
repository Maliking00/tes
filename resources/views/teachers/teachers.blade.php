@extends('layouts.app')

@section('title', 'Teachers | ' . (new \App\Helper\Helper())->showEnvironment())

@section('pageTitle', 'Teachers')

@section('content')
    <div class="row">
        <div class="col-lg-4">
            <div class="card py-4" data-aos="fade-up" data-aos-delay="200">
                <div class="card-body p-4 text-right">
                    <br>
                    <form id="add_{{ \Route::currentRouteName() }}_form" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group mb-2 text-left">
                            <label for="teachersFullName" class="form-label">Full Name</label>
                            <input type="text" name="teachersFullName" id="teachersFullName" class="form-control"
                                placeholder="Full Name">
                            <small class="text-danger" id="teachersFullName-error"></small>
                        </div>
                        <div class="form-group mb-2 text-left">
                            <label for="teachersEmail" class="form-label">Email</label>
                            <input type="email" name="teachersEmail" id="teachersEmail" class="form-control" placeholder="Email">
                            <small class="text-danger" id="teachersEmail-error"></small>
                        </div>
                        <div class="form-group mb-2 text-left">
                            <label for="teachersIdNumber" class="form-label">ID Number</label>
                            <input type="text" name="teachersIdNumber" id="teachersIdNumber" class="form-control"
                                placeholder="ID Number">
                            <small class="text-danger" id="teachersIdNumber-error"></small>
                        </div>
                        <div class="form-group mb-2 text-left">
                            <label for="teachersContactNumber" class="form-label">Contact Number</label>
                            <input type="text" name="teachersContactNumber" id="teachersContactNumber" class="form-control"
                                placeholder="Contact Number">
                            <small class="text-danger" id="teachersContactNumber-error"></small>
                        </div>
                        <div class="form-group mb-4 text-left">
                            <label for="teachersAvatar" class="form-label">Upload avatar</label>
                            <input type="file" name="teachersAvatar" id="teachersAvatar" class="form-control"
                                placeholder="Security Answer">
                            <small class="text-danger" id="teachersAvatar-error"></small>
                        </div>
                        <hr class="hr-divider">
                        <button type="submit" class="btn tes-btn">Add Teacher</button>
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
