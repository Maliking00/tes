<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/logo32x32.png') }}" sizes="32x32">
    <link rel="icon" type="image/png" href="{{ asset('assets/images/logo16x16.png') }}" sizes="16x16">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('vendors/feather/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('vendors/ti-icons/css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('vendors/ti-icons/css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('css/vertical-layout-light/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/aos.css') }}" />
</head>

<body>
    @auth
        @if (Auth::user()->status == 'pending')
            @yield('content')
        @else
            <div class="container-scroller">
                <nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
                    <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
                        <a class="navbar-brand brand-logo mr-5" href="{{ route('dashboard') }}">
                            <img src="{{ asset('assets/images/dashboard/dashboard-logo.png') }}" class="mr-2"
                                alt="logo" />
                        </a>
                        <a class="navbar-brand brand-logo-mini" href="{{ route('dashboard') }}">
                            <img src="{{ asset('assets/images/logo.png') }}" alt="logo" />
                        </a>
                    </div>
                    <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
                        <button class="navbar-toggler navbar-toggler align-self-center" type="button"
                            data-toggle="minimize">
                            <span class="icon-menu"></span>
                        </button>
                        <ul class="navbar-nav navbar-nav-right">
                            <li class="nav-item nav-profile dropdown">
                                <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown"
                                    id="profileDropdown">
                                    <img src="{{asset('images/faces/face28.jpg')}}" alt="profile" />
                                </a>
                                <div class="dropdown-menu dropdown-menu-right navbar-dropdown"
                                    aria-labelledby="profileDropdown">
                                    <a class="dropdown-item">
                                        <i class="ti-settings text-primary"></i>
                                        Settings
                                    </a>
                                    <a class="dropdown-item"
                                        onclick="event.preventDefault();
                                document.getElementById('logout-form').submit();">
                                        <i class="ti-power-off text-primary"></i>
                                        Logout
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        </ul>
                        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button"
                            data-toggle="offcanvas">
                            <span class="icon-menu"></span>
                        </button>
                    </div>
                </nav>
                <div class="container-fluid page-body-wrapper">
                    <div class="theme-setting-wrapper">
                        <div id="settings-trigger"><i class="ti-settings"></i></div>
                        <div id="theme-settings" class="settings-panel">
                            <i class="settings-close ti-close"></i>
                            <p class="settings-heading">SIDEBAR SKINS</p>
                            <div class="sidebar-bg-options selected" id="sidebar-light-theme">
                                <div class="img-ss rounded-circle bg-light border mr-3"></div>Light
                            </div>
                            <div class="sidebar-bg-options" id="sidebar-dark-theme">
                                <div class="img-ss rounded-circle bg-dark border mr-3"></div>Dark
                            </div>
                            <p class="settings-heading mt-2">HEADER SKINS</p>
                            <div class="color-tiles mx-0 px-4">
                                <div class="tiles success"></div>
                                <div class="tiles warning"></div>
                                <div class="tiles danger"></div>
                                <div class="tiles info"></div>
                                <div class="tiles dark"></div>
                                <div class="tiles default"></div>
                            </div>
                        </div>
                    </div>

                    <nav class="sidebar sidebar-offcanvas" id="sidebar">
                        <ul class="nav">
                            @if (Auth::user()->role != 'student')
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('dashboard') }}">
                                        <i class="icon-grid menu-icon"></i>
                                        <span class="menu-title">Dashboard</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="collapse" href="#sidebar_academic_focus"
                                        aria-expanded="false" aria-controls="sidebar_academic_focus">
                                        <i class="icon-layout menu-icon"></i>
                                        <span class="menu-title">Academic Focus</span>
                                        <i class="menu-arrow"></i>
                                    </a>
                                    <div class="collapse" id="sidebar_academic_focus">
                                        <ul class="nav flex-column sub-menu">
                                            <li class="nav-item"> <a class="nav-link"
                                                    href="{{route('subjects')}}">Subjects</a>
                                            </li>
                                            <li class="nav-item"> <a class="nav-link"
                                                    href="{{ route('courses') }}">Courses</a>
                                            </li>
                                            <li class="nav-item"> <a class="nav-link"
                                                    href="{{ route('academics') }}">Academic Year</a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="collapse" href="#sidebar_users"
                                        aria-expanded="false" aria-controls="sidebar_users">
                                        <i class="ti-user menu-icon"></i>
                                        <span class="menu-title">Users</span>
                                        <i class="menu-arrow"></i>
                                    </a>
                                    <div class="collapse" id="sidebar_users">
                                        <ul class="nav flex-column sub-menu">
                                            <li class="nav-item"> <a class="nav-link"
                                                    href="pages/ui-features/buttons.html">Teachers</a>
                                            </li>
                                            <li class="nav-item"> <a class="nav-link"
                                                    href="pages/ui-features/dropdowns.html">Students</a>
                                            </li>
                                            <li class="nav-item"> <a class="nav-link"
                                                    href="pages/ui-features/dropdowns.html">HR</a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="">
                                        <i class="ti-clipboard menu-icon"></i>
                                        <span class="menu-title">Questionnaires</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="">
                                        <i class="ti-calendar menu-icon"></i>
                                        <span class="menu-title">Evaluation Criteria</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="">
                                        <i class="ti-agenda menu-icon"></i>
                                        <span class="menu-title">Evaluation Report</span>
                                    </a>
                                </li>
                            @else
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('dashboard') }}">
                                        <i class="icon-grid menu-icon"></i>
                                        <span class="menu-title">Dashboard</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="">
                                        <i class="ti-calendar menu-icon"></i>
                                        <span class="menu-title">Evaluation</span>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </nav>
                    <div class="main-panel">
                        <div class="content-wrapper">
                            <div class="row">
                                <div class="col-md-12 grid-margin">
                                    <div class="row">
                                        <div class="col-5 col-xl-9 mb-4 mb-xl-0">
                                            <h3 class="font-weight-bold">Welcome {{ Auth::user()->name }}</h3>
                                            <ul class="breadcrumb">
                                                <li>
                                                    <a class="active" href="{{route('dashboard')}}">Dashboard</a>
                                                </li>
                                                @for($i = 2; $i <= count(Request::segments()); $i++)
                                                    <li><i class='ti-angle-right menu-icon'></i></li>
                                                    <li>
                                                        <a class="text-capitalize" href="{{ URL::to( implode( '/', array_slice(Request::segments(), 0 ,$i, true)))}}">
                                                            {{str_replace("-"," ",Request::segment($i))}}
                                                        </a>
                                                    </li>
                                                @endfor
                                            </ul>
                                        </div>
                                        <div class="col-7 col-xl-3 mb-8">
                                            <div class="input-group flex-nowrap" data-aos="fade-up" data-aos-delay="100">
                                                <span class="input-group-text bg-transparent" id="addon-wrapping"><i
                                                        class="ti-search"></i></span>
                                                <input type="search" class="form-control py-1 search-input" id="search_{{\Route::currentRouteName()}}"
                                                    oninput="load{{ucfirst(\Route::currentRouteName())}}('/dashboard/load-{{\Route::currentRouteName()}}')" placeholder="Search for {{\Route::currentRouteName()}}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @yield('content')
                        </div>
                        <footer class="footer">
                            <div class="d-sm-flex justify-content-center justify-content-sm-between">
                                <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Copyright ©
                                    {{ date('Y') }}.
                                    Secure and Reliable Web-based Teacher Evaluation System <br /> with Multi-Factor
                                    Authentication and Advance Encryption Standard</span>
                            </div>
                        </footer>
                    </div>
                </div>
            </div>
        @endif
    @else
        @yield('content')
    @endauth

    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="{{ asset('vendors/js/vendor.bundle.base.js') }}"></script>
    <script src="{{ asset('js/off-canvas.js') }}"></script>
    <script src="{{ asset('js/hoverable-collapse.js') }}"></script>
    <script src="{{ asset('js/template.js') }}"></script>
    <script src="{{ asset('js/settings.js') }}"></script>
    <script src="{{ asset('js/aos.js') }}"></script>
    @include('layouts.alert')
    <script>AOS.init({once: true,offset: 10,});</script>
    @auth 
    <script src="{{ asset('js/axios.js') }}"></script>
    @endauth
    @if (\Route::currentRouteName() == 'dashboard')
        <script>
            async function getWeather(city) {
                const apiKey = "ff982e30801c664902181711436705c9";
                const endpoint =
                    `https://api.openweathermap.org/data/2.5/weather?q=${city}&units=metric&lang=ph&appid=${apiKey}`;

                const response = await fetch(endpoint);
                if (response.ok) {
                    const data = await response.json();
                    return data;
                } else {
                    return null;
                }
            }

            const city = "{{ config('app.dashboard_weather_city.city') }}";
            const weatherTemperature = document.querySelector('.weather-temp');
            const weatherLocation = document.querySelector('.weather-location');
            const weatherType = document.querySelector('.weather-type');
            const currentDate = new Date();
            const currentHour = currentDate.getHours();

            getWeather(city)
                .then((weather) => {
                    if (weather) {
                        weatherLocation.innerHTML = weather.name;
                        weatherType.innerHTML = weather.weather[0].description;
                        weatherTemperature.innerHTML = weather.main.temp;
                    } else {
                        console.log("Failed to retrieve weather data.");
                    }
                })
                .catch((error) => {
                    console.error(error);
                });
        </script>
    @endif
    {{-- DYNAMIC SCRIPTS --}}
    @yield('scripts')
</body>

</html>
