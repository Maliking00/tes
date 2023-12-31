@extends('layouts.app')

@section('title', 'Subject Info | ' . (new \App\Helper\Helper())->showEnvironment())

@section('pageTitle', 'Subject Info | '. $subject->subjectCode)

@section('uuid', $subject->subjectCode)

@section('content')
    <div class="row">
        <div class="col-lg-4">
            <div class="card py-4" data-aos="fade-up" data-aos-delay="200">
                <div class="card-body p-4 text-right">
                    <br>
                    <form method="POST" action="{{ route('update.subject', $subject->id) }}">
                        @csrf
                        <div class="form-group mb-4 text-left">
                            <label for="courses" class="form-label">Select a courses</label>
                            <select name="course_id" id="courses"
                                class="form-select form-control @error('course_id') is-invalid @enderror">
                                <option selected>Choose</option>
                                @foreach ($courses as $course)
                                    <option value="{{ $course->id }}" @if ($course->id == $defaultCourse) selected @endif>{{ $course->courseName . ' ' . $course->courseYearLevel . '-' . $course->courseSection}}</option>
                                @endforeach
                            </select>
                            @error('course_id')
                                <span class="invalid-feedback" role="alert">
                                    <small>{{ $message }}</small>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group text-left">
                            <label for="subjectCode">Subject Code</label>
                            <div>
                                <input type="text" id="subjectCode" class="form-control" placeholder="Subject Code"
                                    name="subjectCode" value="{{ $subject->subjectCode }}" />
                            </div>
                        </div>
                        <div class="form-group text-left">
                            <label for="subjectName">Subject Name</label>
                            <div>
                                <input type="text" id="subjectName" class="form-control" placeholder="Subject Name"
                                    name="subjectName" value="{{ $subject->subjectName }}" />
                            </div>
                        </div>
                        <div class="form-group text-left">
                            <label for="subjectDescription">Subject Description</label>
                            <div>
                                <textarea name="subjectDescription" id="subjectDescription" cols="30" rows="5" class="form-control"
                                    placeholder="Subject Description">{{ $subject->subjectDescription }}</textarea>
                            </div>
                        </div>
                        <hr class="hr-divider">
                        <button type="submit" class="btn tes-btn">Update Subject</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-8 grid-margin">
            <div class="v-100 text-center mb-3" data-aos="fade-up" data-aos-delay="400">
                <div class="card">
                    <div class="card-body">
                        <img class="img-fluid" src="{{ asset('assets/images/dashboard/bg-wave-logo.jpg') }}"
                            alt="Not found">
                        <h3 class="font-weight-normal mt-4">Subject Information</h3>
                        <p>{{ $subject->subjectDescription }}</p>
                    </div>
                </div>
            </div>
            <div class="v-100" data-aos="fade-up" data-aos-delay="400">
                <h5 class="font-weight-normal my-4 aos-init aos-animate" data-aos="fade-up" data-aos-delay="100">Danger Zone
                </h5>
                <div class="card danger-zone">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <p class="font-weight-bold">Delete this subject</p>
                            <p>Once you delete this, there is no going back. Please be certain.</p>
                        </div>
                        <form action="{{ route('delete.subject', $subject->id) }}" method="POST">
                            @csrf
                            <button class="btn btn-outline-danger">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
