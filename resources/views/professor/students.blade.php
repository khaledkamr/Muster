@extends('layouts.professor')

@section('title', 'Students')

@section('content')
    <style>
        .nav-tabs .nav-link {
            color: #495057;
        }

        .nav-tabs .nav-link.active {
            background-color: #ffffff;
            border-color: #dee2e6 #dee2e6 #ffffff;
            color: #007bff;
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

        .table .status-average {
            background-color: #fff3cd;
            color: #856404;
            padding: 5px 10px;
            border-radius: 12px;
            font-size: 12px;
            display: inline-block;
        }

        .table .status-high {
            background-color: #d4edda;
            color: #155724;
            padding: 5px 10px;
            border-radius: 12px;
            font-size: 12px;
            display: inline-block;
        }

        .table .status-risk {
            background-color: #f8d7da;
            color: #721c24;
            padding: 5px 10px;
            border-radius: 12px;
            font-size: 12px;
            display: inline-block;
        }

        .action-icons a,
        .action-icons form {
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

        .action-icons form button {
            background: none;
            border: none;
            padding: 0;
        }

        .search-container {
            margin-bottom: 20px;
            display: flex;
            justify-content: flex-start;
        }

        .search-container input {
            width: 250px;
            margin-right: 10px;
        }

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
    </style>
    <div class="container">
        <h2 class="text-dark fw-bold pt-3 pb-5">{{ $course->name }} / Students</h2>

        <div class="row mb-4">
            <div class="col-md-4">
                <div class="d-flex flex-column gap-5">
                    <div>
                        <label for="statusFilter" class="form-label text-dark fw-bold">Filter by Performance:</label>
                        <form id="filterForm" method="GET" action="{{ route('professor.course.students', $courseId) }}"
                            class="d-flex flex-column">
                            <select id="statusFilter" name="status" class="form-select" onchange="this.form.submit()">
                                <option value="all"
                                    {{ request()->query('status') === 'all' || !request()->query('status') ? 'selected' : '' }}>
                                    All</option>
                                <option value="high" {{ request()->query('status') === 'high' ? 'selected' : '' }}>High
                                    Performance</option>
                                <option value="average" {{ request()->query('status') === 'average' ? 'selected' : '' }}>
                                    Average Performance</option>
                                <option value="low" {{ request()->query('status') === 'low' ? 'selected' : '' }}>Low
                                    Performance</option>
                            </select>
                            @if (request()->query('search'))
                                <input type="hidden" name="search" value="{{ request()->query('search') }}">
                            @endif
                        </form>
                    </div>
                    <div class="d-flex gap-2">
                        <form method="GET" action="{{ route('professor.course.students', $courseId) }}"
                            class="d-flex flex-column">
                            <label for="search" class="form-label text-dark fw-bold">Search for student:</label>
                            <div class="d-flex">
                                <input type="text" name="search" class="form-control me-2" style="width: 300px"
                                    placeholder="Search by ID or Name" value="{{ request()->query('search') }}">
                                <button type="submit" class="btn btn-primary"
                                    style="background-color: #0A9442;">Search</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="bg-white border-0 rounded-4 p-3 shadow" style="position: relative; height: 200px;">
                    <canvas id="genderPieChart"></canvas>
                </div>
            </div>
            <div class="col-md-4">
                <div class="bg-white border-0 rounded-4 p-3 shadow" style="position: relative; height: 200px;">
                    <canvas id="performancePieChart"></canvas>
                </div>
            </div>
        </div>

        <div class="table-container">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th class="text-center bg-dark text-white">ID</th>
                        <th class="text-center bg-dark text-white">Name</th>
                        <th class="text-center bg-dark text-white">Year</th>
                        <th class="text-center bg-dark text-white">Email</th>
                        <th class="text-center bg-dark text-white">Performance Group</th>
                        <th class="text-center bg-dark text-white">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($students->isEmpty())
                        <tr>
                            <td colspan="6" class="text-center">
                                <div class="status-refunded fs-6">No students enrolled in this course.</div>
                            </td>
                        </tr>
                    @else
                        @foreach ($students as $student)
                            <tr>
                                <td class="text-center">{{ $student->id }}</td>
                                <td class="text-center">
                                    <a href="{{ route('professor.student.profile', ['studentId' => $student->id, 'courseId' => $courseId]) }}"
                                        class="text-dark text-decoration-none">
                                        {{ $student->name }}
                                    </a>
                                </td>
                                <td class="text-center">{{ $student->year }}</td>
                                <td class="text-center">{{ $student->email }}</td>
                                <td class="text-center">
                                    <span
                                        class="status-{{ $clusteringStudents['students'][$student->id]['performance_group'] == 'High performers'
                                            ? 'high'
                                            : ($clusteringStudents['students'][$student->id]['performance_group'] == 'Average performers'
                                                ? 'average'
                                                : ($clusteringStudents['students'][$student->id]['performance_group'] == 'At risk'
                                                    ? 'risk'
                                                    : '')) }}">
                                        {{ $clusteringStudents['students'][$student->id]['performance_group'] }}
                                    </span>
                                </td>
                                <td class="action-icons text-center">
                                    <a href="{{ route('professor.course.student.details', ['course_id' => $courseId, 'student_id' => $student->id]) }}"
                                        title="View"><i class="fa-solid fa-eye"></i></a>
                                    <a href=""><i class="fa-solid fa-message" title="send"></i></a>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $students->appends(request()->query())->onEachSide(1)->links() }}
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Performance Pie Chart
        const highPerformanceCount = {{ $clusteringStudents['high_performance_count'] }};
        const averagePerformanceCount = {{ $clusteringStudents['average_performance_count'] }};
        const lowPerformanceCount = {{ $clusteringStudents['at_risk_students_count'] }};
        const total = highPerformanceCount + averagePerformanceCount + lowPerformanceCount;

        const pieCtx = document.getElementById('performancePieChart').getContext('2d');
        const performancePieChart = new Chart(pieCtx, {
            type: 'pie',
            data: {
                labels: ['High performance', 'Average performance', 'At risk students'],
                datasets: [{
                    data: [highPerformanceCount, averagePerformanceCount, lowPerformanceCount],
                    backgroundColor: ['#79f596', '#ffcc00', '#ff808a'],
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
                        text: 'Performance Distribution',
                        color: '#333',
                        font: {
                            size: 16,
                            weight: 'bold'
                        }
                    }
                }
            }
        });

        // Gender Pie Chart
        const maleCount = {{ $maleCount }};
        const femaleCount = {{ $femaleCount }};

        const genderPieCtx = document.getElementById('genderPieChart').getContext('2d');
        const genderPieChart = new Chart(genderPieCtx, {
            type: 'pie',
            data: {
                labels: ['Male', 'Female'],
                datasets: [{
                    data: [maleCount, femaleCount],
                    backgroundColor: ['#89CFF0', '#FFB6C1'],
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
                        text: 'Gender Distribution',
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
@endsection
