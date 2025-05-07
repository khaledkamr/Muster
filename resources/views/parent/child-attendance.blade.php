@extends('layouts.parent')

@section('title', 'Attendance')

@section('content')
<div class="container">
    <h1 class=" pt-3 text-dark fw-bold">{{ $child->name }}'s Attendance</h1>

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

    <!-- Course Selection for Contribution Graph -->
    <div class="mb-4">
        <label for="courseFilter" class="form-label text-dark fw-bold">Select Course:</label>
        <select id="courseFilter" name="course_id" class="form-select" onchange="this.form.submit()" form="filterForm" style="max-width: 300px;">
            <option value="" {{ !$selectedCourse ? 'selected' : '' }}>All Courses</option>
            @foreach ($currentSemesterCourses as $course)
                <option value="{{ $course->id }}" {{ $selectedCourse && $selectedCourse->id === $course->id ? 'selected' : '' }}>
                    {{ $course->code }}: {{ $course->name }}
                </option>
            @endforeach
        </select>
    </div>

    <!-- Contribution Graph for Selected Course -->
    {{-- @if ($selectedCourse) --}}
        <div class="card shadow-sm mb-4" style="max-width: 500px;">
            <div class="card-body">
                {{-- <h5 class="card-title text-dark">{{ $selectedCourse->code }}: {{ $selectedCourse->name }}</h5> --}}
                <div class="contribution-graph">
                    <!-- Month Labels -->
                    <div class="d-flex">
                        @php
                            $currentMonth = $semesterStart->copy();
                            $monthsInSemester = [];
                            while ($currentMonth <= $semesterEnd) {
                                $monthLabel = $currentMonth->format('M');
                                $monthDays = $currentMonth->copy()->startOfMonth()->diffInDays($currentMonth->copy()->endOfMonth()) + 1;
                                $monthWidth = $monthDays * 3.2; 
                                $monthsInSemester[$monthLabel] = $monthWidth;
                                $currentMonth->addMonth();
                            }
                        @endphp
                        <div style="width: 65px;"></div>
                        @foreach ($monthsInSemester as $month => $width)
                            <div class="calendar-day-header" style="min-width: {{ $width }}px;">{{ $month }}</div>
                        @endforeach
                    </div>
                    <!-- Days of the Week -->
                    <div class="d-flex">
                        <div class="d-flex flex-column me-2" style="height: 110px; margin-top: -4px;">
                            <div class="text-dark align-self-end mb-1" style="height: 14px;">sat</div>
                            <div class="text-dark align-self-end mb-1" style="height: 14px;">sun</div>
                            <div class="text-dark align-self-end mb-1" style="height: 14px;">mon</div>
                            <div class="text-dark align-self-end mb-1" style="height: 14px;">tue</div>
                            <div class="text-dark align-self-end mb-1" style="height: 14px;">wed</div>
                            <div class="text-dark align-self-end mb-1" style="height: 14px;">thu</div>
                            <div class="text-dark align-self-end mb-1" style="height: 14px;">fri</div>
                        </div>
                        <div class="d-flex flex-column flex-wrap" style="height: 130px;">
                            @php
                                $currentDate = $semesterStart->copy();
                                $firstDayOfSemester = $semesterStart->copy()->startOfWeek();
                                $offset = $firstDayOfSemester->diffInDays($semesterStart, false);
                                $x = 0;
                            @endphp
                            @while ($currentDate <= $semesterEnd)
                                @php
                                    $dateStr = $currentDate->format('Y-m-d');
                                    $attended = isset($contributionData[$dateStr]) && $contributionData[$dateStr] == 1;
                                    $dayClass = $attended ? 'bg-success' : 'bg-secondary';
                                @endphp

                                @if($currentDate->copy()->format('D') != 'Sat' && $x == 0)
                                    @php
                                        $dayOfWeek = $currentDate->copy()->dayOfWeek; // 0 (Sun) to 6 (Sat)
                                    @endphp
                                    @for ($i = 0; $i < $dayOfWeek + 1; $i++)
                                        <div class="contribution-day bg-body" style="width: 14px; height: 14px; margin: 2px;"></div>
                                    @endfor
                                @endif
                                @php
                                    $x++;
                                @endphp

                                <div class="contribution-day {{ $dayClass }}" style="width: 14px; height: 14px; margin: 2px;" title="{{ $attended ? 'attended on' : 'absent on' }} {{ $currentDate->format('M d, D') }}"></div>
                                
                                @if($currentDate->copy()->endOfMonth()->format('d-m-y') == $currentDate->format('d-m-y'))
                                    @for ($i = 0; $i < 7; $i++)
                                        <div class="contribution-day bg-body" style="width: 14px; height: 14px; margin: 2px;"></div>
                                    @endfor
                                @endif
                                @php
                                    $currentDate->addDay();
                                @endphp
                            @endwhile
                        </div>
                    </div>
                    <!-- Legend -->
                    <div class="d-flex align-items-center mt-2 justify-content-end">
                        <div class="contribution-day bg-secondary me-1" style="width: 14px; height: 14px;"></div>
                        <span class="text-dark me-2">absent</span>
                        <div class="contribution-day bg-success" style="width: 14px; height: 14px;"></div>
                        <span class="text-dark ms-1">attended</span>
                    </div>
                </div>
            </div>
        </div>
    {{-- @endif --}}

    <!-- Hidden Form for Filters -->
    <form id="filterForm" method="GET" action="{{ route('parent.child.attendance', $childId) }}"></form>
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
    </style>
@endsection