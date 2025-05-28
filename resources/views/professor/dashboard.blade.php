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
        </div>
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="bg-white border-0 rounded-4 p-3 shadow">
                    <h5 class="text-dark fw-bold mb-1">Weekly Attendance Trend</h5>
                    <div style="height: 300px;">
                        <canvas id="weeklyAttendanceChart"></canvas>
                    </div>
                    <a href="{{ route('professor.course.attendance', $courseId) }}" class="btn btn-outline-primary w-100 mt-2">View All Attendance</a>
                </div>
            </div>
            <div class="col-md-3">
                <div class="bg-white border-0 rounded-4 p-3 shadow">
                    <h6 class="text-dark text-center fw-bold pb-3 mb-3 border-bottom">Top 5 In Academic Progress</h6>
                    <div class="top-students">
                        @foreach ($top5Students->take(5) as $student)
                            <div class="d-flex align-items-center mb-2">
                                <a href="{{ route('professor.student.profile', [$student->id, $courseId]) }}">
                                    <img src="{{ $student->profile_image ?? asset('imgs/user.png') }}"
                                        class="rounded-circle me-2" alt="{{ $student->name }}"
                                        style="width: 40px; height: 40px; object-fit: cover;">
                                </a>
                                <div class="d-flex flex-column">
                                    <a href="{{ route('professor.student.profile', [$student->id, $courseId]) }}"
                                        class="text-dark text-decoration-none">{{ $student->name }}</a>
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
                    <a href="{{ route('professor.course.students', $courseId) }}"
                        class="btn btn-outline-primary w-100 mt-3">View All Students</a>
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

            let progressColor;
            if (attendanceRate >= 75) {
                progressColor = '#28a745'; // Green for >= 75%
            } else if (attendanceRate <= 50) {
                progressColor = '#dc3545'; // Red for <= 50%
            } else {
                progressColor = '#007bff'; // Blue for 50% to 75%
            }

            new Chart(attendanceChartCtx, {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: [attendanceRate, 100 - attendanceRate],
                        backgroundColor: [progressColor, '#e9ecef'],
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

            new Chart(submissionChartCtx, {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: [submissionRate, 100 - submissionRate],
                        backgroundColor: [progressColor, '#e9ecef'],
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
        });
    </script>
@endsection
