@extends('layouts.professor')

@section('title', 'Attendance')

@section('content')
<div class="container">
    <h2 class="text-dark fw-bold pt-2 pb-4">{{ $course->name }} / Attendance</h2>

    @if ($attendanceRecords->isNotEmpty())
        <div class="mb-5" style="position: relative; height: 400px;">
            <canvas id="weeklyAttendanceChart"></canvas>
        </div>
    @endif

    <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
            <a class="nav-link {{ request()->query('view', 'week1') === 'week1' ? 'active' : '' }}" href="?view=week1">Week 1</a>
        </li>
        @for ($i = 2; $i <= 10; $i++)
            <li class="nav-item">
                <a class="nav-link {{ request()->query('view') === 'week' . $i ? 'active' : '' }}" href="?view=week{{ $i }}">Week {{ $i }}</a>
            </li>
        @endfor
    </ul>

    <div class="container">
        @if ($attendanceRecords->isNotEmpty())
        <div class="row mb-4">
            <div class="col-md-6 mt-2">
                <div class="d-flex flex-column gap-2">
                    <!-- Filters and Search -->
                    <div class="row mb-4 mt-2">
                        <div class="col-md-6">
                            <label for="statusFilter" class="form-label text-dark fw-bold">Filter by Status:</label>
                            <select id="statusFilter" name="status" class="form-select" onchange="this.form.submit()" form="filterForm">
                                <option value="all" {{ $statusFilter === 'all' ? 'selected' : '' }}>All</option>
                                <option value="present" {{ $statusFilter === 'present' ? 'selected' : '' }}>Present</option>
                                <option value="absent" {{ $statusFilter === 'absent' ? 'selected' : '' }}>Absent</option>
                                <option value="late" {{ $statusFilter === 'late' ? 'selected' : '' }}>Late</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="typeFilter" class="form-label text-dark fw-bold">Filter by Session Type:</label>
                            <select id="typeFilter" name="type" class="form-select" onchange="this.form.submit()" form="filterForm">
                                <option value="all" {{ $typeFilter === 'all' ? 'selected' : '' }}>All</option>
                                <option value="lecture" {{ $typeFilter === 'lecture' ? 'selected' : '' }}>Lecture</option>
                                <option value="lab" {{ $typeFilter === 'lab' ? 'selected' : '' }}>Lab</option>
                            </select>
                        </div>
                    </div>
                    <!-- Search -->
                    <div class="search-container">
                        <form method="GET" action="{{ route('professor.course.attendance', $courseId) }}" class="d-flex flex-column">
                            <label for="search" class="form-label text-dark fw-bold">Search for student:</label>
                            <div class="d-flex">
                                <input type="text" name="search" class="form-control" placeholder="Search by ID or Name" value="{{ request()->query('search') }}">
                                <input type="hidden" name="view" value="{{ request()->query('view', 'assign1') }}">
                                <button type="submit" class="btn btn-primary" style="background-color: #0A9442;">Search</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4" style="position: relative; height: 200px;">
                <canvas id="statusPieChart"></canvas>
            </div>
        </div>
        @endif

        <!-- Hidden form to handle filters -->
        <form id="filterForm" method="GET" action="{{ route('professor.course.attendance', $courseId) }}">
            <input type="hidden" name="view" value="{{ request()->query('view', 'week1') }}">
        </form>

        <div class="table-container mb-5">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th class="bg-dark text-light text-center">Student ID</th>
                        <th class="bg-dark text-light text-center">Student Name</th>
                        <th class="bg-dark text-light text-center">Session Type</th>
                        <th class="bg-dark text-light text-center">Attendance Status</th>
                        <th class="bg-dark text-light text-center">Date</th>
                        <th class="bg-dark text-light text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @if($attendanceRecords->isEmpty())
                    <tr>
                        <td colspan="6" class="text-center">
                            <div class="status-absent fs-6">No students enrolled in this course.</div>
                        </td>
                    </tr>
                    @else
                    @foreach ($attendanceRecords as $record)
                        <tr>
                            <td class="text-center">{{ $record->student_id }}</td>
                            <td class="text-center">{{ $record->student->name }}</td>
                            <td class="text-center">{{ $record->type }}</td>
                            <td class="text-center">
                                <span class="status-{{$record->status}}">{{ ucfirst($record->status) }}</span>
                            </td>
                            <td class="text-center">{{ $record->date->format('Y-m-d') }}</td>
                            <td class="action-icons text-center">
                                <a href="{{ route('professor.student.profile', ['studentId' => $record->student->id, 'courseId' => $courseId]) }}" title="View"><i class="fa-solid fa-eye"></i></a>
                                <a href=""><i class="fa-solid fa-message" title="send"></i></a>
                            </td>
                        </tr>
                    @endforeach
                    @endif
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $attendanceRecords->appends(request()->query())->onEachSide(1)->links() }}
        </div>
    </div>
