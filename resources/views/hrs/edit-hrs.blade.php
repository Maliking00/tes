@extends('layouts.app')

@section('title', 'HR Info | ' . (new \App\Helper\Helper())->showEnvironment()))

@section('pageTitle', 'HR Info | '. $hr->name)

@section('uuid', $hr->name)

@section('content')
    <div class="row">
        <div class="col-lg-4">
            <div class="card py-4" data-aos="fade-up" data-aos-delay="200">
                <div class="card-body p-4 text-right">
                    <br>
                    <form method="POST" action="{{ route('update.hr', $hr->id) }}">
                        @csrf
                        <p class="font-weight-bold text-left">Basic Info</p>
                        <div class="form-group mb-2 text-left">
                            <label for="fullname" class="form-label">Full Name</label>
                            <input type="text" name="name" id="fullname"
                                class="form-control @error('name') is-invalid @enderror" value="{{ $hr->name }}"
                                placeholder="Full Name">
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <small>{{ $message }}</small>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group mb-2 text-left">
                            <label for="email" class="form-label">Email</label>
                            <input type="text" name="email" id="email"
                                class="form-control @error('email') is-invalid @enderror" value="{{ $hr->email }}"
                                placeholder="Email">
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <small>{{ $message }}</small>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group mb-2 text-left">
                            <label for="idNumber" class="form-label">ID Number</label>
                            <input type="text" name="idNumber" id="idNumber"
                                class="form-control @error('idNumber') is-invalid @enderror"
                                value="{{ $hr->idNumber }}" placeholder="ID Number">
                            @error('idNumber')
                                <span class="invalid-feedback" role="alert">
                                    <small>{{ $message }}</small>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group mb-2 text-left">
                            <label for="contactNumber" class="form-label">Contact Number</label>
                            <input type="text" name="contactNumber" id="contactNumber"
                                class="form-control @error('contactNumber') is-invalid @enderror"
                                value="{{ $hr->contactNumber }}" placeholder="Contact Number">
                            @error('contactNumber')
                                <span class="invalid-feedback" role="alert">
                                    <small>{{ $message }}</small>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group mb-2 text-left">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" name="password" id="password"
                                class="form-control @error('password') is-invalid @enderror" value="********"
                                placeholder="Password">
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <small>{{ $message }}</small>
                                </span>
                            @enderror
                        </div>
                        <p class="font-weight-bold mt-4 text-left">Security Question and Answer</p>
                        <div class="form-group mb-4 text-left">
                            <label for="securityQuestion" class="form-label">Select a security question</label>
                            <select name="security_question" id="securityQuestion"
                                class="form-select form-control @error('security_question') is-invalid @enderror"
                                aria-label="Default select example">
                                <option selected>Choose</option>
                                @foreach ($securityQuestionsString as $question)
                                    <option value="{{ $question->id }}" @if ($question->question == $defaultSecurityQA->question) selected @endif>
                                        {{ $question->question }}</option>
                                @endforeach
                            </select>
                            @error('security_question')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group mb-4 text-left">
                            <label for="security_answer" class="form-label text-left">Security Answer</label>
                            <input type="text" value="{{ $defaultSecurityQA->answer }}" name="security_answer"
                                id="security_answer" class="form-control @error('security_answer') is-invalid @enderror"
                                placeholder="Security Answer">
                            @error('security_answer')
                                <span class="invalid-feedback" role="alert">
                                    <small>{{ $message }}</small>
                                </span>
                            @enderror
                        </div>
                        <hr class="hr-divider">
                        <button type="submit" class="btn tes-btn">Update Hr</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-8 grid-margin">
            <div class="v-100 text-center mb-3" data-aos="fade-up" data-aos-delay="400">
                <div class="card">
                    <div class="card-body">
                        <img src="{{ asset((new \App\Helper\Helper())->avatarPathOnProduction($hr->avatarUrl, 'avatarUrl')) }}"
                            class="user-avatar" alt="profile" />
                        <h3 class="font-weight-normal mt-4">Hr Information</h3>
                        <p>Please review the information and make any necessary updates</p>
                        <form id="updateAvatar" action="{{route('update.hr.avatar', $hr->id)}}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group mb-4 text-left">
                                <input type="file" name="avatar" id="avatar" class="form-control d-none"
                                    placeholder="Security Answer" onchange="document.querySelector('#updateAvatar').submit()">
                            </div>
                        </form>
                        <button class="btn tes-btn" onclick="document.querySelector('#avatar').click()">Change
                            Profile</button>
                    </div>
                </div>
            </div>
            <div class="v-100" data-aos="fade-up" data-aos-delay="400">
                <h5 class="font-weight-normal my-4 aos-init aos-animate" data-aos="fade-up" data-aos-delay="100">Hr
                    Status</h5>
                <div class="card border">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <p class="font-weight-bold">Update hr status</p>
                            <p>Once you delete this, there is no going back. Please be certain.</p>
                        </div>
                        <form action="{{ route('update.hr.status', $hr->id) }}" method="POST">
                            @csrf
                            @if ($hr->status == 'pending')
                                <input type="hidden" name="status" value="approved">
                                <button type="submit" class="btn btn-outline-info">Approve this hr</button>
                            @else
                                <input type="hidden" name="status" value="pending">
                                <button type="submit" class="btn btn-outline-primary">Make it pending</button>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
            <div class="v-100" data-aos="fade-up" data-aos-delay="400">
                <h5 class="font-weight-normal mt-4 aos-init aos-animate" data-aos="fade-up" data-aos-delay="100">User
                    Role
                </h5>
                <div class="card border">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <p class="font-weight-bold">Update Role</p>
                            <p>This will set the user role.</p>
                        </div>
                        <form id="updateRole" action="{{ route('update.hr.role', $hr->id) }}" method="POST">
                            @csrf
                            <select name="role" class="form-select form-control" aria-label="Default select example"
                                style="padding-left:40px;padding-right:40px;"
                                onchange="document.querySelector('#updateRole').submit()">
                                <option selected>Choose Role</option>
                                <option value="student">Student</option>
                                <option value="HR">HR</option>
                            </select>
                        </form>
                    </div>
                </div>
            </div>
            <div class="v-100" data-aos="fade-up" data-aos-delay="400">
                <h5 class="font-weight-normal my-4 aos-init aos-animate" data-aos="fade-up" data-aos-delay="100">Danger
                    Zone
                </h5>
                <div class="card danger-zone">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <p class="font-weight-bold">Delete this hr</p>
                            <p>Once you delete this, there is no going back. Please be certain.</p>
                        </div>
                        <form action="{{ route('delete.hr', $hr->id) }}" method="POST">
                            @csrf
                            <button class="btn btn-outline-danger">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
