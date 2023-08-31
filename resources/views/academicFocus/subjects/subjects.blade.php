@extends('layouts.app')

@section('title', 'Subjects | Teacher Evaluation System')

@section('content')
<div class="row">
    <div class="col-lg-4">
        <div class="card py-4" data-aos="fade-up" data-aos-delay="200">
            <div class="card-body p-4 text-right">
                <br>
                <form id="add_{{\Route::currentRouteName()}}_form">
                    @csrf
                    <div class="form-group text-left">
                        <label>Subject Code</label>
                        <div>
                            <input type="text" class="form-control" placeholder="Subject Code"
                                name="subjectCode" value="{{ old('subject') }}" />
                            <small class="text-danger" id="subjectCode-error"></small>
                        </div>
                    </div>
                    <div class="form-group text-left">
                        <label>Subject Name</label>
                        <div>
                            <input type="text" class="form-control" placeholder="Subject Name"
                                name="subjectName" value="{{ old('subject') }}" />
                            <small class="text-danger" id="subjectName-error"></small>
                        </div>
                    </div>
                    <div class="form-group text-left">
                        <label>Subject Description</label>
                        <div>
                            <textarea name="subjectDescription" id="" cols="30" rows="3" class="form-control"
                                placeholder="Subject Description"></textarea>
                            <small class="text-danger" id="subjectDescription-error"></small>
                        </div>
                    </div>
                    <hr class="hr-divider">
                    <button type="submit" class="btn tes-btn">Add Subject</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-8 grid-margin">
        <div class="table-responsive" id="load{{ucfirst(\Route::currentRouteName())}}">

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

        async function load{{ucfirst(\Route::currentRouteName())}}(url) {
            let {{\Route::currentRouteName()}}Output = document.querySelector('#load{{ucfirst(\Route::currentRouteName())}}');
            let searchInput = document.querySelector('#search_{{\Route::currentRouteName()}}');
            const paginationLink = document.querySelector('#pagination_link')
            const page_of = document.querySelector('.page_of')
            const page_total = document.querySelector('.page_total')

            {{\Route::currentRouteName()}}Output.innerHTML = `<div class="text-center">
            <div class="spinner-border" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            </div>`;

            let URL = searchInput.value == '' ? url : url + `?search_input=${searchInput.value}`

            await axios.get(URL)
                .then(function(response) {
                    if (response.status == 200) {
                        let data = response.data;
                        {{\Route::currentRouteName()}}Output.innerHTML = data.table;
                        let pagination = data.pagination.links;
                        if (data.pagination.total > 0) {
                            paginationLink.innerHTML = '';
                            pagination.forEach(elem => {
                                if (elem.url != null) {
                                    paginationLink.innerHTML += `<li style="cursor:pointer;" class="page-item ${elem.active ? 'active' : '' } ">
                                <a onclick="load{{ucfirst(\Route::currentRouteName())}}('${elem.url}')" class="page-link">${elem.label}</a>
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

        load{{ucfirst(\Route::currentRouteName())}}('/dashboard/load-{{\Route::currentRouteName()}}');

        const {{\Route::currentRouteName()}}Form = document.querySelector('#add_{{\Route::currentRouteName()}}_form');
        {{\Route::currentRouteName()}}Form.addEventListener('submit', async (e) => {
            e.preventDefault();
            let formData = new FormData({{\Route::currentRouteName()}}Form);
            let subjectCodeError = document.querySelector('#subjectCode-error');
            let subjectNameError = document.querySelector('#subjectName-error');
            let subjectDescriptionError = document.querySelector('#subjectDescription-error');

            await axios.post('/dashboard/store-{{\Route::currentRouteName()}}', formData)
                .then(function(response) {
                    if (response.status == 200) {
                        load{{ucfirst(\Route::currentRouteName())}}('/dashboard/load-{{\Route::currentRouteName()}}');
                        {{\Route::currentRouteName()}}Form.reset();
                        subjectCodeError.innerHTML = '';
                        subjectNameError.innerHTML = '';
                        subjectDescriptionError.innerHTML = '';
                    }
                })
                .catch(function(error) {
                    error.response.data.errors.subjectCode ?
                        subjectCodeError.innerHTML = error.response.data.errors.subjectCode :
                        subjectCodeError.innerHTML = '';

                    error.response.data.errors.subjectName ?
                        subjectNameError.innerHTML = error.response.data.errors.subjectName :
                        subjectNameError.innerHTML = '';

                    error.response.data.errors.subjectDescription ?
                        subjectDescriptionError.innerHTML = error.response.data.errors.subjectDescription :
                        subjectDescriptionError.innerHTML = '';
                })
        })
    </script>
@endsection