</div>

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Prepare data for the weekly attendance line chart
    const labels = @json(array_keys($weeklyAttendance));
    const presentData = @json(array_column($weeklyAttendance, 'present'));
    const absentData = @json(array_column($weeklyAttendance, 'absent'));
    const lateData = @json(array_column($weeklyAttendance, 'late'));

    const ctx = document.getElementById('weeklyAttendanceChart').getContext('2d');
    const weeklyAttendanceChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels.map(label => label.replace('week', 'Week ')),
            datasets: [
                {
                    label: 'Late',
                    data: lateData,
                    borderColor: '#ffcc00',
                    backgroundColor: 'rgba(255, 204, 0, 0.2)',
                    tension: 0.1,
                    fill: true
                },
                {
                    label: 'Absent',
                    data: absentData,
                    borderColor: '#ff808a',
                    backgroundColor: 'rgba(255, 128, 138, 0.2)',
                    tension: 0.1,
                    fill: true
                },
                {
                    label: 'Present',
                    data: presentData,
                    borderColor: '#79f596',
                    backgroundColor: 'rgba(121, 245, 150, 0.2)',
                    tension: 0.1,
                    fill: true
                },
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        color: '#333'
                    }
                },
                title: {
                    display: true,
                    text: 'Attendance Trends Over Weeks',
                    color: '#333',
                    font: {
                        size: 16,
                        weight: 'bold'
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Number of Students',
                        color: '#333'
                    },
                    ticks: {
                        color: '#333'
                    }
                },
                x: {
                    title: {
                        display: true,
                        color: '#333'
                    },
                    ticks: {
                        color: '#333'
                    }
                }
            }
        }
    });

    // Prepare data for the pie chart
    const presentCount = {{ $attendanceRecords->where('status', 'present')->count() }};
    const absentCount = {{ $attendanceRecords->where('status', 'absent')->count() }};
    const lateCount = {{ $attendanceRecords->where('status', 'late')->count() }};
    const total = presentCount + absentCount + lateCount;

    const pieCtx = document.getElementById('statusPieChart').getContext('2d');
    const statusPieChart = new Chart(pieCtx, {
        type: 'pie',
        data: {
            labels: ['Present', 'Absent', 'Late'],
            datasets: [{
                data: [presentCount, absentCount, lateCount],
                backgroundColor: ['#79f596', '#ff808a', '#ffcc00'],
                borderWidth: 1,
                borderColor: '#eee'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        color: '#333'
                    }
                },
                title: {
                    display: true,
                    text: 'Attendance Status Distribution',
                    color: '#333',
                    font: {
                        size: 16,
                        weight: 'bold'
                    }
                }
            }
        }
    });
</script>

<style>
    .pagination {
        margin: 0;
        padding: 0;
    }
    .pagination .page-item .page-link {
        color: #0A9442;
        border: 1px solid #dee2e6;
        padding: 8px 16px;
        margin: 0 2px;
        border-radius: 4px;
        transition: 0.3s;
    }
    .pagination .page-item.active .page-link {
        background-color: #0A9442;
        border-color: #0A9442;
        color: white;
    }
    .pagination .page-item .page-link:hover {
        background-color: #0A9442;
        border-color: #0A9442;
        color: white;
    }
    .pagination .page-item.disabled .page-link {
        color: #fff;
        pointer-events: none;
        background-color: #d5d7d8;
        border-color: #dee2e6;
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
    .table .status-present {
        background-color: #d4edda;
        color: #155724;
        padding: 5px 10px;
        border-radius: 12px;
        font-size: 12px;
        display: inline-block;
    }
    .table .status-absent {
        background-color: #f8d7da;
        color: #721c24;
        padding: 5px 10px;
        border-radius: 12px;
        font-size: 12px;
        display: inline-block;
    }
    .table .status-late {
        background-color: #fff3cd;
        color: #856404;
        padding: 5px 10px;
        border-radius: 12px;
        font-size: 12px;
        display: inline-block;
    }
    .action-icons a, .action-icons form {
        display: inline-block;
        margin-right: 5px;
    }
    .action-icons i {
        font-size: 16px;
        color: #6c757d;
    }
    .action-icons i:hover {
        color: #007bff;
    }
    .action-icons .delete-icon:hover {
        color: #dc3545;
    }
    .search-container {
        margin-bottom: 20px;
        display: flex;
        justify-content: flex-start;
    }
    .search-container input {
        width: 300px;
        margin-right: 10px;
    }
    .nav-tabs .nav-link {
        color: #495057;
    }
    .nav-tabs .nav-link.active {
        background-color: #ffffff;
        border-color: #dee2e6 #dee2e6 #ffffff;
        color: #007bff;
    }
</style>
@endsection