@extends('layouts.app')

@section('title', 'Course Info | ' . (new \App\Helper\Helper())->showEnvironment()))

@section('pageTitle', 'Restriction Info | ' . $academics->academicYear)

@section('uuid', $academics->academicYear . ' | ' . $academics->academicSemester)

@section('content')
    <div class="row">
        <div class="col-lg-4">
            <div class="card py-4" data-aos="fade-up" data-aos-delay="200">
                <div class="card-body p-4 text-right">
                    <br>
                    <form method="POST" action="{{ route('store.restriction') }}">
                        @csrf
                        <input type="hidden" name="academic_id" value="{{ $academics->id }}">
                        <div class="form-group text-left">
                            <label for="teacher_id">Teacher Name</label>
                            <div>
                                <select name="teacher_id" id="teacher_id" class="form-select form-control">
                                    <option selected>Choose</option>
                                    @foreach ($models['teachers'] as $teacher)
                                        <option value="{{ $teacher->id }}">{{ $teacher->teachersFullName }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group text-left">
                            <label for="course_id">Courses</label>
                            <div>
                                <select name="course_id" id="course_id" class="form-select form-control">
                                    <option selected>Choose</option>
                                    @foreach ($models['courses'] as $course)
                                        <option value="{{ $course->id }}">
                                            {{ $course->courseName . '-' . $course->courseYearLevel . '-' . $course->courseSection }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group text-left">
                            <label for="subject_id">Subjects</label>
                            <div>
                                <select name="subject_id" id="subject_id" class="form-select form-control">
                                    <option selected>Choose</option>
                                    @foreach ($models['subjects'] as $subject)
                                        <option value="{{ $subject->id }}">{{ $subject->subjectCode }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <hr class="hr-divider">
                        <button type="submit" class="btn tes-btn">Assign Restriction</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-8 grid-margin">
            <div class="table-responsive">
                @if (count($restrictions) > 0)
                    <table class="table">
                        <thead>
                            <tr class="t-row-head" data-aos="fade-up" data-aos-delay="100">
                                <th>Teacher</th>
                                <th>Coursse</th>
                                <th>Subject</th>
                                <th>Date Created</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $idNum = 0; @endphp
                            @foreach($restrictions as $restriction)
                            @php $idNum++; @endphp
                                <tr class="t-row" data-aos="fade-up" data-aos-delay="' . $delay . '00">
                                    <td class="text-capitalize">{{$restriction->teacher}}</td>
                                    <td class="text-capitalize">{{$restriction->course}}</td>
                                    <td class="text-capitalize">{{$restriction->subject}}</td>
                                    <td class="text-lowercase">{{$restriction->created_at->diffForHumans()}}</td>
                                    <td>
                                        <div class="dropup">
                                            <i class="ti-trash show-options text-danger" onclick="event.preventDefault();
                                            document.getElementById('restriction{{$idNum}}').submit();" title="Delete restriction"></i>
                                            <form id="restriction{{$idNum}}" action="{{route('delete.restriction', $restriction->id)}}" method="POST">
                                                @csrf
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                <div class="v-100 text-center" data-aos="fade-up" data-aos-delay="400">
                    <div class="card">
                        <div class="card-body">
                            <img class="img-fluid" src="{{asset('/assets/images/404.jpg')}}" alt="Not found">
                            <h3 class="font-weight-normal mt-4">No Restriction found</h3>
                            <p>I'm sorry, but the specified restriction could not be found.</p>
                            <p>Please provide additional details or clarify your request for further assistance.</p>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
@endsection
