@extends('layouts.student')

@section('title', 'Grades')

@section('content')
    <div class="container">
        <h1 class="pb-3 pt-3 text-dark fw-bold">Your Grades</h1>

        <div class="row mb-4">
            <div class="col-md-6">
                <label for="semesterFilter" class="form-label text-dark fw-bold">Select Semester:</label>
                <select id="semesterFilter" name="semester" onchange="this.form.submit()" form="semesterForm" class="form-select" style="max-width: 300px;">
                    <option value="" {{ $selectedSemester === null ? 'selected' : '' }} disabled>Select a semester</option>
                    @foreach ($semesters as $value => $label)
                        <option value="{{ $value }}" {{ $value == $selectedSemester ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <form action="" id="semesterForm" method="GET"></form>
        </div>

        <!-- Semester Statistics -->
        @if($selectedSemester !== null)
            <div class="row mb-4" id="semesterStats">
                <div class="col-md-4">
                    <div class="bg-white rounded-4 p-3 shadow-sm">
                        <div class="rounded-4">
                            <h5 class="card-title text-dark fw-bold">Semester Statistics</h5>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted">Earned Hours:</span>
                                <span class="fw-bold text-dark" id="semesterCredits">
                                    {{ $semesterStatistics['semester_credits'] ?? 0 }}
                                </span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">Semester GPA:</span>
                                <span class="fw-bold text-dark" id="semesterGPA">
                                    @if($semesterStatistics['semester_gpa'])
                                        {{ $semesterStatistics['semester_gpa'] }}
                                    @else
                                        <i class="fa-solid fa-ban text-muted"></i>
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="bg-white rounded-4 p-3 shadow-sm">
                        <div class="rounded-4">
                            <h5 class="card-title text-dark fw-bold">Cumulative Statistics</h5>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted">Total Hours:</span>
                                <span class="fw-bold text-dark" id="totalCredits">
                                    {{ $semesterStatistics['total_credits'] ?? 0 }}
                                </span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">CGPA:</span>
                                <div class="d-flex align-items-center">
                                    <span class="fw-bold me-2 text-dark" id="cgpa">
                                        {{ $semesterStatistics['cgpa'] }}
                                    </span>
                                    @if($semesterStatistics['cgpa']) 
                                        @if($semesterStatistics['cgpa_status'] == 'up')
                                            <i id="cgpaTrend" class="fas fa-circle-up text-success"></i>
                                        @elseif($semesterStatistics['cgpa_status'] == 'down')
                                            <i id="cgpaTrend" class="fas fa-circle-down text-danger"></i>
                                        @else
                                            <i id="cgpaTrend" class="fas fa-circle text-secondary"></i>
                                        @endif
                                    @else
                                        <i class="fa-solid fa-ban text-muted"></i>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="bg-white rounded-4 p-3 shadow-sm">
                        <div class="rounded-4">
                            <h5 class="card-title text-dark fw-bold">Department Statistics</h5>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted">Total Students:</span>
                                <span class="fw-bold text-dark" id="totalStudents">
                                    {{ $semesterStatistics['department_students'] ?? 0 }}
                                </span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">Avg CGPA:</span>
                                <div class="d-flex align-items-center">
                                    <span class="fw-bold text-dark" id="avgcgpa">
                                        {{ $semesterStatistics['department_avg_cgpa'] ?? 0.00 }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="table-container mb-4">
            <table class="table table-striped" id="gradesTable">
                <thead>
                    <tr>
                        <th class="bg-dark text-light text-center" width="10%">Course Code</th>
                        <th class="bg-dark text-light text-center" width="20%">Course Name</th>
                        <th class="bg-dark text-light text-center" width="10%">Hours</th>
                        <th class="bg-dark text-light text-center" width="15%">Difficulty</th>
                        <th class="bg-dark text-light text-center" width="15%">Type</th>
                        <th class="bg-dark text-light text-center" width="10%">Grade</th>
                        <th class="bg-dark text-light text-center" width="20%">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @if($selectedSemester == null)
                        <tr>
                            <td colspan="7" class="text-center">
                                <div class="status-danger fs-6">No semester selected!</div>
                            </td>
                        </tr>
                    @else
                        @foreach ($grades as $grade)
                            <tr>    
                                <td class="text-center">{{ $grade['course']->code }}</td>
                                <td class="text-center">{{ $grade['course']->name }}</td>
                                <td class="text-center">{{ $grade['course']->credit_hours }}</td>
                                <td class="text-center">{{ $grade['course']->difficulty }}</td>
                                <td class="text-center">{{ $grade['course']->type }}</td>
                                <td class="text-center text-{{ $grade->status === 'pass' ? 'success' : 'danger' }}">{{ $grade->grade ?? '-' }}</td>
                                <td class="text-center action-icons">
                                    <a href="{{ route('student.course-details', $grade['course']->id) }}" class="btn btn-sm btn-success text-light ps-3 pe-3">
                                        <i class="fa-solid fa-arrow-up-right-from-square text-light"></i> Details
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>

        <div class="row">
            <!-- Grade Distribution Chart -->
            @if($selectedSemester !== null && $gradesDistribution)
                <div class="col-md-6 mb-4" id="gradeDistribution">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title text-dark fw-bold">Grades Distribution</h5>
                                <canvas id="gradeDistributionChart" style="max-height: 300px;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- GPA Trend Chart -->
            @if($selectedSemester !== null && $cgpaTrend)
                <div class="col-md-6 mb-4" id="gpaTrend">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title text-dark fw-bold">CGPA Trend</h5>
                                <canvas id="gpaTrendChart" style="max-height: 300px;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const gradesDistribution = @json($gradesDistribution);
            const gradeLabels = @json($gradeLabels);
            const gpaTrendData = @json($cgpaTrend);

            const gradeDistributionChart = document.getElementById('gradeDistributionChart');
            const gpaTrendChart = document.getElementById('gpaTrendChart');

            const semesterLabels = Object.keys(gpaTrendData).map(date => {
                const d = new Date(date);
                return d.toLocaleString('default', { month: 'short', year: 'numeric' });
            });

            new Chart(gpaTrendChart, {
                type: 'line',
                data: {
                    labels: semesterLabels,
                    datasets: [{
                        label: 'Semester GPA',
                        data: Object.values(gpaTrendData).map(item => item.cgpa),
                        borderColor: '#28a745',
                        backgroundColor: 'rgba(40, 167, 69, 0.1)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#28a745',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 4.0,
                            ticks: {
                                stepSize: 1,
                                callback: function(value) {
                                    return value.toFixed(1);
                                }
                            },
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                maxRotation: 45,
                                minRotation: 30
                            },
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                title: function(context) {
                                    return semesterLabels[context[0].dataIndex];
                                },
                                label: function(context) {
                                    return `GPA: ${context.parsed.y.toFixed(2)}`;
                                }
                            }
                        }
                    }
                }
            });

            new Chart(gradeDistributionChart, {
                type: 'bar',
                data: {
                    labels: gradeLabels,
                    datasets: [{
                        label: 'NO. Courses',
                        data: gradesDistribution,
                        backgroundColor: [
                            'rgb(40, 167, 69, 0.8)', // A+
                            'rgb(40, 167, 69, 0.8)', // A
                            'rgb(40, 167, 69, 0.8)', // A-
                            'rgb(23, 162, 184, 0.8)', // B+
                            'rgb(23, 162, 184, 0.8)', // B
                            'rgb(23, 162, 184, 0.8)', // B-
                            'rgb(255, 193, 7, 0.8)', // C+
                            'rgb(255, 193, 7, 0.8)', // C
                            'rgb(255, 193, 7, 0.8)', // C-
                            'rgb(220, 53, 69, 0.8)', // D+
                            'rgb(220, 53, 69, 0.8)', // D
                            'rgb(220, 53, 69, 0.8)', // D-
                            'rgb(220, 53, 69, 0.8)' // F
                        ],
                        borderColor: [
                            'rgb(40, 167, 69, 0.8)', // A+
                            'rgb(40, 167, 69, 0.8)', // A
                            'rgb(40, 167, 69, 0.8)', // A-
                            'rgb(23, 162, 184, 0.8)', // B+
                            'rgb(23, 162, 184, 0.8)', // B
                            'rgb(23, 162, 184, 0.8)', // B-
                            'rgb(255, 193, 7, 0.8)', // C+
                            'rgb(255, 193, 7, 0.8)', // C
                            'rgb(255, 193, 7, 0.8)', // C-
                            'rgb(220, 53, 69, 0.8)', // D+
                            'rgb(220, 53, 69, 0.8)', // D
                            'rgb(220, 53, 69, 0.8)', // D-
                            'rgb(220, 53, 69, 0.8)' // F
                        ],
                        borderWidth: 1,
                        barRadius: 5,
                        borderRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            },
                            title: {
                                display: true,
                                text: 'Number of Courses'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        });
    </script>

    <style>
        .table-container {
            /* color: #ec5690; */
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

        .card {
            border: none;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        .card-body {
            padding: 1.5rem;
        }

        .card-title {
            margin-bottom: 1.5rem;
            font-size: 1.1rem;
        }

        .text-muted {
            color: #6c757d !important;
        }

        .fw-bold {
            font-weight: 600 !important;
        }

        .status-danger {
            background-color: #f8d7da;
            color: #721c24;
            padding: 5px 10px;
            border-radius: 12px;
            font-size: 12px;
            display: inline-block;
        }
    </style>
@endsection
