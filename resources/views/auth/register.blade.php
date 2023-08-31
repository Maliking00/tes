@extends('layouts.app')

@section('title', 'Register | Teacher Evaluation System')

@section('content')
    <div class="container-fluid welcome">
        <div class="row vh-100">
            <div class="col-lg-6 vh-100 w-first-col">
                <p>Welcome to</p>
                <h2>Teacher Evaluation System</h2>
                <p>Secure and Reliable Web-based Teacher Evaluation System <br /> with Multi-Factor Authentication and Advance Encryption Standard</p>
            </div>
            <div class="col-lg-6 vh-100 w-second-col">
                <div class="w-login-form">
                    <div class="my-4 text-center">
                        <img src="{{ asset('/assets/images/logo.png') }}" alt="welcom-logo">
                    </div>
                    <form action="{{ route('register.data') }}" method="POST">
                        @csrf
                        <div class="form-group mb-2">
                            <label for="fullname" class="form-label">Full Name</label>
                            <input type="text" name="name" id="fullname"
                                class="form-control @error('name') is-invalid @enderror" placeholder="Full Name">
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <small>{{ $message }}</small>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group mb-2">
                            <label for="email" class="form-label">Email</label>
                            <input type="text" name="email" id="email"
                                class="form-control @error('email') is-invalid @enderror" placeholder="Email">
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <small>{{ $message }}</small>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group mb-2">
                            <label for="idNumber" class="form-label">ID Number</label>
                            <input type="text" name="idNumber" id="idNumber"
                                class="form-control @error('idNumber') is-invalid @enderror" placeholder="ID Number">
                            @error('idNumber')
                                <span class="invalid-feedback" role="alert">
                                    <small>{{ $message }}</small>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group mb-2">
                            <label for="contactNumber" class="form-label">Contact Number</label>
                            <input type="text" name="contactNumber" id="contactNumber"
                                class="form-control @error('contactNumber') is-invalid @enderror" placeholder="Contact Number">
                            @error('contactNumber')
                                <span class="invalid-feedback" role="alert">
                                    <small>{{ $message }}</small>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group mb-2">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" name="password" id="password"
                                class="form-control @error('password') is-invalid @enderror" placeholder="Password">
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <small>{{ $message }}</small>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group mb-4">
                            <label for="password_confirmation" class="form-label">Confirm Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                class="form-control @error('password_confirmation') is-invalid @enderror" placeholder="Confirm Password">
                            @error('password_confirmation')
                                <span class="invalid-feedback" role="alert">
                                    <small>{{ $message }}</small>
                                </span>
                            @enderror
                        </div>
                        <div class="form-goup">
                            <button type="submit" class="btn tes-btn w-100">Next</button>
                        </div>
                    </form>
                    <div class="my-3 text-center account-ask">
                        <p>Already have an account? <a href="{{route('login')}}">Sign in</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection