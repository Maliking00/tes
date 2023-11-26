@extends('layouts.app')

@section('title', 'Settings | ' . (new \App\Helper\Helper())->showEnvironment())

@section('pageTitle', 'Settings')

@section('content')
    <div class="row">
        <div class="col-lg-6">
            <h4 class="mb-3" data-aos="fade-up" data-aos-delay="200">Administrator Credentials</h4>
            <div class="card shadow-sm" data-aos="fade-up" data-aos-delay="300">
                <div class="card-body">
                    <div class="text-center">
                        <img src="{{ asset((new \App\Helper\Helper())->userAvatar(Auth::user()->avatarUrl)) }}"
                            class="user-avatar" alt="profile" />
                        <h3 class="font-weight-normal mt-4">{{ Auth::user()->name }}</h3>
                        <p class="font-weight-normal">{{ Auth::user()->email }}</p>
                        <form id="updateAvatar" action="{{ route('update.student.avatar', Auth::id()) }}" method="post"
                            enctype="multipart/form-data">
                            @csrf
                            <input type="file" name="avatar" id="avatar" class="form-control d-none"
                                placeholder="Security Answer" onchange="document.querySelector('#updateAvatar').submit()">
                        </form>
                        <button class="btn tes-btn" onclick="document.querySelector('#avatar').click()">Change
                            Profile</button>
                        <hr>
                    </div>
                    <form action="{{route('update.admin.credentials', Auth::id())}}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="name">Full Name</label>
                                    <input type="text" name="name" id="name" class="form-control"
                                        placeholder="Full Name" value="{{Auth::user()->name}}">
                                    @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <small>{{ $message }}</small>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" name="email" id="email" class="form-control"
                                        placeholder="Email" value="{{Auth::user()->email}}">
                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <small>{{ $message }}</small>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="idNumber">ID Number</label>
                                    <input type="text" name="idNumber" id="idNumber" class="form-control"
                                        placeholder="ID Number" value="{{Auth::user()->idNumber}}">
                                    @error('idNumber')
                                        <span class="invalid-feedback" role="alert">
                                            <small>{{ $message }}</small>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="contactNumber">Contact Number</label>
                                    <input type="text" name="contactNumber" id="" class="form-control"
                                        placeholder="Contact Number" value="{{Auth::user()->contactNumber}}">
                                    @error('contactNumber')
                                        <span class="invalid-feedback" role="alert">
                                            <small>{{ $message }}</small>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label for="password">Password</label>
                                    <input type="password" name="password" id="" class="form-control"
                                        placeholder="Password" value="ThisIsAPassWord@123">
                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <small>{{ $message }}</small>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <p class="font-weight-bold mt-4 text-left">Security Question and Answer</p>
                            <div class="form-group mb-4 text-left">
                                <label for="securityQuestion" class="form-label">Select a security question</label>
                                <select name="security_question" id="securityQuestion"
                                    class="form-select form-control @error('security_question') is-invalid @enderror"
                                    aria-label="Default select example">
                                    <option selected>Choose</option>
                                    @foreach ($securityQuestionsString as $question)
                                        <option value="{{ $question->id }}"
                                            @if ($question->question == $defaultSecurityQA->question) selected @endif>{{ $question->question }}
                                        </option>
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
                            <div class="form-group">
                                <button type="submit" class="btn tes-btn">Update</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <h4 class="mb-3" data-aos="fade-up" data-aos-delay="300">System configuration</h4>
            <div class="card shadow-sm" data-aos="fade-up" data-aos-delay="400">
                <div class="card-body">
                    <form action="{{ route('settings') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <h5>One-Time Passcode</h5>
                            <div class="d-flex align-items-center gap-2 mt-4">
                                <input type="checkbox" value="0" id="smsMode" name="smsMode"
                                    @if ($setting->smsMode == 0) checked @endif>
                                <label class="mb-0" for="smsMode">Enable Test Mode</label>
                                @error('smsMode')
                                    <span class="invalid-feedback" role="alert">
                                        <small>{{ $message }}</small>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="semaphoreApiKey">Semaphore API Key</label>
                            <input type="text" name="semaphoreApiKey" value="{{ $setting->semaphoreApiKey }}"
                                class="form-control" placeholder="API Key">
                            @error('semaphoreApiKey')
                                <span class="invalid-feedback" role="alert">
                                    <small>{{ $message }}</small>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="weatherCity">Weather City Name <i class="ti-help-alt" data-bs-toggle="tooltip"
                                    data-bs-placement="top"
                                    title="This will display the temperature on the dashboard page base on location."></i></label>
                            <input type="text" name="weatherCity" value="{{ $setting->weatherCity }}"
                                class="form-control" placeholder="City Name">
                            @error('weatherCity')
                                <span class="invalid-feedback" role="alert">
                                    <small>{{ $message }}</small>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <button class="btn tes-btn" type="submit">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    </script>
@endsection
