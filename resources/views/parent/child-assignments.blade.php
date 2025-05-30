@extends('layouts.parent')

@section('title', 'Assignments')

@section('content')
<div class="container">
    <h1 class="pb-3 pt-3 text-dark fw-bold">{{ $child->name }}'s Assignments</h1>

    <!-- Tabs for Upcoming Assignments and Assignments -->
    <div class="container">
        <ul class="nav nav-tabs mb-4">
            <li class="nav-item">
                <a class="nav-link {{ request()->query('view', 'assignments') === 'assignments' ? 'active' : '' }}" href="?view=assignments">Assignments</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->query('view') === 'upcoming' ? 'active' : '' }}" href="?view=upcoming">Upcoming Assignments</a>
            </li>
        </ul>

        <div class="row mb-4">
            <!-- Filters and Search -->
            @if (request()->query('view', 'assignments') === 'assignments')
                <div class="col-md-6 d-flex flex-column justify-content-center">
                    <div class="col-md-6 mb-3">
                        <label for="statusFilter" class="form-label text-dark fw-bold">Filter by Status:</label>
                        <select id="statusFilter" name="status" class="form-select" onchange="this.form.submit()" form="filterForm">
                            <option value="all" {{ $statusFilter === 'all' ? 'selected' : '' }}>All</option>
                            <option value="submitted" {{ $statusFilter === 'submitted' ? 'selected' : '' }}>Submitted</option>
                            <option value="pending" {{ $statusFilter === 'pending' ? 'selected' : '' }}>Pending</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="search" class="form-label text-dark fw-bold">Search by Course:</label>
                        <div class="input-group">
                            <input type="text" id="search" name="search" 
                                class="form-control shadow-sm border-end-0" 
                                value="{{ $searchQuery }}" 
                                placeholder="Search by course code or name" 
                                form="filterForm"
                                style="border-radius: 20px 0 0 20px;">
                            <span class="input-group-text bg-white border-start-0" style="border-radius: 0 20px 20px 0;">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                        </div>
                    </div>

                    <!-- Hidden form to handle filters -->
                    <form id="filterForm" method="GET" action="{{ route('parent.child.assignments', $childId) }}">
                        <input type="hidden" name="view" value="assignments">
                    </form>
                </div>
                
                <!-- Progress Circles -->
                <div class="col-md-3 text-center">
                    <div class="bg-white p-3 rounded-4 shadow">
                        <h5 class="text-dark fw-bold mt-2">Assignment Completion</h5>
                        <div class="position-relative d-inline-block" style="width: 150px; height: 100px;">
                            <canvas id="completionChart"></canvas>
                            <div class="position-absolute top-50 start-50 translate-middle text-center">
                                <span class="fs-5 text-dark fw-bold">{{ $completionPercentage }}%</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 text-center">
                    <div class="bg-white p-3 rounded-4 shadow">
                        <h5 class="text-dark fw-bold mt-2">Assignment Score Rate</h5>
                        <div class="position-relative d-inline-block" style="width: 150px; height: 100px;">
                            <canvas id="scoreChart"></canvas>
                            <div class="position-absolute top-50 start-50 translate-middle text-center">
                                <span class="fs-5 text-dark fw-bold">{{ $scorePercentage }}%</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Assignments Display -->
        @if (request()->query('view', 'assignments') === 'assignments')
            @if ($filteredSubmissions->isNotEmpty())
                <div class="table-container">
                    <table class="table table-striped">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center bg-dark text-white">Assignment Title</th>
                                <th class="text-center bg-dark text-white">Course</th>
                                <th class="text-center bg-dark text-white">Professor</th>
                                <th class="text-center bg-dark text-white">Status</th>
                                <th class="text-center bg-dark text-white">Submitted Date</th>
                                <th class="text-center bg-dark text-white">Due Date</th>
                                <th class="text-center bg-dark text-white">Score</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($filteredSubmissions as $submission)
                            <tr>
                                <td class="fw-bold text-center">{{ $submission->assignment->title }}</td>
                                <td class="text-center">{{ $submission->assignment->course->name }}</td>
                                <td class="text-center">
                                    <a class="text-dark text-decoration-none hover-underline" href="{{ route('parent.professor-profile', $submission->assignment->course->professor->id) }}">
                                        {{ $submission->assignment->course->professor->name }}
                                    </a>
                                </td>
                                <td class="text-center">
                                    <span class="badge status-{{ $submission->status }}">
                                        {{ ucfirst($submission->status) }}
                                    </span>
                                </td>
                                <td class="text-center">{{ $submission->submitted_at ? $submission->submitted_at->format('M d, Y') : '-' }}</td>
                                <td class="text-center">{{ $submission->assignment->due_date->format('M d, Y') }}</td>
                                <td class="text-light text-center ">
                                    <div class="badge bg-primary" style="font-size: 12px;">{{ $submission->score ?? '-' }}</div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
            <p class="text-center text-dark">No assignments found.</p>
            @endif
        @else
            <!-- Upcoming Assignments -->
            @if (!empty($upcomingAssignments))
                <div class="table-container">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th class="text-center bg-dark text-white">Assignment Title</th>
                                <th class="text-center bg-dark text-white">Course</th>
                                <th class="text-center bg-dark text-white">Professor</th>
                                <th class="text-center bg-dark text-white">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($currentSemesterCourses as $course)
                                @foreach ($upcomingAssignments as $upcomingAssignment)
                                    <tr>
                                        <td class="text-center">{{ $upcomingAssignment }}</td>
                                        <td class="text-center">{{ $course->code }}: {{ $course->name }}</td>
                                        <td class="text-center">{{ $course->professor->name }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-warning text-dark">Not Posted Yet</span>
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-center text-dark">No upcoming assignments.</p>
            @endif
        @endif
    </div>
</div>


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
        .table-container {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }
    .table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 0;
    }
    .table thead {
        background-color: #f8f9fa;
        color: #333;
    }
    .table th {
        padding: 15px;
        text-align: left;
        font-weight: 600;
        font-size: 14px;
        border-bottom: 1px solid #e9ecef;
    }
    .table td {
        padding: 15px;
        font-size: 14px;
        color: #333;
        border-bottom: 1px solid #e9ecef;
    }
    .table tbody tr:hover {
        background-color: #f1f3f5;
    }
    .table .status-submitted {
        background-color: #d4edda;
        color: #155724;
        padding: 5px 10px;
        border-radius: 12px;
        font-size: 12px;
        display: inline-block;
    }
    .table .status-pending {
        background-color: #f8d7da;
        color: #721c24;
        padding: 5px 10px;
        border-radius: 12px;
        font-size: 12px;
        display: inline-block;
    }
    </style>
@endsection