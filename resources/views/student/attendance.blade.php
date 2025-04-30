@extends('layouts.student')

@section('title', 'Attendance')

@section('content')
    <h1 class=" pt-3 text-dark fw-bold">Your Attendance</h1>

    <div class="d-flex flex-row-reverse justify-content-around align-items-center mb-4">
        <!-- Attendance Rate -->
        <div class="text-center">
            <h5 class="text-dark mt-2">Attendance Rate</h5>
            <div class="position-relative d-inline-block" style="width: 100px; height: 100px;">
                <canvas id="attendanceRateChart"></canvas>
                <div class="position-absolute top-50 start-50 translate-middle text-center">
                    <span class="fs-5 text-dark fw-bold">{{ $attendanceRate }}%</span>
                </div>
            </div>
        </div>

        <!-- Filter for Lectures/Labs -->
        <div class="align-self-end">
            <label for="typeFilter" class="form-label text-dark fw-bold">Filter by Session Type:</label>
            <select id="typeFilter" name="type" class="form-select" onchange="this.form.submit()" form="filterForm" style="max-width: 300px;">
                <option value="both" {{ $filterType === 'both' ? 'selected' : '' }}>Both</option>
                <option value="lecture" {{ $filterType === 'lecture' ? 'selected' : '' }}>Lectures</option>
                <option value="lab" {{ $filterType === 'lab' ? 'selected' : '' }}>Labs</option>
            </select>
        </div>
    </div>

    <!-- Weekly Attendance Graph -->
    <div class="mb-5">
        <canvas id="weeklyAttendanceChart" height="100"></canvas>
    </div>

    <!-- Course Selection for Calendar -->
    <div class="mb-4">
        <label for="courseFilter" class="form-label text-dark fw-bold">Select Course:</label>
        <select id="courseFilter" name="course_id" class="form-select" onchange="this.form.submit()" form="filterForm" style="max-width: 300px;">
            <option value="" {{ !$selectedCourse ? 'selected' : '' }}>Select a course</option>
            @foreach ($currentSemesterCourses as $course)
                <option value="{{ $course->id }}" {{ $selectedCourse && $selectedCourse->id === $course->id ? 'selected' : '' }}>
                    {{ $course->code }}: {{ $course->name }}
                </option>
            @endforeach
        </select>
    </div>

    <!-- Calendar for Selected Course -->
    @if ($selectedCourse)
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title text-dark">{{ $selectedCourse->code }}: {{ $selectedCourse->name }}</h5>
                    <div>
                        <a href="{{ route('student.attendance', ['course_id' => $selectedCourse->id, 'month' => $canGoPrev ? $calendarMonth->copy()->subMonth()->format('Y-m') : $calendarMonth]) }}" class="btn btn-sm btn-secondary {{ !$canGoPrev ? 'disabled' : '' }}">Previous</a>
                        <span class="text-dark mx-2">{{ $calendarMonth->format('F Y') }}</span>
                        <a href="{{ route('student.attendance', ['course_id' => $selectedCourse->id, 'month' => $canGoNext ? $calendarMonth->copy()->addMonth()->format('Y-m') : $calendarMonth]) }}" class="btn btn-sm btn-secondary {{ !$canGoNext ? 'disabled' : '' }}">Next</a>
                    </div>
                </div>
                <div class="calendar">
                    <div class="d-flex flex-wrap">
                        @foreach (['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $day)
                            <div class="calendar-day-header text-center text-dark fw-bold" style="width: 14.28%;">{{ $day }}</div>
                        @endforeach
                        @for ($i = 1; $i < $calendarMonth->copy()->startOfMonth()->dayOfWeek; $i++)
                            <div class="calendar-day" style="width: 14.28%;"></div>
                        @endfor
                        @foreach ($calendarMonth->copy()->startOfMonth()->daysUntil($calendarMonth->copy()->endOfMonth()) as $day)
                            @php
                                $attendance = $calendarAttendances->firstWhere('date', $day->format('Y-m-d'));
                            @endphp
                            <div class="calendar-day text-center {{ $attendance ? ($attendance->status === 'present' ? 'bg-success text-white' : 'bg-danger text-white') : '' }}" style="width: 14.28%; padding: 10px; border: 1px solid #dee2e6;">
                                {{ $day->day }}
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Hidden Form for Filters -->
    <form id="filterForm" method="GET" action="{{ route('student.attendance') }}">
        @if ($selectedCourse)
            <input type="hidden" name="month" value="{{ $calendarMonth->format('Y-m') }}">
        @endif
    </form>

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
                type: 'line', // Other options include 'line', 'pie', 'doughnut', 'radar', 'polarArea', 'bubble', 'scatter'
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Number of Attended Sessions',
                        data: data,
                        borderColor: '#007bff',
                        backgroundColor: 'rgba(0, 123, 255, 0.2)',
                        fill: true,
                        tension: 0.4,
                    }]
                },
                options: {
                    responsive: true,
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
        .calendar-day-header {
            padding: 10px;
            border: 1px solid #dee2e6;
            background-color: #f1f3f5;
        }
        .calendar-day {
            padding: 10px;
            border: 1px solid #dee2e6;
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
    </style>
@endsection