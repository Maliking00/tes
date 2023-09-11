@extends('layouts.app')

@section('title', 'Approval | ' . (new \App\Helper\Helper())->showEnvironment())

@section('content')
    <div class="container-fluid for-approval">
        <div class="row vh-100">
            <div class="col-lg-12 vh-100">
                <div class="d-flex justify-content-center align-items-center vh-100">
                    <div class="card">
                        <div class="card-body text-center">
                            <img class="my-3" src="{{asset( (new \App\Helper\Helper())->avatarPathOnProduction(Auth::user()->avatarUrl, 'avatarUrl') )}}" alt="approval-logo">
                            <h2>Hello {{ Auth::user()->name }}</h2>
                            <p>Your account is currently pending approval. <br /> Please contact the administrator for
                                further information.</p>
                            <button class="btn tes-btn d-flex align-items-center gap-3 mx-auto"
                                onclick="event.preventDefault();
                            document.getElementById('logout-form').submit();">Sign
                                out <i class="ti-power-off text-white"></i></button>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
