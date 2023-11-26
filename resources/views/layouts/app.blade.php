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
    <link rel="stylesheet" href="{{ asset('vendors/ti-icons/css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('css/vertical-layout-light/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/aos.css') }}" />
    @yield('styles')
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
                            <span class="icon-menu"><i class="ti-align-left"></i></span>
                        </button>
                        <ul class="navbar-nav navbar-nav-right">
                            <li class="nav-item nav-profile dropdown">
                                <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown"
                                    id="profileDropdown">
                                    <img src="{{ asset((new \App\Helper\Helper())->userAvatar(Auth::user()->avatarUrl)) }}"
                                        alt="profile" title="{{ Auth::user()->email }}" />
                                </a>
                                <div class="dropdown-menu dropdown-menu-right navbar-dropdown"
                                    aria-labelledby="profileDropdown">
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
                    </div>
                </nav>
                <div class="container-fluid page-body-wrapper">
                    <nav class="sidebar sidebar-offcanvas" id="sidebar">
                        <ul class="nav">
                            @if (Auth::user()->role == 'admin')
                                <li class="nav-item">
                                    <a class="nav-link @if (\Route::currentRouteName() == 'dashboard') active @endif"
                                        href="{{ route('dashboard') }}">
                                        <i class="ti-dashboard menu-icon"></i>
                                        <span class="menu-title">Dashboard</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link @if ((new \App\Helper\Helper())->isAcaddemicFocusRoutes(\Route::currentRouteName())) active @endif"
                                        data-toggle="collapse" href="#sidebar_academic_focus"
                                        @if ((new \App\Helper\Helper())->isAcaddemicFocusRoutes(\Route::currentRouteName())) aria-expanded="true" @else aria-expanded="false" @endif
                                        aria-controls="sidebar_academic_focus">
                                        <i class="ti-filter menu-icon"></i>
                                        <span class="menu-title">Academic Focus</span>
                                        <i class="menu-arrow"></i>
                                    </a>
                                    <div class="collapse @if ((new \App\Helper\Helper())->isAcaddemicFocusRoutes(\Route::currentRouteName())) show @endif"
                                        id="sidebar_academic_focus">
                                        <ul class="nav flex-column sub-menu">
                                            <li class="nav-item">
                                                <a class="nav-link" href="{{ route('courses') }}">Courses</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" href="{{ route('subjects') }}">Subjects</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" href="{{ route('academics') }}">Academic Year</a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link @if ((new \App\Helper\Helper())->isUsersRoutes(\Route::currentRouteName())) active @endif"
                                        data-toggle="collapse" href="#sidebar_users"
                                        @if ((new \App\Helper\Helper())->isUsersRoutes(\Route::currentRouteName())) aria-expanded="true" @else aria-expanded="false" @endif
                                        aria-controls="sidebar_users">
                                        <i class="ti-user menu-icon"></i>
                                        <span class="menu-title">Users</span>
                                        <i class="menu-arrow"></i>
                                    </a>
                                    <div class="collapse @if ((new \App\Helper\Helper())->isUsersRoutes(\Route::currentRouteName())) show @endif" id="sidebar_users">
                                        <ul class="nav flex-column sub-menu">
                                            <li class="nav-item">
                                                <a class="nav-link" href="{{ route('teachers') }}">Teachers</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" href="{{ route('students') }}">Students</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" href="{{ route('hrs') }}">HR</a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link @if (\Route::currentRouteName() == 'criterias' || \Route::currentRouteName() == 'show.edit.criteria') active @endif"
                                        href="{{ route('criterias') }}">
                                        <i class="ti-calendar menu-icon"></i>
                                        <span class="menu-title">Evaluation Criteria</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link @if (\Route::currentRouteName() == 'questionnaires' || \Route::currentRouteName() == 'show.manage.questionnaires') active @endif"
                                        href="{{ route('questionnaires') }}">
                                        <i class="ti-clipboard menu-icon"></i>
                                        <span class="menu-title">Questionnaires</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('evaluation.reports') }}">
                                        <i class="ti-agenda menu-icon"></i>
                                        <span class="menu-title">Evaluation Report</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link @if (\Route::currentRouteName() == 'settings') active @endif"
                                        href="{{ route('settings') }}">
                                        <i class="ti-settings menu-icon"></i>
                                        <span class="menu-title">Settings</span>
                                    </a>
                                </li>
                            @elseif(Auth::user()->role == 'student')
                                <li class="nav-item">
                                    <a class="nav-link @if (\Route::currentRouteName() == 'dashboard') active @endif"
                                        href="{{ route('dashboard') }}">
                                        <i class="icon-grid menu-icon"></i>
                                        <span class="menu-title">Dashboard</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link @if (\Route::currentRouteName() == 'teacher.evaluation') active @endif"
                                        href="{{ route('teacher.evaluation') }}">
                                        <i class="ti-calendar menu-icon"></i>
                                        <span class="menu-title">Teacher Evaluation</span>
                                    </a>
                                </li>
                            @else
                                <li class="nav-item">
                                    <a class="nav-link @if (\Route::currentRouteName() == 'dashboard') active @endif"
                                        href="{{ route('dashboard') }}">
                                        <i class="icon-grid menu-icon"></i>
                                        <span class="menu-title">Dashboard</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('evaluation.reports') }}">
                                        <i class="ti-calendar menu-icon"></i>
                                        <span class="menu-title">Evaluation Reports</span>
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
                                        <div class="col-5 col-xl-10 mb-4 mb-xl-0">
                                            @if (\Route::currentRouteName() == 'dashboard')
                                                <h3 class="font-weight-bold main-panel-title">Welcome
                                                    {{ Auth::user()->name }}</h3>
                                            @else
                                                <h3 class="font-weight-bold main-panel-title">@yield('pageTitle')</h3>
                                            @endif
                                            <ul class="breadcrumb">
                                                <li>
                                                    <a class="active" href="{{ route('dashboard') }}">Dashboard</a>
                                                </li>
                                                @if (\Route::currentRouteName() == 'teacher.evaluation.academic')
                                                    <li><i class='ti-angle-right menu-icon'></i></li>
                                                    <li>
                                                        <a class="active"
                                                            href="{{ route('teacher.evaluation') }}">Back</a>
                                                    </li>
                                                @elseif(\Route::currentRouteName() == 'evaluation.reports.responses')
                                                    <li><i class='ti-angle-right menu-icon'></i></li>
                                                    <li>
                                                        <a class="active"
                                                            href="{{ route('evaluation.reports') }}">Evaluation
                                                            Reports</a>
                                                    </li>
                                                    <li><i class='ti-angle-right menu-icon'></i></li>
                                                    <li>
                                                        @yield('param')
                                                    </li>
                                                @else
                                                    @for ($i = 2; $i <= count(Request::segments()); $i++)
                                                        <li><i class='ti-angle-right menu-icon'></i></li>
                                                        <li>

                                                            <a class="text-capitalize"
                                                                href="{{ URL::to(implode('/', array_slice(Request::segments(), 0, $i, true))) }}">
                                                                @if (preg_match('/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}$/', Request::segment($i)))
                                                                    @yield('uuid')
                                                                @else
                                                                    {{ str_replace('-', ' ', Request::segment($i)) }}
                                                                @endif
                                                            </a>
                                                        </li>
                                                    @endfor
                                                @endif
                                            </ul>
                                        </div>
                                        <div class="col-7 col-xl-2 mb-8">
                                            @if ((new \App\Helper\Helper())->isListPage(\Route::currentRouteName()))
                                                <div class="input-group flex-nowrap" data-aos="fade-up"
                                                    data-aos-delay="100">
                                                    <span class="input-group-text bg-transparent" id="addon-wrapping"><i
                                                            class="ti-search"></i></span>
                                                    <input type="search" class="form-control py-1 search-input"
                                                        id="search_{{ \Route::currentRouteName() }}"
                                                        oninput="load{{ ucfirst(\Route::currentRouteName()) }}('/dashboard/load-{{ \Route::currentRouteName() }}')"
                                                        placeholder="Search for {{ \Route::currentRouteName() }}">
                                                </div>
                                            @endif
                                            @yield('questionnairesActions')
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
    <script src="{{ asset('js/template.js') }}"></script>
    <script src="{{ asset('js/aos.js') }}"></script>
    @include('layouts.alert')
    <script>
        AOS.init({
            once: true,
            offset: 10,
        });
    </script>
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

            @php
                $setting = (new \App\Models\Setting())->first();
            @endphp

            const city = "{{ $setting->weatherCity }}";
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
    <script>
        if (document.querySelector('.alert')) {
            setTimeout(() => {
                document.querySelector('.alert').style.display = 'none';
            }, 20000);
        }
    </script>
    @if (\Route::currentRouteName() == 'restrictions')
        <script>
            async function handleSubjects(course_id) {
                const courseURL = `/load-students-restriction-subjects/${course_id}`;
                const subjectSlect = document.querySelector('#subjects');
                await axios.get(courseURL)
                    .then(function(response) {
                        console.log(response);
                        if (response.status == 200) {
                            subjectSlect.innerHTML = response.data.checkbox;
                        }
                    })
                    .catch(function(error) {
                        console.log(error);
                    })
            }
        </script>
    @endif
    @if (
        \Route::currentRouteName() == 'students' ||
            \Route::currentRouteName() == 'show.edit.student' ||
            \Route::currentRouteName() == 'register')
        <script>
            async function handleSubjects(course_id) {
                const courseURL = `/load-students-subjects/${course_id}`;
                const subjectSlect = document.querySelector('#subjects');
                await axios.get(courseURL)
                    .then(function(response) {
                        console.log(response);
                        if (response.status == 200) {
                            subjectSlect.innerHTML = response.data.checkbox;
                        }
                    })
                    .catch(function(error) {
                        console.log(error);
                    })
            }
        </script>
    @endif
</body>

</html>
