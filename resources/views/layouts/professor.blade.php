<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Professor Dashboard - @yield('title')</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background-color: #343a40;
            color: #ffffff;
            font-family: Arial, sans-serif;
            margin: 0;
        }
        .navbar {
            background-color: #495057;
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
            flex: 1;
        }
        .search input {
            background-color: #3a3c3f;
            border: none;
            color: #ffffff;
        }
        .search input::placeholder {
            color: #ced4da;
        }
        .search input:focus {
            background-color: #3a3d41;
            outline: none;
        }
        .sidebar {
            background-color: #495057;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            padding-top: 60px;
            box-shadow: 4px 0 8px rgba(0, 0, 0, 0.3);
            z-index: 2;
        }
        .sidebar a {
            color: #ffffff;
            padding: 15px;
            display: block;
            text-decoration: none;
        }
        .sidebar a:hover {
            background-color: #6c757d;
        }
        .content {
            margin-left: 250px;
            padding: 20px;
            padding-top: 70px;
        }
        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }
        .btn-danger:hover {
            background-color: #b02a37;
            border-color: #b02a37;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <a href="{{ route('professor.courses') }}">Courses</a>
        <a href="{{ route('professor.assignments') }}">Assignments</a>
        <a href="{{ route('professor.profile') }}">Profile</a>
    </div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <a class="navbar-brand text-white d-flex align-items-center" href="{{ route('professor.home') }}">
            <img src="{{ asset('imgs/logo.png') }}" alt="University Logo" style="height: 40px; margin-right: 10px;">
            Professor Dashboard
        </a>
        <form class="search d-flex" role="search" action="" method="GET">
            <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search" name="query">
            <button class="btn btn-outline-success ml-2" type="submit">
                <i class="fas fa-search"></i>
            </button>
        </form>
        <div class="ml-auto d-flex align-items-center pl-4">
            <span class="navbar-text mr-3 d-flex align-items-center">
                <i class="fas fa-user-circle" style="font-size: 1.5em; margin-right: 5px;"></i>
                <div>{{ Auth::user()->name }}</div>
            </span>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-danger">Logout</button>
            </form>
        </div>
    </nav>

    <!-- Content -->
    <div class="content">
        @yield('content')
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>