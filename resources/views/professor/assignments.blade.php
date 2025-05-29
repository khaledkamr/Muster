@extends('layouts.professor')

@section('title', 'Assignments')

@section('content')
<div class="container">
    <h2 class="text-dark fw-bold pt-2 pb-4">{{ $course->name }} / Assignments</h2>

    <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
            <a class="nav-link {{ request()->query('view', 'assign1') === 'assign1' ? 'active' : '' }}" href="?view=assign1">Assignment 1</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->query('view') === 'assign2' ? 'active' : '' }}" href="?view=assign2">Assignment 2</a>
        </li>
        <li class="nav-item"> 
            <a class="nav-link" href="">+</a>
        </li>
    </ul>

    <div class="container">
        @if ($filteredSubmissions->isNotEmpty())
            <div class="row mb-4">
                <div class="col-md-6 mt-2">
                    <div class="d-flex flex-column gap-5">
                        <!-- Filters and Search -->
                        <div>
                            <label for="statusFilter" class="form-label text-dark fw-bold">Filter by Status:</label>
                            <select id="statusFilter" name="status" class="form-select" onchange="this.form.submit()" form="filterForm">
                                <option value="all" {{ $statusFilter === 'all' ? 'selected' : '' }}>All</option>
                                <option value="submitted" {{ $statusFilter === 'submitted' ? 'selected' : '' }}>Submitted</option>
                                <option value="pending" {{ $statusFilter === 'pending' ? 'selected' : '' }}>Pending</option>
                            </select>
                        </div>
                        <div class="d-flex gap-2">
                            <form method="GET" action="{{ route('professor.course.assignments', $courseId) }}" class="d-flex flex-column">
                                <label for="search" class="form-label text-dark fw-bold">Search for student:</label>
                                <div class="d-flex">
                                    <input type="text" name="search" class="form-control me-2" style="width: 500px" placeholder="Search by ID or Name" value="{{ request()->query('search') }}">
                                    <input type="hidden" name="view" value="{{ request()->query('view', 'assign1') }}">
                                    <button type="submit" class="btn btn-primary" style="background-color: #0A9442;">Search</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    
                </div>
                <!-- Pie Chart -->
                <div class="col-md-4 mb-4">
                    <div class="bg-white border-0 rounded-4 p-3 shadow-sm" style="position: relative; height: 200px;">
                        <canvas id="statusPieChart"></canvas>
                    </div>
                </div>
            </div>
        @endif
        
        <!-- Hidden form to handle filters -->
        <form id="filterForm" method="GET" action="{{ route('professor.course.assignments', $courseId) }}">
            <input type="hidden" name="view" value="{{ request()->query('view', 'assign1') }}">
        </form>

        <div class="table-container">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th class="bg-dark text-light text-center">Student ID</th>
                        <th class="bg-dark text-light text-center">Student Name</th>
                        <th class="bg-dark text-light text-center">Assignment Title</th>
                        <th class="bg-dark text-light text-center">Assignment Status</th>
                        <th class="bg-dark text-light text-center">Submission Date</th>
                        <th class="bg-dark text-light text-center">Score</th>
                        <th class="bg-dark text-light text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @if($filteredSubmissions->isEmpty())
                    <tr>
                        <td colspan="6" class="text-center">
                            <div class="status-pending fs-6">No students enrolled in this course.</div>
                        </td>
                    </tr>
                    @else
                    @foreach($filteredSubmissions as $submission)
                        <tr>
                            <td class="text-center">{{ $submission->student_id }}</td>
                            <td class="text-center">
                                <a href="{{ route('professor.student.profile', ['studentId' => $submission->user->id, 'courseId' => $courseId]) }}"
                                    class="text-dark text-decoration-none">
                                    {{ $submission->user->name }}
                                </a>
                            </td>
                            <td class="text-center">{{ $submission->assignment->title }}</td>
                            <td class="text-center">
                                <span class="status-{{ $submission->status }}">{{ ucfirst($submission->status) }}</span>
                            </td>
                            <td class="text-center text-{{ $submission->status === 'pending' ? 'danger' : 'dark' }}">
                                {{ $submission->submitted_at ? $submission->submitted_at->format('Y-m-d') : 'Not submitted' }}
                            </td>
                            <td class="text-center">
                                <span class="badge bg-primary">{{ $submission->score }}</span>
                            </td>
                            <td class="action-icons text-center">
                                <a href="{{ route('professor.course.student.details', ['course_id' => $courseId, 'student_id' => $submission->user->id]) }}" title="View"><i class="fa-solid fa-eye"></i></a>
                                <a href=""><i class="fa-solid fa-message" title="send"></i></a>
                            </td>
                        </tr>
                    @endforeach
                    @endif
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $filteredSubmissions->appends(request()->query())->onEachSide(1)->links() }}
        </div>
    </div>
</div>


<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Prepare data for the pie chart
    const submittedCount = {{ $filteredSubmissions->where('status', 'submitted')->count() }};
    const pendingCount = {{ $filteredSubmissions->where('status', 'pending')->count() }};
    const total = submittedCount + pendingCount;

    const ctx = document.getElementById('statusPieChart').getContext('2d');
    const statusPieChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Submitted', 'Pending'],
            datasets: [{
                data: [submittedCount, pendingCount],
                backgroundColor: ['#79f596', '#ff808a'],
                borderWidth: 1,
                borderColor: '#eee'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'left',
                    labels: {
                        padding: 20,
                        color: '#333'
                    }
                },
                title: {
                    display: true,
                    text: 'Submission Status Distribution',
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
    .chart-container {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        padding: 20px;
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