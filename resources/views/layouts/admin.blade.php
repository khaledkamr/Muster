<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - @yield('title')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css">
    <style>
        ::-webkit-scrollbar {
            width: 10px;
            height: 0; /* Prevent horizontal scrollbar */
        }
        ::-webkit-scrollbar-track {
            background: #121212 !important;
        }
        ::-webkit-scrollbar-thumb {
            background: #0A9442 !important;
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
            z-index: 2000;
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
        /* Styles for the collapsible menus */
        .toggle {
            color: #ffffff;
            padding: 15px 20px;
            display: flex;
            align-items: center;
            text-decoration: none;
            transition: 0.3s;
            cursor: pointer;
        }
        .toggle:hover {
            color: rgb(0, 140, 255);
            border-radius: 30px;
        }
        .toggle[aria-expanded="true"] {
            color: rgb(0, 140, 255);
        }
        .sub-menu {
            background-color: #1a1a1a;
            border-radius: 10px;
            margin: 0 10px 10px 10px;
        }
        .sub-menu a {
            padding: 10px 40px;
            font-size: 0.9rem;
        }
        .sub-menu a:hover {
            color: rgb(0, 140, 255);
            background-color: #2a2a2a;
        }
        .sub-menu a.active {
            background-color: #ffffff;
            color: black;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <img class="image pb-5" src="{{ asset('imgs/logo.png') }}" alt="MUST">

        <ul class="nav flex-column">
            <!-- Dashboard -->
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.home') ? 'active' : '' }}"
                   href="{{ route('admin.home') }}">
                   <i class="bi bi-columns-gap ps-1"></i> Dashboard
                </a>
            </li>

            <!-- Users -->
            <li class="nav-item">
                <a class="toggle" data-bs-toggle="collapse" href="#users" role="button" aria-expanded="false" aria-controls="users">
                    <i class="bi bi-person-fill"></i> Users
                </a>
                <div class="collapse sub-menu" id="users">
                    <a class="nav-link {{ request()->routeIs('admin.create.user') ? 'active' : '' }}"
                       href="{{ route('admin.create.user') }}">
                       <i class="fa-solid fa-user-plus ps-1"></i> Add User
                    </a>
                    <a class="nav-link {{ request()->routeIs('admin.show.users') ? 'active' : '' }}"
                       href="{{ route('admin.show.users') }}">
                       <i class="fa-solid fa-users ps-1"></i> Show Users
                    </a>
                </div>
            </li>

            <!-- Courses -->
            <li class="nav-item">
                <a class="toggle" data-bs-toggle="collapse" href="#courses" role="button" aria-expanded="false" aria-controls="courses">
                    <i class="fa-solid fa-layer-group"></i> Courses
                </a>
                <div class="collapse sub-menu" id="courses">
                    <a class="nav-link {{ request()->routeIs('admin.create.course') ? 'active' : '' }}"
                       href="{{ route('admin.create.course') }}">
                       <i class="fa-solid fa-circle-plus ps-1"></i> Add Course
                    </a>
                    <a class="nav-link {{ request()->routeIs('admin.show.courses') ? 'active' : '' }}"
                       href="{{ route('admin.show.courses') }}">
                       <i class="fa-solid fa-book-open ps-1"></i> Show Courses
                    </a>
                </div>
            </li>

            <!-- Manage AI Models -->
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.aiModels.overview') ? 'active' : '' }}"
                   href="{{ route('admin.feedbacks') }}">
                   <i class="fa-solid fa-comments"></i> Feedbacks
                </a>
            </li>

            <!-- Profile -->
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.profile') ? 'active' : '' }}"
                   href="{{ route('admin.profile') }}">
                    <i class="bi bi-person-circle"></i> Profile
                </a>
            </li>

            <!-- Logout -->
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
        <h3 class="text-dark pe-4">Muster</h3>
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
            </div>
        </div>
    </nav>

    <!-- Content -->
    <div class="content">
        @yield('content')
    </div>

    <!-- Logout Modal -->
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Ensure only one sub-menu is open at a time
        document.querySelectorAll('.toggle').forEach(function(toggle) {
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
                document.querySelectorAll('.sub-menu.collapse.show').forEach(function(otherCollapse) {
                    if (otherCollapse !== collapseElement) {
                        new bootstrap.Collapse(otherCollapse, {
                            toggle: false
                        }).hide();
                    }
                });
            });
        });

        // Prevent sub-menu collapse when clicking links
        document.querySelectorAll('.sub-menu a').forEach(function(link) {
            link.addEventListener('click', function(e) {
                e.stopPropagation(); // Prevent event from bubbling up to toggle
            });
        });

        // Auto-expand sub-menu based on current route
        document.addEventListener('DOMContentLoaded', function() {
            const currentRoute = window.location.pathname;
            const userRoutes = ['users/create', 'users'];
            const courseRoutes = ['courses/create', 'courses'];
            const aiModelsRoutes = ['feedbacks'];

            if (userRoutes.some(route => currentRoute.includes(route))) {
                const userCollapse = document.querySelector('#users');
                if (userCollapse) {
                    new bootstrap.Collapse(userCollapse, {
                        show: true
                    });
                }
            } else if (courseRoutes.some(route => currentRoute.includes(route))) {
                const courseCollapse = document.querySelector('#courses');
                if (courseCollapse) {
                    new bootstrap.Collapse(courseCollapse, {
                        show: true
                    });
                }
            } else if (aiModelsRoutes.some(route => currentRoute.includes(route))) {
                const aiModelsCollapse = document.querySelector('#AImodels');
                if (aiModelsCollapse) {
                    new bootstrap.Collapse(aiModelsCollapse, {
                        show: true
                    });
                }
            }
        });

        // Logout form submission
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