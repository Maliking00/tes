@extends('layouts.app')

@section('title', 'Dashboard | ' . (new \App\Helper\Helper())->showEnvironment()))

@section('content')
    @if (Auth::user()->role != 'student')
        <div class="row">
            <div class="col-md-6 grid-margin stretch-card">
                <div class="card tale-bg">
                    <div class="card-people mt-auto">
                        <img src="{{ asset('assets/images/dashboard/dashboard-temp.svg') }}" alt="people">
                        <div class="weather-info">
                            <div class="d-flex">
                                <div>
                                    <h2 class="mb-0 font-weight-normal"><i class="icon-sun mr-2"></i><span
                                            class="weather-temp">0</span><sup>C</sup>
                                    </h2>
                                </div>
                                <div class="ml-2">
                                    <h4 class="location font-weight-normal weather-location">-----</h4>
                                    <h6 class="font-weight-normal weather-type">----</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 grid-margin transparent">
                <div class="row">
                    <div class="col-md-6 mb-4 stretch-card transparent">
                        <div class="card card-tale">
                            <div class="card-body">
                                <p class="mb-4">Users</p>
                                <p class="fs-30 mb-2">{{ $users->count() }}</p>
                                <p>Overall total users</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4 stretch-card transparent">
                        <div class="card card-dark-blue">
                            <div class="card-body">
                                <p class="mb-4">Students</p>
                                <p class="fs-30 mb-2">61344</p>
                                <p>Number of students</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-4 mb-lg-0 stretch-card transparent">
                        <div class="card card-light-blue">
                            <div class="card-body">
                                <p class="mb-4">Teachers</p>
                                <p class="fs-30 mb-2">34040</p>
                                <p>Number of teachers</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 stretch-card transparent">
                        <div class="card card-light-danger">
                            <div class="card-body">
                                <p class="mb-4">Courses</p>
                                <p class="fs-30 mb-2">47033</p>
                                <p>Number of courses</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8 stretch-card grid-margin">
                <div class="row w-100">
                    <div class="col-md-12 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <p class="card-title">Charts</p>
                                <div class="charts-data">
                                    <div class="mt-3">
                                        <p class="mb-0">Data 1</p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="progress progress-md flex-grow-1 mr-4">
                                                <div class="progress-bar bg-inf0" role="progressbar" style="width: 95%"
                                                    aria-valuenow="95" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                            <p class="mb-0">5k</p>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <p class="mb-0">Data 2</p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="progress progress-md flex-grow-1 mr-4">
                                                <div class="progress-bar bg-info" role="progressbar" style="width: 35%"
                                                    aria-valuenow="35" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                            <p class="mb-0">1k</p>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <p class="mb-0">Data 3</p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="progress progress-md flex-grow-1 mr-4">
                                                <div class="progress-bar bg-info" role="progressbar" style="width: 48%"
                                                    aria-valuenow="48" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                            <p class="mb-0">992</p>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <p class="mb-0">Data 4</p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="progress progress-md flex-grow-1 mr-4">
                                                <div class="progress-bar bg-info" role="progressbar" style="width: 25%"
                                                    aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                            <p class="mb-0">687</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 stretch-card grid-margin grid-margin-md-0">
                        <div class="card data-icon-card-primary">
                            <div class="card-body">
                                <p class="card-title text-white">Number of Meetings</p>
                                <div class="row">
                                    <div class="col-8 text-white">
                                        <h3>34040</h3>
                                        <p class="text-white font-weight-500 mb-0">The total number of sessions
                                            within the date range.It is calculated as the sum . </p>
                                    </div>
                                    <div class="col-4 background-icon">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 stretch-card grid-margin">
                <div class="card">
                    <div class="card-body">
                        <p class="card-title">Users</p>
                        <ul class="icon-data-list">
                            <li>
                                <div class="d-flex">
                                    <img src="images/faces/face1.jpg" alt="user">
                                    <div>
                                        <p class="text-info mb-1">Isabella Becker</p>
                                        <p class="mb-0">BS Information Technology</p>
                                        <small>2nd Year</small>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="d-flex">
                                    <img src="images/faces/face2.jpg" alt="user">
                                    <div>
                                        <p class="text-info mb-1">Adam Warren</p>
                                        <p class="mb-0">BS Information Technology</p>
                                        <small>2nd Year</small>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="d-flex">
                                    <img src="images/faces/face3.jpg" alt="user">
                                    <div>
                                        <p class="text-info mb-1">Leonard Thornton</p>
                                        <p class="mb-0">BS Information Technology</p>
                                        <small>2nd Year</small>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="d-flex">
                                    <img src="images/faces/face4.jpg" alt="user">
                                    <div>
                                        <p class="text-info mb-1">George Morrison</p>
                                        <p class="mb-0">BS Information Technology</p>
                                        <small>2nd Year</small>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="d-flex">
                                    <img src="images/faces/face5.jpg" alt="user">
                                    <div>
                                        <p class="text-info mb-1">Ryan Cortez</p>
                                        <p class="mb-0">Herbs are fun and easy to grow.</p>
                                        <small>9:00 am</small>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="content-wrapper">
            Student Dashboard
        </div>
    @endif
@endsection
