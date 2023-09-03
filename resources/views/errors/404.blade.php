@extends('layouts.app')

@section('title', '404 | Page not found')

@section('content')
    <div class="container-fluid vh-100 d-flex align-items-center justify-content-center">
        <div class="text-center">
            <img class="w-50" src="{{asset('assets/images/404.png')}}" alt="404">
            <p class="my-5">This page has gone MIA, probably chasing its dreams of becoming a unicorn. 🦄✨ <br /> Try another link, and remember, even unicorns need a vacation!</p>
            <a href="{{route('welcome')}}" class="btn tes-btn">I'm not a unicorn</a>
        </div>
    </div>
@endsection