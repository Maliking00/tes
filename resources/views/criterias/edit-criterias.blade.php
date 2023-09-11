@extends('layouts.app')

@section('title', 'Criterias Info | ' . (new \App\Helper\Helper())->showEnvironment())

@section('pageTitle', 'Criteria Info | '. $criteria->criterias)

@section('uuid', $criteria->criterias)

@section('content')
    <div class="row">
        <div class="col-lg-4">
            <div class="card py-4" data-aos="fade-up" data-aos-delay="200">
                <div class="card-body p-4 text-right">
                    <br>
                    <form method="POST" action="{{ route('update.criteria', $criteria->id) }}">
                        @csrf
                        <div class="form-group text-left">
                            <label for="criterias">Criterias</label>
                            <div>
                                <input type="text" id="criterias" class="form-control" placeholder="Subject Code"
                                    name="criterias" value="{{ $criteria->criterias }}" />
                            </div>
                        </div>
                        <hr class="hr-divider">
                        <button type="submit" class="btn tes-btn">Update Criteria</button>
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
                        <h3 class="font-weight-normal mt-4">Criteria Information</h3>
                        <p>Please review the information below and make any necessary updates</p>
                    </div>
                </div>
            </div>
            <div class="v-100" data-aos="fade-up" data-aos-delay="400">
                <h5 class="font-weight-normal my-4 aos-init aos-animate" data-aos="fade-up" data-aos-delay="100">Danger Zone
                </h5>
                <div class="card danger-zone">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <p class="font-weight-bold">Delete this criteria</p>
                            <p>Once you delete this, there is no going back. Please be certain.</p>
                        </div>
                        <form action="{{ route('delete.criteria', $criteria->id) }}" method="POST">
                            @csrf
                            <button class="btn btn-outline-danger">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
