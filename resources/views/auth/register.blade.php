@extends('layouts.app')

@section('title', 'Register | Teacher Evaluation System')

@section('content')
    <div class="container-fluid welcome">
        <div class="row vh-100">
            <div class="col-lg-6 vh-100 w-first-col">
                <p>Welcome to</p>
                <h2>Teacher Evaluation System</h2>
                <p>Secure and Reliable Web-based Teacher Evaluation System <br /> with Multi-Factor Authentication and
                    Advance Encryption Standard</p>
            </div>
            <div class="col-lg-6 vh-100 w-second-col">
                <div class="w-login-form register-form">
                    <div class="my-4 text-center">
                        <img src="{{ asset('/assets/images/logo.png') }}" alt="welcom-logo">
                    </div>
                    <form action="{{ route('register.data') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group mb-2">
                                    <label for="fullname" class="form-label">Full Name</label>
                                    <input type="text" name="name" id="fullname"
                                        class="form-control @error('name') is-invalid @enderror" placeholder="Full Name">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group mb-2">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="text" name="email" id="email"
                                        class="form-control @error('email') is-invalid @enderror" placeholder="Email">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group mb-2">
                                    <label for="idNumber" class="form-label">ID Number</label>
                                    <input type="text" name="idNumber" id="idNumber"
                                        class="form-control @error('idNumber') is-invalid @enderror"
                                        placeholder="ID Number">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group mb-2">
                                    <label for="contactNumber" class="form-label">Contact Number</label>
                                    <input type="text" name="contactNumber" id="contactNumber"
                                        class="form-control @error('contactNumber') is-invalid @enderror"
                                        placeholder="Contact Number">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group mb-4 text-left">
                                    <label for="courses" class="form-label">Select course</label>
                                    <select name="courses" id="courses"
                                        class="form-select form-control @error('courses') is-invalid @enderror">
                                        <option selected>Choose</option>
                                        @foreach ($courses as $course)
                                            <option value="{{ $course->id }}">
                                                {{ $course->courseName . ' ' . $course->courseYearLevel . '-' . $course->courseSection }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group mb-4 text-left">
                                    <label for="subjects" class="form-label">Select subjects</label>
                                    <select name="subjects[]" id="subjects"
                                        class="form-select form-control @error('subjects') is-invalid @enderror" multiple>
                                        @foreach ($subjects as $subject)
                                            <option value="{{ $subject->id }}">
                                                {{ $subject->subjectCode }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-2">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" name="password" id="password"
                                class="form-control @error('password') is-invalid @enderror" placeholder="Password">
                        </div>
                        <div class="form-group mb-4">
                            <label for="password_confirmation" class="form-label">Confirm Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                class="form-control @error('password_confirmation') is-invalid @enderror"
                                placeholder="Confirm Password">
                        </div>
                        <div class="form-goup">
                            <button type="submit" class="btn tes-btn w-100">Next</button>
                        </div>
                    </form>
                    <div class="my-3 text-center account-ask">
                        <p>Already have an account? <a href="{{ route('login') }}">Sign in</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
