@extends('layouts.student')

@section('title', 'Assignments')

@section('content')
    <h1 class="pb-3 pt-3 text-dark fw-bold">Your Assignments</h1>

    <!-- Tabs for Upcoming Assignments and Assignments -->
    <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
            <a class="nav-link {{ request()->query('view', 'assignments') === 'assignments' ? 'active' : '' }}" href="?view=assignments">Assignments</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->query('view') === 'upcoming' ? 'active' : '' }}" href="?view=upcoming">Upcoming Assignments</a>
        </li>
    </ul>

    <!-- Progress Circles -->
    <div class="row mb-4">
        <div class="col-md-6 text-center">
            <div class="position-relative d-inline-block" style="width: 150px; height: 150px;">
                <canvas id="completionChart"></canvas>
                <div class="position-absolute top-50 start-50 translate-middle text-center">
                    <span class="fs-3 text-dark fw-bold">{{ $completionPercentage }}%</span>
                </div>
            </div>
            <h5 class="text-dark mt-2">Assignment Completion</h5>
        </div>
        <div class="col-md-6 text-center">
            <div class="position-relative d-inline-block" style="width: 150px; height: 150px;">
                <canvas id="scoreChart"></canvas>
                <div class="position-absolute top-50 start-50 translate-middle text-center">
                    <span class="fs-3 text-dark fw-bold">{{ $scorePercentage }}%</span>
                </div>
            </div>
            <h5 class="text-dark mt-2">Assignment Score Rate</h5>
        </div>
    </div>

    <!-- Filters and Search -->
    @if (request()->query('view', 'assignments') === 'assignments')
        <div class="row mb-4">
            <div class="col-md-6">
                <label for="statusFilter" class="form-label text-dark fw-bold">Filter by Status:</label>
                <select id="statusFilter" name="status" class="form-select" onchange="this.form.submit()" form="filterForm">
                    <option value="all" {{ $statusFilter === 'all' ? 'selected' : '' }}>All</option>
                    <option value="submitted" {{ $statusFilter === 'submitted' ? 'selected' : '' }}>Submitted</option>
                    <option value="pending" {{ $statusFilter === 'pending' ? 'selected' : '' }}>Pending</option>
                </select>
            </div>
            <div class="col-md-6">
                <label for="search" class="form-label text-dark fw-bold">Search by Course:</label>
                <input type="text" id="search" name="search" class="form-control" value="{{ $searchQuery }}" placeholder="Search by course code or name" form="filterForm">
            </div>
        </div>

        <!-- Hidden form to handle filters -->
        <form id="filterForm" method="GET" action="{{ route('student.assignments') }}">
            <input type="hidden" name="view" value="assignments">
        </form>
    @endif

    <!-- Assignments Display -->
    @if (request()->query('view', 'assignments') === 'assignments')
        @if ($filteredSubmissions->isNotEmpty())
            <div class="row">
                @foreach ($filteredSubmissions as $submission)
                    <div class="col-md-6 mb-4">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title text-dark">{{ $submission->assignment->title }}</h5>
                                <p class="card-text text-dark">
                                    <strong>Course:</strong> {{ $submission->assignment->course->code }}: {{ $submission->assignment->course->name }}<br>
                                    <strong>Professor:</strong> {{ $submission->assignment->course->professor->name }}<br>
                                    <strong>Status:</strong> 
                                    <span class="badge bg-{{ $submission->status === 'submitted' ? 'success' : 'danger' }}">
                                        {{ ucfirst($submission->status) }}
                                    </span><br>
                                    <strong>Submitted At:</strong> {{ $submission->submitted_at ? $submission->submitted_at->format('M d, Y') : 'N/A' }}<br>
                                    <strong>Due Date:</strong> {{ $submission->assignment->due_date->format('M d, Y') }}<br>
                                    <strong>Score:</strong> {{ $submission->score ?? 'N/A' }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-center text-dark">No assignments found.</p>
        @endif
    @else
        <!-- Upcoming Assignments -->
        @if (!empty($upcomingAssignments))
            <div class="row">
                @foreach ($currentSemesterCourses as $course)
                    @foreach ($upcomingAssignments as $upcomingAssignment)
                        <div class="col-md-6 mb-4">
                            <div class="card shadow-sm">
                                <div class="card-body">
                                    <h5 class="card-title text-dark">{{ $upcomingAssignment }}</h5>
                                    <p class="card-text text-dark">
                                        <strong>Course:</strong> {{ $course->code }}: {{ $course->name }}<br>
                                        <strong>Professor:</strong> {{ $course->professor->name }}<br>
                                        <strong>Status:</strong> <span class="badge bg-warning">Not Yet Posted</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endforeach
            </div>
        @else
            <p class="text-center text-dark">No upcoming assignments.</p>
        @endif
    @endif

    <!-- Include Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Completion Chart
            const completionCtx = document.getElementById('completionChart').getContext('2d');
            const completionPercentage = {{ $completionPercentage }};
            let completionColor = completionPercentage >= 75 ? '#28a745' : (completionPercentage <= 50 ? '#dc3545' : '#007bff');

            new Chart(completionCtx, {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: [completionPercentage, 100 - completionPercentage],
                        backgroundColor: [completionColor, '#e9ecef'],
                        borderWidth: 0,
                        circumference: 360,
                        cutout: '80%',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: { enabled: false }
                    }
                }
            });

            // Score Chart
            const scoreCtx = document.getElementById('scoreChart').getContext('2d');
            const scorePercentage = {{ $scorePercentage }};
            let scoreColor = scorePercentage >= 75 ? '#28a745' : (scorePercentage <= 50 ? '#dc3545' : '#007bff');

            new Chart(scoreCtx, {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: [scorePercentage, 100 - scorePercentage],
                        backgroundColor: [scoreColor, '#e9ecef'],
                        borderWidth: 0,
                        circumference: 360,
                        cutout: '80%',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: { enabled: false }
                    }
                }
            });
        });
    </script>

    <style>
        .card {
            background-color: #ffffff;
            border: none;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .nav-tabs .nav-link {
            color: #495057;
        }
        .nav-tabs .nav-link.active {
            background-color: #ffffff;
            border-color: #dee2e6 #dee2e6 #ffffff;
            color: #007bff;
        }
        .form-select, .form-control {
            background-color: #ffffff;
            border: 1px solid #ced4da;
            color: #495057;
        }
        .form-select:focus, .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
    </style>
@endsection