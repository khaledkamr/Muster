<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - @yield('title')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #002361;
            --secondary: #0A9442;
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
            z-index: 100;
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
        .search button {
            border: none;
            background: none;
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
            /* margin: 0px 20px; */
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
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <img class="image pb-4" src="{{asset("imgs/logo.png")}}" alt="MUST">
        {{-- <h2 class="text-center fw-bold">Muster</h2> --}}
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('student.home') ? 'active' : '' }}" href="{{ route('student.home') }}">
                    <i class="fa-solid fa-house"></i> Home
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('student.grades') ? 'active' : '' }}" href="{{ route('student.grades') }}">
                    <i class="bi bi-award"></i> Grades
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('student.assignments') ? 'active' : '' }}" href="{{ route('student.assignments') }}">
                    <i class="bi bi-list-task"></i> Assignments
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('student.attendance') ? 'active' : '' }}" href="{{ route('student.attendance') }}">
                    <i class="fa-solid fa-clipboard-user"></i> Attendance
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('student.profile') ? 'active' : '' }}" href="{{ route('student.profile') }}">
                    <i class="bi bi-person-fill"></i> Profile
                </a>
            </li>
            <li class="nav-item">
                <form action="{{ route('logout') }}" method="POST" id="logout-form">
                    @csrf
                    <button type="button" class="text" data-bs-toggle="modal" data-bs-target="#logoutModal">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i> Logout
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
                {{-- <span class="badge bg-danger">1</span> --}}
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
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