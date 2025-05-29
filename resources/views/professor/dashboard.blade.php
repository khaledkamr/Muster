@extends('layouts.professor')

@section('title', 'Dashboard')

@section('content')
    <div class="container">
        <h2 class="text-dark fw-bold pt-3 pb-4">{{ $course->name }} / Dashboard</h2>

        <div class="row">
            <div class="col-md-3">
                <div class="bg-white border-0 rounded-4 p-3 shadow d-flex justify-content-between align-items-center">
                    <div class="text-dark">
                        <h3 class="fw-bold">{{ $students->count() }}</h3>
                        <h4 class="text-muted">Total Students</h4>
                        <small class="d-flex align-items-center">
                            <span class="{{ $percentageDiff >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ number_format($percentageDiff, 1) }}%
                            </span>
                            <span class="text-muted ps-1">
                                Than Last Year
                                <i class="fa-solid fa-circle-up text-success"></i>
                            </span>
                        </small>
                    </div>
                    <div class="text-dark">
                        <i class="fa-solid fa-users fs-1 pe-2"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="bg-white border-0 rounded-4 p-3 shadow">
                    <div class="d-flex align-items-center">
                        <div class="position-relative" style="width: 100px; height: 100px;">
                            <canvas id="submissionChart"></canvas>
                            <div class="position-absolute top-50 start-50 translate-middle text-center">
                                <span class="fw-bold text-dark">{{ number_format($submissionRate, 1) }}%</span>
                            </div>
                        </div>
                        <div class="text-dark ms-3">
                            <h5 class="mb-3 fw-bold">Submission Rate</h5>
                            <h6 class="text-muted mb-0">{{ $totalAssignments }} assignments</h6>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="bg-white border-0 rounded-4 p-3 shadow d-flex justify-content-between align-items-center">
                    <div>
                        <div class="d-flex align-items-center">
                            <div class="position-relative" style="width: 100px; height: 100px;">
                                <canvas id="attendanceChart"></canvas>
                                <div class="position-absolute top-50 start-50 translate-middle text-center">
                                    <span class="fw-bold text-dark">{{ number_format($attendanceRate, 1) }}%</span>
                                </div>
                            </div>
                            <div class="text-dark ms-3">
                                <h5 class="mb-3 fw-bold">Attendance Rate</h5>
                                <h6 class="text-muted mb-0">{{ $totalSessions }} Sessions</h6>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-center align-items-center">
                        <div style="height: 100px; width: 120px;">
                            <canvas id="attendancePieChart"></canvas>
                        </div>
                        <div class="text-dark ms-3">
                            <h5 class="mb-3 fw-bold">Attendance Distribution</h5>
                            <small>
                                <span class="badge text-dark" style="background-color: #79f596;">Present</span>
                                <span class="badge text-dark ms-1" style="background-color: #ff808a;">Absent</span>
                                <span class="badge text-dark ms-1" style="background-color: #ffcc00;">Late</span>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="bg-white border-0 rounded-4 p-3 shadow">
                    <h5 class="text-dark fw-bold mb-1">Weekly Attendance Trend</h5>
                    <div style="height: 295px;">
                        <canvas id="weeklyAttendanceChart"></canvas>
                    </div>
                    <a href="{{ route('professor.course.attendance', $courseId) }}" class="btn btn-outline-primary w-100 mt-2">View All Attendance</a>
                </div>
            </div>
            <div class="col-md-3">
                <div class="bg-white border-0 rounded-4 p-3 shadow">
                    <h6 class="text-dark text-center fw-bold pb-3 mb-3 border-bottom">Top 5 In Academic Progress</h6>
                    <div class="top-students">
                        @php
                            $i = 1;
                        @endphp
                        @foreach ($top5Students->take(5) as $student)
                            <div class="d-flex align-items-center mb-2">
                                <a href="{{ route('professor.student.profile', [$student->id, $courseId]) }}">
                                    <img src="{{ $student->profile_image ?? asset('imgs/user.png') }}"
                                        class="rounded-circle me-2" alt="{{ $student->name }}"
                                        style="width: 40px; height: 40px; object-fit: cover;">
                                </a>
                                <div class="d-flex flex-column">
                                    <a href="{{ route('professor.student.profile', [$student->id, $courseId]) }}" class="text-dark text-decoration-none">
                                        {{ $student->name }}
                                        @if($i == 1)
                                            <i class="fa-solid fa-crown text-warning ps-1"></i>
                                            @php
                                                $i++;
                                            @endphp
                                        @endif
                                    </a>
                                    <small class="text-muted">ID: {{ $student->id }}</small>
                                </div>
                                <div class="ms-auto">
                                    <span class="badge bg-primary">
                                        <i class="fa-solid fa-chart-line"></i> {{ $student->grades->where('course_id', $course->id)->first()->total }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <a href="{{ route('professor.course.grades', $courseId) }}" class="btn btn-outline-primary w-100 mt-3">More Details</a>
                </div>
            </div>
            <div class="col-md-3">
                <div class="bg-white border-0 rounded-4 p-3 shadow">
                    <h6 class="text-dark text-center fw-bold pb-3 mb-0 pb-0">Performance Status</h6>
                    <div>
                        <canvas id="performanceBarChart" style="height: 281px;"></canvas>
                    </div>
                    <a href="{{ route('professor.course.students', $courseId) }}" class="btn btn-outline-primary w-100 mt-3">View All Students</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Attendance Rate Chart
            const attendanceChartCtx = document.getElementById('attendanceChart').getContext('2d');
            const attendanceRate = {{ $attendanceRate }};

            let attendanceProgressColor;
            if (attendanceRate >= 75) {
                attendanceProgressColor = '#28a745';
            } else if (attendanceRate <= 50) {
                attendanceProgressColor = '#dc3545';
            } else {
                attendanceProgressColor = '#007bff';
            }

            new Chart(attendanceChartCtx, {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: [attendanceRate, 100 - attendanceRate],
                        backgroundColor: [attendanceProgressColor, '#e9ecef'],
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

            // Submission Rate Chart
            const submissionChartCtx = document.getElementById('submissionChart').getContext('2d');
            const submissionRate = {{ $submissionRate }};

            let submissionProgressColor;
            if (submissionRate >= 75) {
                submissionProgressColor = '#28a745'; 
            } else if (submissionRate <= 50) {
                submissionProgressColor = '#dc3545';
            } else {
                submissionProgressColor = '#007bff';
            }

            new Chart(submissionChartCtx, {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: [submissionRate, 100 - submissionRate],
                        backgroundColor: [submissionProgressColor, '#e9ecef'],
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

            // Weekly Attendance Chart
            const weeklyAttendanceChartCtx = document.getElementById('weeklyAttendanceChart').getContext('2d');
            const weeklyData = @json($weeklyAttendance);

            const labels = Object.keys(weeklyData).map(week => week.replace('week', 'Week '));
            const presentData = Object.values(weeklyData).map(data => data.present);
            const absentData = Object.values(weeklyData).map(data => data.absent);
            const lateData = Object.values(weeklyData).map(data => data.late);

            new Chart(weeklyAttendanceChartCtx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Late',
                            data: lateData,
                            borderColor: '#ffcc00',
                            backgroundColor: 'rgba(255, 204, 0, 0.2)',
                            tension: 0,
                            fill: true
                        },
                        {
                            label: 'Absent',
                            data: absentData,
                            borderColor: '#ff808a',
                            backgroundColor: 'rgba(255, 128, 138, 0.2)',
                            tension: 0,
                            fill: true
                        },
                        {
                            label: 'Present',
                            data: presentData,
                            borderColor: '#79f596',
                            backgroundColor: 'rgba(121, 245, 150, 0.2)',
                            tension: 0,
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
                                usePointStyle: true,
                                padding: 20
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: false,
                                text: 'Number of Students',
                                font: {
                                    size: 12
                                }
                            },
                            ticks: {
                                stepSize: 5
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });

            const attendancePieChartCtx = document.getElementById('attendancePieChart').getContext('2d');
            const presentCount = {{ $presentCount }};
            const absentCount = {{ $absentCount }};
            const lateCount = {{ $lateCount }};

            const attendancePieChart = new Chart(attendancePieChartCtx, {
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
                            display: false,
                            position: 'left',
                            labels: {
                                color: '#333'
                            }
                        },
                        title: {
                            display: false,
                            text: 'Performance Status Distribution',
                            color: '#333',
                            font: {
                                size: 16,
                                weight: 'bold'
                            }
                        }
                    }
                }
            });

            const performanceBarChartCtx = document.getElementById('performanceBarChart').getContext('2d');
            const performanceBarChart = new Chart(performanceBarChartCtx, {
                type: 'bar',
                data: {
                    labels: ['High', 'Average', 'Low'],
                    datasets: [{
                        label: 'Performance',
                        data: [45, 55, 25],
                        backgroundColor: ['rgb(40, 167, 69, 0.8)', 'rgb(0, 123, 255, 0.8)', 'rgb(220, 53, 69, 0.8)'],
                        borderWidth: 1,
                        borderColor: '#eee',
                        borderRadius: 10,
                        barRadius: 10,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        title: {
                            display: false,
                            text: 'Performance Status',
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
                            ticks: {
                                stepSize: 5
                            },
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        });
    </script>
@endsection
