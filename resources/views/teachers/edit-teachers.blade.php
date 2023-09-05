@extends('layouts.app')

@section('title', 'Teacher Info | Teacher Evaluation System')

@section('pageTitle', 'Teacher Info | '. $teacher->teachersFullName)

@section('uuid', $teacher->teachersFullName)

@section('content')
    <div class="row">
        <div class="col-lg-4">
            <div class="card py-4" data-aos="fade-up" data-aos-delay="200">
                <div class="card-body p-4 text-right">
                    <br>
                    <form method="POST" action="{{ route('update.teacher', $teacher->id) }}">
                        @csrf
                        <p class="font-weight-bold text-left">Basic Info</p>
                        <div class="form-group mb-2 text-left">
                            <label for="teachersFullName" class="form-label">Full Name</label>
                            <input type="text" name="teachersFullName" id="teachersFullName"
                                class="form-control @error('teachersFullName') is-invalid @enderror" value="{{ $teacher->teachersFullName }}"
                                placeholder="Full Name">
                            @error('teachersFullName')
                                <span class="invalid-feedback" role="alert">
                                    <small>{{ $message }}</small>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group mb-2 text-left">
                            <label for="teachersEmail" class="form-label">Email</label>
                            <input type="email" name="teachersEmail" id="teachersEmail"
                                class="form-control @error('teachersEmail') is-invalid @enderror" value="{{ $teacher->teachersEmail }}"
                                placeholder="Email">
                            @error('teachersEmail')
                                <span class="invalid-feedback" role="alert">
                                    <small>{{ $message }}</small>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group mb-2 text-left">
                            <label for="teachersIdNumber" class="form-label">ID Number</label>
                            <input type="text" name="teachersIdNumber" id="teachersIdNumber"
                                class="form-control @error('teachersIdNumber') is-invalid @enderror"
                                value="{{ $teacher->teachersIdNumber }}" placeholder="ID Number">
                            @error('teachersIdNumber')
                                <span class="invalid-feedback" role="alert">
                                    <small>{{ $message }}</small>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group mb-2 text-left">
                            <label for="teachersContactNumber" class="form-label">Contact Number</label>
                            <input type="text" name="teachersContactNumber" id="teachersContactNumber"
                                class="form-control @error('teachersContactNumber') is-invalid @enderror"
                                value="{{ $teacher->teachersContactNumber }}" placeholder="Contact Number">
                            @error('teachersContactNumber')
                                <span class="invalid-feedback" role="alert">
                                    <small>{{ $message }}</small>
                                </span>
                            @enderror
                        </div>
                        <hr class="hr-divider">
                        <button type="submit" class="btn tes-btn">Update Teacher</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-8 grid-margin">
            <div class="v-100 text-center mb-3" data-aos="fade-up" data-aos-delay="400">
                <div class="card">
                    <div class="card-body">
                        <img src="{{ asset((new \App\Helper\Helper())->avatarPathOnProduction($teacher->teachersAvatar, 'teachersAvatar')) }}"
                            class="user-avatar" alt="profile" />
                        <h3 class="font-weight-normal mt-4">Teacher Information</h3>
                        <p>Please review the information and make any necessary updates</p>
                        <form id="updateAvatar" action="{{route('update.teacher.avatar', $teacher->id)}}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group mb-4 text-left">
                                <input type="file" name="teachersAvatar" id="teachersAvatar" class="form-control d-none"
                                    placeholder="Security Answer" onchange="document.querySelector('#updateAvatar').submit()">
                            </div>
                        </form>
                        <button class="btn tes-btn" onclick="document.querySelector('#teachersAvatar').click()">Change
                            Profile</button>
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
                            <p class="font-weight-bold">Delete this teacher</p>
                            <p>Once you delete this, there is no going back. Please be certain.</p>
                        </div>
                        <form action="{{ route('delete.teacher', $teacher->id) }}" method="POST">
                            @csrf
                            <button class="btn btn-outline-danger">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
