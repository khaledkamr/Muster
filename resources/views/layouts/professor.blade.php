<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Professor Dashboard - @yield('title')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        ::-webkit-scrollbar {
            width: 10px;
            height: 0; /* Prevent horizontal scrollbar */
        }

        ::-webkit-scrollbar-track {
            background: #0A9442 !important;
        }

        ::-webkit-scrollbar-thumb {
            background: #002361 !important;
            /* color: #00246171; */
            cursor: grab;
            transition: 0.3s;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #0157b3;
        }
        body {
            background-color: #eeee;
            color: #ffffff;
            font-family: Arial, sans-serif;
            margin: 0;
        }
        .navbar {
            background-color: #eeeeee;
            position: fixed;
            top: 0;
            left: 250px;
            width: calc(100% - 250px);
            z-index: 1000;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            z-index: 1;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .search {
            position: relative;
            flex: 1;
        }
        .search button {
            position: absolute;
            color: #ffffff;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: none;
        }
        .search input {
            background-color: #d1d1d1ee;
            border: none;
            border-radius: 30px;
            color: #ffffff;
        }
        .search input::placeholder {
            color: white;
        }
        .search input:focus {
            background-color: #d1d1d1ee;
            outline: none;
        }
        .notifications {
            position: relative;
            color: #4d4c4c;
        }
        .notifications span {
            position: absolute;
            border-radius: 50%;
            bottom: 0;
            right: 0;
            transform: translate(50%, 50%);
        }
        .sidebar {
            padding: 0px 10px;
            background-color: #121212;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            padding-top: 20px;
            box-shadow: 4px 0 8px rgba(0, 0, 0, 0.3);
            z-index: 2;
        }
        .sidebar a {
            color: #ffffff;
            padding: 15px 20px;
            display: flex;
            align-items: center;
            text-decoration: none;
            transition: 0.3s;
        }
        .sidebar a:hover {
            color: rgb(0, 140, 255);
            border-radius: 30px;
            transition: 0.3s;
        }
        .sidebar button {
            color: #ffffff;
            padding: 15px 20px;
            display: flex;
            align-items: center;
            text-decoration: none;
            transition: 0.3s;
            background: none;
            border: none;
        }
        .sidebar button:hover {
            color: rgb(255, 30, 0);
            border-radius: 30px;
            transition: 0.3s;
        }
        .sidebar a.active {
            background-color: #ffffff;
            color: black;
            border-radius: 30px;
        }
        .sidebar a.active i {
            color: black;
        }
        .sidebar i {
            margin-right: 10px;
        }
        .sidebar .image {
            width: 100px;
            margin: 0 auto;
            display: block;
        }
        .content {
            margin-left: 250px;
            padding: 20px;
            padding-top: 70px;
        }
        /* Styles for the collapsible course menu */
        .course-toggle {
            color: #ffffff;
            padding: 15px 20px;
            display: flex;
            align-items: center;
            text-decoration: none;
            transition: 0.3s;
            cursor: pointer;
        }
        .course-toggle:hover {
            color: rgb(0, 140, 255);
            border-radius: 30px;
        }
        .course-toggle[aria-expanded="true"] {
            color: rgb(0, 140, 255);
        }
        .course-sub-menu {
            background-color: #1a1a1a;
            border-radius: 10px;
            margin: 0 10px 10px 10px;
        }
        .course-sub-menu a {
            padding: 10px 40px;
            font-size: 0.9rem;
        }
        .course-sub-menu a:hover {
            color: rgb(0, 140, 255);
            background-color: #2a2a2a;
        }
        .course-sub-menu a.active {
            background-color: #ffffff;
            color: black;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <img class="image pb-3" src="{{ asset('imgs/logo.png') }}" alt="MUST">

        <ul class="nav flex-column">
            <!-- Home -->
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('professor.home') ? 'active' : '' }}"
                   href="{{ route('professor.home') }}">
                    <i class="bi bi-house-door"></i> Home
                </a>
            </li>

            <!-- Courses -->
            @foreach (Auth::user()->courses as $course)
                <li class="nav-item">
                    <!-- Course Toggle -->
                    <a class="course-toggle" data-bs-toggle="collapse" href="#course-{{ $course->id }}" role="button" aria-expanded="false" aria-controls="course-{{ $course->id }}">
                        <i class="bi bi-book"></i> {{ $course->code }}
                    </a>

                    <!-- Course Sub-Menu -->
                    <div class="collapse course-sub-menu" id="course-{{ $course->id }}">
                        <a class="nav-link {{ request()->routeIs('professor.course.students') && $courseId == $course->id ? 'active' : '' }}"
                           href="{{ route('professor.course.students', $course->id) }}">
                           <i class="fa-solid fa-users"></i> Students
                        </a>
                        <a class="nav-link {{ request()->routeIs('professor.course.attendance') && $courseId == $course->id ? 'active' : '' }}"
                           href="{{ route('professor.course.attendance', $course->id) }}">
                           <i class="fa-solid fa-clipboard-user ps-1 pe-1"></i> Attendance
                        </a>
                        <a class="nav-link {{ request()->routeIs('professor.course.assignments') && $courseId == $course->id ? 'active' : '' }}"
                           href="{{ route('professor.course.assignments', $course->id) }}">
                           <i class="bi bi-list-task pe-1"></i> Assignments
                        </a>
                    </div>
                </li>
            @endforeach

            <!-- Profile -->
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('professor.profile') ? 'active' : '' }}"
                   href="{{ route('professor.profile') }}">
                    <i class="bi bi-person-fill"></i> Profile
                </a>
            </li>

            <!-- Logout -->
            <li class="nav-item">
                <form action="{{ route('logout') }}" method="POST" id="logout-form">
                    @csrf
                    <button type="button" class="text" data-bs-toggle="modal" data-bs-target="#logoutModal">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </button>
                </form>
            </li>
        </ul>
    </div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg ps-5 pe-5">
        <form class="search d-flex" role="search">
            <button class="" type="submit">
                <i class="bi bi-search"></i>
            </button>
            <input class="form-control me-2" type="search" placeholder="Search..." aria-label="Search">
        </form>
        <div class="ml-auto d-flex align-items-center ps-4">
            <span class="navbar-text mr-3 d-flex align-items-center">
                <i class="bi bi-person-circle pe-2" style="font-size: 1.5em; margin-right: 5px;"></i>
                <div>{{ Auth::user()->name }}</div>
            </span>
            <div class="notifications ms-4">
                <i class="bi bi-bell-fill" style="font-size: 1.5em;"></i>
                {{-- <span class="badge bg-danger">3</span> --}}
            </div>
        </div>
    </nav>

    <!-- Content -->
    <div class="content">
        @yield('content')
    </div>

    <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-dark fw-bold" id="logoutModalLabel">Confirm Logout</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-dark">
                    Are you sure you want to logout?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmLogout">Logout</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Ensure only one course sub-menu is open at a time
        document.querySelectorAll('.course-toggle').forEach(function(toggle) {
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Get the target collapse element
                const collapseId = this.getAttribute('href');
                const collapseElement = document.querySelector(collapseId);
                
                // Toggle the collapse state
                const bsCollapse = new bootstrap.Collapse(collapseElement, {
                    toggle: true
                });

                // Close other open collapses
                document.querySelectorAll('.course-sub-menu.collapse.show').forEach(function(otherCollapse) {
                    if (otherCollapse !== collapseElement) {
                        new bootstrap.Collapse(otherCollapse, {
                            toggle: false
                        }).hide();
                    }
                });
            });
        });

        // Prevent sub-menu collapse when clicking links
        document.querySelectorAll('.course-sub-menu a').forEach(function(link) {
            link.addEventListener('click', function(e) {
                e.stopPropagation(); // Prevent event from bubbling up to toggle
            });
        });

        // Auto-expand course sub-menu based on current route
        document.addEventListener('DOMContentLoaded', function() {
            const currentRoute = window.location.pathname;
            const courseRoutes = ['students', 'attendance', 'assignments'];

            console.log(courseRoutes.some(route => currentRoute.includes(route)));

            if (courseRoutes.some(route => currentRoute.includes(route))) {
                const courseId = currentRoute.split('/').pop();
                console.log(courseId);
                const courseCollapse = document.querySelector(`#course-${courseId}`);
                if (courseCollapse) {
                    new bootstrap.Collapse(courseCollapse, {
                        show: true
                    });
                }
            }
        });

        document.addEventListener('DOMContentLoaded', function () {
            const logoutForm = document.getElementById('logout-form');
            const confirmLogoutButton = document.getElementById('confirmLogout');

            confirmLogoutButton.addEventListener('click', function () {
                logoutForm.submit(); 
            });
        });
    </script>
</body>
</html>