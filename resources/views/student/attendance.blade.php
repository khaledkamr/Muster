@extends('layouts.student')

@section('title', 'Attendance')

@section('content')
<div class="container">
    <h1 class="pt-3 text-dark fw-bold mb-5">Your Attendance</h1>

    <!-- Weekly Attendance Graph -->
    <div class="row">
        <div class="col-md-10">
            <div class="bg-white p-3 rounded-4 shadow mb-5">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-center mb-1 ms-3">
                        <h5 class="text-dark fw-bold align-self-center">Weekly Attendance Trend <i class="fa-solid fa-chart-line"></i></h5>
                    </div>
                    <div class="me-3">
                        <select id="typeFilter" name="type" class="form-select" onchange="this.form.submit()" form="filterForm" style="width: 250px;">
                            <option value="both" {{ $filterType === 'both' ? 'selected' : '' }}>Both</option>
                            <option value="lecture" {{ $filterType === 'lecture' ? 'selected' : '' }}>Lectures</option>
                            <option value="lab" {{ $filterType === 'lab' ? 'selected' : '' }}>Labs</option>
                        </select>
                    </div>
                </div>
                <canvas id="weeklyAttendanceChart" height="100"></canvas>
                <!-- Hidden Form for Filters -->
                <form id="filterForm" method="GET" action="{{ route('student.attendance') }}"></form>
            </div>
        </div>
        <div class="col-md-2 d-flex flex-column gap-4">
            <div class="bg-white p-3 rounded-4 shadow text-center">
                <h5 class="text-dark mt-2 mb-2">Attendance Rate</h5>
                <div class="position-relative d-inline-block" style="width: 100px; height: 115px;">
                    <canvas id="attendanceRateChart"></canvas>
                    <div class="position-absolute top-50 start-50 translate-middle text-center">
                        <span class="fs-5 text-dark fw-bold">{{ $attendanceRate }}%</span>
                    </div>
                </div>
            </div>
            <div class="bg-white pt-3 p-2 rounded-4 shadow text-center">
                <h5 class="text-dark mt-2">Missing Sessions</h5>
                <div class="position-relative d-inline-block" style="width: 100px; height: 100px;">
                    <div class="position-absolute top-50 start-50 translate-middle text-center">
                        <span class="fs-1 fw-bold text-danger">{{ $missingSessions }}</span>
                        <span class="text-muted">
                            @if($filterType === 'both')
                                Sessions
                            @elseif($filterType === 'lecture')
                                Lectures
                            @elseif($filterType === 'lab')
                                Labs
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Courses Accordion -->
    <div class="accordion" id="coursesAccordion">
        @foreach ($coursesAttendance['course'] as $course)
            <div class="accordion-item border-0 rounded-4 shadow-sm mb-3">
                <h2 class="accordion-header" id="heading{{ $course->id }}">
                    <button class="accordion-button collapsed bg-white shadow-sm rounded-3" type="button"
                        data-bs-toggle="collapse" data-bs-target="#collapse{{ $course->id }}" aria-expanded="false"
                        aria-controls="collapse{{ $course->id }}">
                        <div class="d-flex justify-content-between align-items-center w-100 me-3">
                            <div>
                                <h4 class="mb-1 text-dark">{{ $course->code }}</h4>
                                <p class="mb-0 text-muted">{{ $course->name }}</p>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-primary me-2">{{ $course->credit_hours }} HRs</span>
                            </div>
                        </div>
                    </button>
                </h2>
                <div id="collapse{{ $course->id }}" class="accordion-collapse collapse" aria-labelledby="heading{{ $course->id }}" data-bs-parent="#coursesAccordion">
                    <div class="accordion-body bg-white shadow-sm rounded-3 mt-2">
                        <div class="row mb-4">
                            <div class="col-md-8">
                                <div class="table-container shadow">
                                    <table class="table">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="text-center bg-dark text-white">#</th>
                                                @for ($i = 1; $i < 11; $i++)
                                                    <th class="text-center bg-dark text-white">{{ $i }}th</th>
                                                @endfor
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach (['lecture', 'lab'] as $type)
                                                <tr>
                                                    <td class="text-center bg-dark text-white">{{ ucfirst($type) }}</td>
                                                    @php
                                                        $courseIndex = array_search($course->id, $coursesAttendance['courseId']);
                                                        $filteredAttendance = $coursesAttendance['attendance'][$courseIndex]->where('type', $type);
                                                    @endphp
                                                    @foreach ($filteredAttendance as $attendance)
                                                        <td class="attendance-cell text-center" style="cursor: pointer;"
                                                            title="{{ $attendance->date->format('M d, Y') }}">
                                                            @if ($attendance->status === 'present')
                                                                <i class="fa-solid fa-check text-success"></i>
                                                            @elseif($attendance->status === 'late')
                                                                <i class="fa-solid fa-clock text-warning"></i>
                                                            @else
                                                                <i class="fa-solid fa-xmark text-danger"></i>
                                                            @endif
                                                        </td>
                                                    @endforeach
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-0 shadow rounded-4"">
                                    <div class="card-body d-flex flex-column justify-content-center">
                                        <div class="row">
                                            <div class="col-md-6  text-center">
                                                <div class="position-relative d-inline-block" style="width: 180px; height: 115px;">
                                                    <canvas id="courseAttendanceChart{{ $course->id }}"></canvas>
                                                    <div class="position-absolute top-50 start-50 translate-middle text-center">
                                                        <span class="fs-4 text-dark fw-bold d-block">{{ $coursesAttendance['attendanceRate'][$courseIndex] }}%</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 text-left d-flex align-items-center">
                                                <h5 class="card-title text-dark fw-bold mb-0">Course Attendance Rate</h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    const courseAttendanceCtx = document.getElementById('courseAttendanceChart{{ $course->id }}').getContext('2d');
                                    const courseAttendanceRate = {{ $coursesAttendance['attendanceRate'][$courseIndex] }};
                                    let courseAttendanceColor;
                                    
                                    if (courseAttendanceRate >= 75) {
                                        courseAttendanceColor = '#28a745';
                                    } else if (courseAttendanceRate <= 50) {
                                        courseAttendanceColor = '#dc3545';
                                    } else {
                                        courseAttendanceColor = '#007bff';
                                    }

                                    new Chart(courseAttendanceCtx, {
                                        type: 'doughnut',
                                        data: {
                                            datasets: [{
                                                data: [courseAttendanceRate, 100 - courseAttendanceRate],
                                                backgroundColor: [courseAttendanceColor, '#e9ecef'],
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
                        </div>
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('student.course-details', $course->id) }}" class="btn btn-primary">View More Details</a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Courses attendanceRate Bar Chart -->
    <div class="bg-white p-3 rounded-4 shadow mb-5">
        <h5 class="text-dark fw-bold align-self-center">Attendance Rate of All Courses</h5>
        <div class="chart-container" style="position: relative; height:400px;">
            <canvas id="courseAttendanceRateChart"></canvas>
        </div>
    </div>

    <!-- Alert for Lowest Attendance Rate Course -->
    @php
        $lowestAttendanceIndex = array_search(min($coursesAttendance['attendanceRate']), $coursesAttendance['attendanceRate']);
        $lowestAttendanceCourse = $coursesAttendance['course'][$lowestAttendanceIndex];
        $lowestAttendance = $coursesAttendance['attendanceRate'][$lowestAttendanceIndex];
    @endphp
    <div class="alert alert-warning mt-4 shadow-sm" role="alert">
        <div class="d-flex align-items-center">
            <i class="fas fa-exclamation-triangle me-3"></i>
            <div>
                <h5 class="alert-heading mb-1">Attention Needed</h5>
                <p class="mb-0">
                    Your attendance rate of {{ $lowestAttendance }}% in <strong>{{ $lowestAttendanceCourse->name }}</strong> is concerning. Low attendance can significantly impact your academic performance and final grade. Please make attending this course a priority and contact your professor or academic advisor if you're having difficulties.
                </p>
            </div>
        </div>
    </div>
</div>

    <!-- Include Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Attendance Rate Chart
            const attendanceRateCtx = document.getElementById('attendanceRateChart').getContext('2d');
            const attendanceRate = {{ $attendanceRate }};
            let rateColor = attendanceRate >= 75 ? '#28a745' : (attendanceRate <= 50 ? '#dc3545' : '#007bff');

            new Chart(attendanceRateCtx, {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: [attendanceRate, 100 - attendanceRate],
                        backgroundColor: [rateColor, '#e9ecef'],
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

            // Weekly Attendance Graph
            const weeklyAttendanceCtx = document.getElementById('weeklyAttendanceChart').getContext('2d');
            const weeklyData = @json($weeklyAttendance);
            const labels = Object.keys(weeklyData).map(week => `Week ${week}`);
            const data = Object.values(weeklyData);

            new Chart(weeklyAttendanceCtx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Attended Session',
                        data: data,
                        borderColor: '#007bff',
                        backgroundColor: 'rgba(0, 123, 255, 0.2)',
                        fill: true,
                        tension: 0.4,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    
                    plugins: {
                        legend: { display: false },
                    },
                    scales: {
                        x: {
                            title: {
                                display: false,
                                text: 'Week Number'
                            },
                            max: 17,
                            ticks: {
                                maxRotation: 45,
                                minRotation: 45
                            },
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Number of Sessions'
                            },
                            beginAtZero: true,
                            max: {{ $filterType === 'both' ? $currentSemesterCourses->count() * 2 : $currentSemesterCourses->count() }},
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });

            const courseAttendanceRateChartCtx = document.getElementById('courseAttendanceRateChart').getContext('2d');
            const courses = @json(collect($coursesAttendance['course'])->pluck('code'));
            new Chart(courseAttendanceRateChartCtx, {
                type: 'bar',
                data: {
                    labels: courses,
                    datasets: [{
                        label: 'Attendance Rate',
                        data: @json($coursesAttendance['attendanceRate']),
                        backgroundColor: ['rgba(54, 162, 235, 0.8)', 'rgba(75, 192, 192, 0.8)', 'rgba(153, 102, 255, 0.8)', 'rgba(255, 159, 64, 0.8)', 'rgba(255, 99, 132, 0.8)'],
                        borderColor: ['rgba(54, 162, 235, 0.8)', 'rgba(75, 192, 192, 0.8)', 'rgba(153, 102, 255, 0.8)', 'rgba(255, 159, 64, 0.8)', 'rgba(255, 99, 132, 0.8)'],
                        borderWidth: 1,
                        borderRadius: 20,
                        barThickness: 100,
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
                            callbacks: {
                                label: function(context) {
                                    return `Attendance Rate: ${context.raw}%`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: Math.max(...@json($coursesAttendance['attendanceRate'])) + 10,
                            title: {
                                display: true,
                                text: 'Attendance Rate'
                            },
                            ticks: {
                                stepSize: 2
                            }
                        },
                        x: {
                            title: {
                                display: false,
                                text: 'Course'
                            },
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        });
    </script>

    <style>
        .content {
            background-color: #f8f9fa;
        }
        .card {
            background-color: #ffffff;
            border: none;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .contribution-day {
            border-radius: 3px;
            cursor: pointer;
            transition: 0.3s;
        }
        .contribution-day:hover {
            opacity: 0.8;
        }
        
        .form-select {
            background-color: #ffffff;
            border: 1px solid #ced4da;
            color: #495057;
        }
        .form-select:focus {
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

    </style>
@endsection