@extends('layouts.student')

@section('title', 'Course Details - ' . $course->name)

@section('content')
    <style>
        .grades .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.288);
        }
    </style>
    <div class="d-flex align-items-center mb-2 pt-3">
        <a href="{{ url()->previous() }}" class="btn btn-secondary me-3">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <h1 class="text-dark fw-bold mb-0">{{ $course->code }}</h1>
    </div>

    <div class="d-flex align-items-center mb-5">
        <h4 class="text-dark fw-bold mb-0">{{ $course->name }}</h4>
        <span class="text-dark ms-3">{{ $course->credit_hours }} HRs</span>
    </div>

    <div class="container">
        <h4 class="text-dark fw-bold mb-4">Grades Statistics</h4>
        <div class="grades row justify-content-left mb-4">
            <div class="col-6 col-md-2 mb-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h6 class="text-dark fw-bold">Quiz 1</h6>
                        <div class="position-relative d-inline-block" style="width: 150px; height: 100px;">
                            <canvas id="Quiz1Chart"></canvas>
                            <div class="position-absolute top-50 start-50 translate-middle text-center">
                                <span class="text-dark">{{ $displayScores['quiz1'] }}</span>
                                <span class="fw-bold">/{{ $displayMaxScores['quiz1'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-2 mb-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h6 class="text-dark fw-bold">Midterm</h6>
                        <div class="position-relative d-inline-block" style="width: 150px; height: 100px;">
                            <canvas id="MidtermChart"></canvas>
                            <div class="position-absolute top-50 start-50 translate-middle text-center">
                                <span class="text-dark">{{ $displayScores['midterm'] }}</span>
                                <span class="fw-bold">/{{ $displayMaxScores['midterm'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-2 mb-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h6 class="text-dark fw-bold">Quiz 2</h6>
                        <div class="position-relative d-inline-block" style="width: 150px; height: 100px;">
                            <canvas id="Quiz2Chart"></canvas>
                            <div class="position-absolute top-50 start-50 translate-middle text-center">
                                <span class="text-dark">{{ $displayScores['quiz2'] }}</span>
                                <span class="fw-bold">/{{ $displayMaxScores['quiz2'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-2 mb-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h6 class="text-dark fw-bold">Project</h6>
                        <div class="position-relative d-inline-block" style="width: 150px; height: 100px;">
                            <canvas id="ProjectChart"></canvas>
                            <div class="position-absolute top-50 start-50 translate-middle text-center">
                                <span class="text-dark">{{ $displayScores['project'] }}</span>
                                <span class="fw-bold">/{{ $displayMaxScores['project'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-2 mb-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h6 class="text-dark fw-bold">Assignments</h6>
                        <div class="position-relative d-inline-block" style="width: 150px; height: 100px;">
                            <canvas id="AssignmentsChart"></canvas>
                            <div class="position-absolute top-50 start-50 translate-middle text-center">
                                <span class="text-dark">{{ $displayScores['assignments'] }}</span>
                                <span class="fw-bold">/{{ $displayMaxScores['assignments'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-2 mb-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h6 class="text-dark fw-bold">Final</h6>
                        <div class="position-relative d-inline-block" style="width: 150px; height: 100px;">
                            <canvas id="FinalChart"></canvas>
                            <div class="position-absolute top-50 start-50 translate-middle text-center">
                                <span class="text-dark">{{ $displayScores['final'] }}</span>
                                <span class="fw-bold">/{{ $displayMaxScores['final'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    

        <div class="row mb-5">
            <div class="col-md-6">
                <div class="card bg-white border-0 shadow">
                    <div class="card-body">
                        <h5 class="text-dark fw-bold mb-4">Final Grade</h5>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="col-md-6 text-center">
                                <div class="position-relative d-inline-block" style="width: 180px; height: 130px;">
                                    <canvas id="totalScoreChart"></canvas>
                                    <div class="position-absolute top-50 start-50 translate-middle text-center">
                                        <span class="fs-4 text-dark fw-bold d-block">{{ $percentage }}%</span>
                                        <span class="fs-6 text-dark">{{ $totalScore }}/{{ $totalMaxScore }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-center text-dark d-flex flex-column justify-content-center align-items-center">
                                    @if (!empty($grade->grade))
                                        <h5>Grade</h5>
                                        @php
                                            $gradeStatus = '';
                                            if($grade->total >= 122.4)
                                                $gradeStatus = 'success';
                                            elseif($grade->total <= 102)
                                                $gradeStatus = 'danger';
                                            else
                                                $gradeStatus = 'warning';
                                        @endphp
                                        <div class="btn btn-{{ $gradeStatus }} w-fit pe-3 ps-3" style="font-size: 40px;">
                                            {{ $grade->grade }}
                                        </div>
                                        <p class="mt-3">
                                            Status: <strong>{{ ucfirst($grade->status) }}</strong>
                                            @if ($grade->status === 'pass')
                                                <i class="fa-solid fa-circle-check text-success"></i>
                                            @else
                                                <i class="fa-solid fa-circle-xmark text-danger"></i>
                                            @endif
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card bg-white border-0 shadow" style="height: 240px;">
                    <div class="card-body">
                        <h5 class="text-dark fw-bold mb-4">Average Grades</h5>
                        <div class="d-flex justify-content-center gap-5 align-items-center">
                            <div class="text-center">
                                <h6 class="text-dark mb-2">Your Grade</h6>
                                <span class="fs-2 text-dark fw-bold">{{ $grade->total }}</span>
                            </div>
                            <div class="text-center">
                                <h6 class="text-dark mb-2">Department Average</h6>
                                <span class="fs-2 text-dark fw-bold">{{ $averageGrade }}</span>
                            </div>
                            <div class="text-center">
                                <h6 class="text-dark mb-2">Department Students</h6>
                                <span class="fs-2 text-dark fw-bold">
                                    {{ $departmentStudents }}
                                    <i class="fa-solid fa-users"></i>
                                </span>
                            </div>
                        </div>
                        <div class="mt-4">
                            <p class="text-muted mb-1">Your grade compared to class average:</p>
                            @if($grade->grade > $averageGrade)
                                <p class="text-success mb-0">
                                    Above average by {{ number_format($grade->total - $averageGrade, 1) }} points
                                    <i class="fa-solid fa-circle-up text-success"></i>
                                </p>
                            @elseif($grade->grade < $averageGrade) 
                                <p class="text-danger mb-0">
                                    Below average by {{ number_format($averageGrade - $grade->total, 1) }} points
                                    <i class="fa-solid fa-circle-down text-danger"></i>
                                </p>
                            @else
                                <p class="text-dark mb-0">
                                    At class average
                                    <i class="fa-solid fa-thumbs-up"></i>
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
    

    <!-- Assignment Statistics -->
    <div class="container mb-4">
        <h4 class="text-dark fw-bold mb-4">Assignment Statistics</h4>
        <div class="row pb-4">
            <div class="col-md-6">
                <div class="table-container shadow">
                    <table class="table table-striped ">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center bg-dark text-white">Assignment Title</th>
                                <th class="text-center bg-dark text-white">Status</th>
                                <th class="text-center bg-dark text-white">Submitted Date</th>
                                <th class="text-center bg-dark text-white">Due Date</th>
                                <th class="text-center bg-dark text-white">Score</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($submissions->isNotEmpty())
                                @foreach ($submissions as $submission)
                                <tr>
                                    <td class="fw-bold text-center">{{ $submission->assignment->title }}</td>
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
                            @else
                                <tr>
                                    <td colspan="5" class="text-center text-dark status-pending">No assignments found.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 rounded-4 shadow text-center" style="height: 210px;">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <h5 class="card-title text-dark fw-bold pb-3">Assignment Completion</h5>
                        <div class="position-relative d-inline-block" style="width: 170; height: 120px;">
                            <canvas id="completionChart"></canvas>
                            <div class="position-absolute top-50 start-50 translate-middle text-center">
                                <span class="fs-5 text-dark fw-bold d-block">{{ $completionRate }}%</span>
                                <span class="fs-6 text-dark">{{ $completedAssignments }}/{{ $totalAssignments }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow rounded-4 text-center" style="height: 210px;">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <h5 class="card-title text-dark fw-bold pb-3">Assignment Score Rate</h5>
                        <div class="position-relative d-inline-block" style="width: 170; height: 120px;">
                            <canvas id="scoreRateChart"></canvas>
                            <div class="position-absolute top-50 start-50 translate-middle text-center">
                                <span class="fs-5 text-dark fw-bold d-block">{{ $scoreRate }}%</span>
                                <span class="fs-6 text-dark">{{ $totalAssignmentsScore }}/{{ $maxPossibleScore }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <h4 class="text-dark fw-bold mb-4">Attendance Statistics</h4>
            <ul class="list-unstyled">
                <li class="text-dark">collapse table</li>
                <li class="text-dark">bar chart (lectures and labs)</li>
                <li class="text-dark">average attendance</li>
                <li class="text-dark">attendance rate</li>
                <li class="text-dark">classes attended</li>
                <li class="text-dark">classes missed</li>
            </ul>
        </div>
    </div>

    

    <!-- Include Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Total Score Chart
            const totalScoreCtx = document.getElementById('totalScoreChart').getContext('2d');
            const totalScore = {{ $totalScore }};
            const maxScore = {{ $totalMaxScore }};
            const scorePercentage = (totalScore / maxScore) * 100;

            let progressColor;
            if (scorePercentage >= 75) {
                progressColor = '#28a745'; // Green for >= 75%
            } else if (scorePercentage <= 50) {
                progressColor = '#dc3545'; // Red for <= 50%
            } else {
                progressColor = '#007bff'; // Blue for 50% to 75%
            }

            new Chart(totalScoreCtx, {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: [scorePercentage, 100 - scorePercentage],
                        backgroundColor: [progressColor, '#e9ecef'],
                        borderWidth: 0,
                        circumference: 360,
                        cutout: '80%',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            enabled: false
                        }
                    }
                }
            });

            // Quiz 1 Chart
            const quiz1Ctx = document.getElementById('Quiz1Chart').getContext('2d');
            const quiz1Score = {{ $displayScores['quiz1'] }};
            const quiz1MaxScore = {{ $displayMaxScores['quiz1'] }};
            const quiz1Percentage = (quiz1Score / quiz1MaxScore) * 100;
            let quiz1ProgressColor;
            if (quiz1Percentage >= 75) {
                quiz1ProgressColor = '#28a745'; // Green for >= 75%
            } else if (quiz1Percentage <= 50) {
                quiz1ProgressColor = '#dc3545'; // Red for <= 50%
            } else {
                quiz1ProgressColor = '#007bff'; // Blue for 50% to 75%
            }

            new Chart(quiz1Ctx, {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: [quiz1Percentage, 100 - quiz1Percentage],
                        backgroundColor: [quiz1ProgressColor, '#e9ecef'],
                        borderWidth: 0,
                        circumference: 360,
                        cutout: '85%',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            enabled: false
                        }
                    }
                }
            });

            // Midterm Chart
            const midtermCtx = document.getElementById('MidtermChart').getContext('2d');
            const midtermScore = {{ $displayScores['midterm'] }};
            const midtermMaxScore = {{ $displayMaxScores['midterm'] }};
            const midtermPercentage = (midtermScore / midtermMaxScore) * 100;
            let midtermProgressColor;
            if (midtermPercentage >= 75) {
                midtermProgressColor = '#28a745';
            } else if (midtermPercentage <= 50) {
                midtermProgressColor = '#dc3545';
            } else {
                midtermProgressColor = '#007bff';
            }

            new Chart(midtermCtx, {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: [midtermPercentage, 100 - midtermPercentage],
                        backgroundColor: [midtermProgressColor, '#e9ecef'],
                        borderWidth: 0,
                        circumference: 360,
                        cutout: '85%',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            enabled: false
                        }
                    }
                }
            });

            // Quiz 2 Chart
            const quiz2Ctx = document.getElementById('Quiz2Chart').getContext('2d');
            const quiz2Score = {{ $displayScores['quiz2'] }};
            const quiz2MaxScore = {{ $displayMaxScores['quiz2'] }};
            const quiz2Percentage = (quiz2Score / quiz2MaxScore) * 100;
            let quiz2ProgressColor;
            if (quiz2Percentage >= 75) {
                quiz2ProgressColor = '#28a745';
            } else if (quiz2Percentage <= 50) {
                quiz2ProgressColor = '#dc3545';
            } else {
                quiz2ProgressColor = '#007bff';
            }

            new Chart(quiz2Ctx, {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: [quiz2Percentage, 100 - quiz2Percentage],
                        backgroundColor: [quiz2ProgressColor, '#e9ecef'],
                        borderWidth: 0,
                        circumference: 360,
                        cutout: '85%',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            enabled: false
                        }
                    }
                }
            });

            // Project Chart
            const projectCtx = document.getElementById('ProjectChart').getContext('2d');
            const projectScore = {{ $displayScores['project'] }};
            const projectMaxScore = {{ $displayMaxScores['project'] }};
            const projectPercentage = (projectScore / projectMaxScore) * 100;
            let projectProgressColor;
            if (projectPercentage >= 75) {
                projectProgressColor = '#28a745';
            } else if (projectPercentage <= 50) {
                projectProgressColor = '#dc3545';
            } else {
                projectProgressColor = '#007bff';
            }

            new Chart(projectCtx, {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: [projectPercentage, 100 - projectPercentage],
                        backgroundColor: [projectProgressColor, '#e9ecef'],
                        borderWidth: 0,
                        circumference: 360,
                        cutout: '85%',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            enabled: false
                        }
                    }
                }
            });

            // Assignments Chart
            const assignmentsCtx = document.getElementById('AssignmentsChart').getContext('2d');
            const assignmentsScore = {{ $displayScores['assignments'] }};
            const assignmentsMaxScore = {{ $displayMaxScores['assignments'] }};
            const assignmentsPercentage = (assignmentsScore / assignmentsMaxScore) * 100;
            let assignmentsProgressColor;
            if (assignmentsPercentage >= 75) {
                assignmentsProgressColor = '#28a745';
            } else if (assignmentsPercentage <= 50) {
                assignmentsProgressColor = '#dc3545';
            } else {
                assignmentsProgressColor = '#007bff';
            }

            new Chart(assignmentsCtx, {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: [assignmentsPercentage, 100 - assignmentsPercentage],
                        backgroundColor: [assignmentsProgressColor, '#e9ecef'],
                        borderWidth: 0,
                        circumference: 360,
                        cutout: '85%',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            enabled: false
                        }
                    }
                }
            });

            // Final Chart
            const finalCtx = document.getElementById('FinalChart').getContext('2d');
            const finalScore = {{ $displayScores['final'] }};
            const finalMaxScore = {{ $displayMaxScores['final'] }};
            const finalPercentage = (finalScore / finalMaxScore) * 100;
            let finalProgressColor;
            if (finalPercentage >= 75) {
                finalProgressColor = '#28a745';
            } else if (finalPercentage <= 50) {
                finalProgressColor = '#dc3545';
            } else {
                finalProgressColor = '#007bff';
            }

            new Chart(finalCtx, {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: [finalPercentage, 100 - finalPercentage],
                        backgroundColor: [finalProgressColor, '#e9ecef'],
                        borderWidth: 0,
                        circumference: 360,
                        cutout: '85%',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            enabled: false
                        }
                    }
                }
            });

            // Assignment Completion Chart
            const completionCtx = document.getElementById('completionChart').getContext('2d');
            const completionPercentage = {{ $completionRate }};
            let completionColor;
            if (completionPercentage >= 75) {
                completionColor = '#28a745';
            } else if (completionPercentage <= 50) {
                completionColor = '#dc3545';
            } else {
                completionColor = '#007bff';
            }

            new Chart(completionCtx, {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: [completionPercentage, 100 - completionPercentage],
                        backgroundColor: [completionColor, '#e9ecef'],
                        borderWidth: 0,
                        circumference: 360,
                        cutout: '85%',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            enabled: false
                        }
                    }
                }
            });

            // Assignment Score Rate Chart
            const scoreRateCtx = document.getElementById('scoreRateChart').getContext('2d');
            const scoreRatePercentage = {{ $scoreRate }};
            let scoreRateColor;
            if (scoreRatePercentage >= 75) {
                scoreRateColor = '#28a745';
            } else if (scoreRatePercentage <= 50) {
                scoreRateColor = '#dc3545';
            } else {
                scoreRateColor = '#007bff';
            }

            new Chart(scoreRateCtx, {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: [scoreRatePercentage, 100 - scoreRatePercentage],
                        backgroundColor: [scoreRateColor, '#e9ecef'],
                        borderWidth: 0,
                        circumference: 360,
                        cutout: '85%',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            enabled: false
                        }
                    }
                }
            });
        });
    </script>

    <style>
    .card {
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .card-body {
        padding: 1rem;
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
