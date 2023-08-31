@extends('layouts.app')

@section('title', 'Security Question | Teacher Evaluation System')

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
                <div class="w-login-form">
                    <div class="my-4 text-center">
                        <img src="{{ asset('/assets/images/logo.png') }}" alt="welcom-logo">
                    </div>
                    <form action="{{ route('register.security.question') }}" method="POST">
                        @csrf
                        <div class="form-group mb-4">
                            <label for="password_confirmation" class="form-label">Select a security question</label>
                            <select name="security_question" class="form-select form-control @error('security_question') is-invalid @enderror"
                                aria-label="Default select example">
                                <option selected>Choose</option>
                                @foreach ($registerSecurityQuestions as $question)
                                    <option value="{{ $question->id }}">{{ $question->question }}</option>
                                @endforeach
                            </select>
                            @error('security_question')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group mb-4">
                            <label for="security_answer" class="form-label">Security Answer</label>
                            <input type="text" name="security_answer" id="security_answer"
                                class="form-control @error('security_answer') is-invalid @enderror" placeholder="Security Answer">
                            @error('security_answer')
                                <span class="invalid-feedback" role="alert">
                                    <small>{{ $message }}</small>
                                </span>
                            @enderror
                        </div>

                        <div class="form-goup">
                            <button type="submit" class="btn tes-btn w-100">Proceed</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
