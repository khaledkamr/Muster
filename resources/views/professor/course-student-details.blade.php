@extends('layouts.professor')

@section('title', 'Course Student Details')

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
    <h4 class="text-dark fw-bold mb-0">{{ $user->name }}'s academic details in {{ $course->name }}</h4>
    <span class="text-dark ms-3">{{ $course->credit_hours }} HRs</span>
</div>

<div class="container">
    <h4 class="text-dark fw-bold mb-4">Grades Statistics</h4>
    <div class="grades row justify-content-left mb-4">
        <div class="col-6 col-md-2 mb-3">
            <div class="bg-white text-center rounded-4 shadow p-3">
                <div class="rounded">
                    <h6 class="text-dark fw-bold">Quiz 1</h6>
                    <div class="position-relative d-inline-block" style="width: 150px; height: 100px;">
                        <canvas id="Quiz1Chart"></canvas>
                        <div class="position-absolute top-50 start-50 translate-middle text-center">
                            <span class="text-dark">{{ $displayScores['quiz1'] }}</span>
                            <span class="text-dark fw-bold">/{{ $displayMaxScores['quiz1'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-2 mb-3">
            <div class="bg-white text-center rounded-4 shadow p-3">
                <div class="rounded">   
                    <h6 class="text-dark fw-bold">Midterm</h6>
                    <div class="position-relative d-inline-block" style="width: 150px; height: 100px;">
                        <canvas id="MidtermChart"></canvas>
                        <div class="position-absolute top-50 start-50 translate-middle text-center">
                            <span class="text-dark">{{ $displayScores['midterm'] }}</span>
                            <span class="text-dark fw-bold">/{{ $displayMaxScores['midterm'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-2 mb-3">
            <div class="bg-white text-center rounded-4 shadow p-3">
                <div class="rounded">
                    <h6 class="text-dark fw-bold">Quiz 2</h6>
                    <div class="position-relative d-inline-block" style="width: 150px; height: 100px;">
                        <canvas id="Quiz2Chart"></canvas>
                        <div class="position-absolute top-50 start-50 translate-middle text-center">
                            <span class="text-dark">{{ $displayScores['quiz2'] }}</span>
                            <span class="text-dark fw-bold">/{{ $displayMaxScores['quiz2'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-2 mb-3">
            <div class="bg-white text-center rounded-4 shadow p-3">
                <div class="rounded">
                    <h6 class="text-dark fw-bold">Project</h6>
                    <div class="position-relative d-inline-block" style="width: 150px; height: 100px;">
                        <canvas id="ProjectChart"></canvas>
                        <div class="position-absolute top-50 start-50 translate-middle text-center">
                            <span class="text-dark">{{ $displayScores['project'] }}</span>
                            <span class="text-dark fw-bold">/{{ $displayMaxScores['project'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-2 mb-3">
            <div class="bg-white text-center rounded-4 shadow p-3">
                <div class="rounded">
                    <h6 class="text-dark fw-bold">Assignments</h6>
                    <div class="position-relative d-inline-block" style="width: 150px; height: 100px;">
                        <canvas id="AssignmentsChart"></canvas>
                        <div class="position-absolute top-50 start-50 translate-middle text-center">
                            <span class="text-dark">{{ $displayScores['assignments'] }}</span>
                            <span class="text-dark fw-bold">/{{ $displayMaxScores['assignments'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-2 mb-3">
            <div class="bg-white text-center rounded-4 shadow p-3">
                <div class="rounded">
                    <h6 class="text-dark fw-bold">Final</h6>
                    <div class="position-relative d-inline-block" style="width: 150px; height: 100px;">
                        <canvas id="FinalChart"></canvas>
                        <div class="position-absolute top-50 start-50 translate-middle text-center">
                            <span class="text-dark">{{ $displayScores['final'] }}</span>
                            <span class="text-dark fw-bold">/{{ $displayMaxScores['final'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-5">
        <div class="col-md-6">
            <div class="bg-white border-0 shadow rounded-4 p-3">
                <div class="rounded">
                    <h5 class="text-dark fw-bold mb-4">{{ $grade->grade ? 'Final Grade' : 'Predicted Grade' }}</h5>
                    @if($grade->grade)
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
                                <div
                                    class="text-center text-dark d-flex flex-column justify-content-center align-items-center">
                                    @if (!empty($grade->grade))
                                        <h5>Grade</h5>
                                        @php
                                            $gradeStatus = '';
                                            if ($grade->total >= 122.4) {
                                                $gradeStatus = 'success';
                                            } elseif ($grade->total <= 102) {
                                                $gradeStatus = 'danger';
                                            } else {
                                                $gradeStatus = 'warning';
                                            }
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
                    @else
                        <div class="d-flex justify-content-center gap-4 align-items-center">
                            <div class="text-center">
                                <h6 class="text-dark mb-2">Total Grade So Far</h6>
                                <div class="position-relative d-inline-block" style="width: 150px; height: 130px;">
                                    <canvas id="totalScoreChart"></canvas>
                                    <div class="position-absolute top-50 start-50 translate-middle text-center">
                                        <span class="fs-4 text-dark fw-bold d-block">{{ $percentage }}%</span>
                                        <span class="fs-6 text-dark">{{ $totalScore }}/{{ $totalMaxScore }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="text-center">
                                <h6 class="text-dark mb-2">Status</h6>
                                <div class="d-flex flex-column justify-content-center align-items-center alert alert-primary" style="width: 200px; height: 120px;">
                                    @if((70 / 100) * 100 >= 75)
                                        <span class="fs-2 text-success fw-bold">Excellent</span>
                                        <p class="text-success mb-0"><i class="fa-solid fa-star"></i> On Track</p>
                                    @elseif((70 / 100) * 100 >= 60)
                                        <span class="fs-2 text-primary fw-bold">Good</span>
                                        <p class="text-primary mb-0"><i class="fa-solid fa-circle-check"></i> Passing</p>
                                    @else
                                        <span class="fs-2 text-danger fw-bold">At Risk</span>
                                        <p class="text-danger mb-0"><i class="fa-solid fa-triangle-exclamation"></i> Needs Improvement</p>
                                    @endif
                                </div>
                            </div>
                            <div class="text-center">
                                <h6 class="text-dark mb-2">Predicted Grade</h6>
                                @php
                                    $predictedPercentage = (70 / 100) * 100;
                                    $predictedGrade = 'N/A';
                                    if ($predictedPercentage >= 90) $predictedGrade = 'A';
                                    elseif ($predictedPercentage >= 80) $predictedGrade = 'B';
                                    elseif ($predictedPercentage >= 70) $predictedGrade = 'C';
                                    elseif ($predictedPercentage >= 60) $predictedGrade = 'D';
                                    else $predictedGrade = 'F';
                                @endphp
                                <div class="d-flex flex-column justify-content-center align-items-center" style=" height: 130px;">
                                    <span class="fs-2 text-{{ $predictedGrade === 'F' ? 'danger' : 'dark' }} fw-bold">{{ $predictedGrade }}</span>
                                    <p class="text-muted mb-0">Based on current progress</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="bg-white border-0 shadow rounded-4 p-3" style="height: 240px;">
                <div class="rounded">
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
                    <div class="mt-2">
                        @if ($grade->total > $averageGrade)
                            <div class="alert alert-success" role="alert">
                                <i class="fa-solid fa-circle-up me-2"></i>
                                <strong>Excellent Performance!</strong> Student is {{ number_format($grade->total - $averageGrade, 1) }} points above average.
                                <small>Consider giving them additional challenging material or having them mentor other students.</small>
                            </div>
                        @elseif($grade->total < $averageGrade)
                            <div class="alert alert-danger" role="alert">
                                <i class="fa-solid fa-circle-down me-2"></i>
                                <strong>Needs Support.</strong> Student is {{ number_format($averageGrade - $grade->total, 1) }} points below average.
                                <small>Consider scheduling office hours or recommending tutoring services.</small>
                            </div>
                        @else
                            <div class="alert alert-info" role="alert">
                                <i class="fa-solid fa-thumbs-up me-2"></i>
                                <strong>Average Performance.</strong> Student is at the class average.
                                <small>Monitor progress and provide opportunities for improvement.</small>
                            </div>
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
                                    <td class="text-center">
                                        {{ $submission->submitted_at ? $submission->submitted_at->format('M d, Y') : '-' }}
                                    </td>
                                    <td class="text-center">{{ $submission->assignment->due_date->format('M d, Y') }}
                                    </td>
                                    <td class="text-light text-center ">
                                        <div class="badge bg-primary" style="font-size: 12px;">
                                            {{ $submission->score ?? '-' }}</div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="5" class="text-center text-dark status-pending">No assignments found.
                                </td>
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

<!-- Attendance Statistics -->
<div class="container">
    <h4 class="text-dark fw-bold mb-4">Attendance Statistics</h4>
    {{-- attendance table --}}
    <div class="row">
        <div class="col-md-12">
            <div class="table-container shadow">
                <table class="table">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center bg-dark text-white">#</th>
                            @for ($i = 1; $i < 17; $i++)
                                <th class="text-center bg-dark text-white">{{ $i }}th</th>
                            @endfor
                        </tr>
                    </thead>
                    <tbody>
                        @foreach (['lecture', 'lab'] as $type)
                            <tr>
                                <td class="text-center bg-dark text-white">{{ ucfirst($type) }}</td>
                                @php
                                    $filteredAttendance = $attendances->where('type', $type);
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

    </div>
        {{-- bar chart (lectures and labs) --}}
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card border-0 shadow rounded-4">
                    <div class="card-body">
                        <h5 class="card-title text-dark fw-bold mb-4">Lecture vs Lab Attendance</h5>
                        <canvas id="attendanceComparisonChart" height="300px"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="row">
                    <div class="col-md-3">
                        <div class="border-0 rounded-4 shadow text-center bg-white p-3">
                            <div class="d-flex flex-column justify-content-center">
                                <h6 class="card-title text-dark fw-bold pb-2">Missed Lectures</h6>
                                <div class="d-flex justify-content-center align-items-center">
                                    <span class="fs-1 text-{{ $missedLectures > 0 ? 'danger' : 'success' }} fw-bold d-block">{{ $missedLectures }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border-0 shadow rounded-4 text-center bg-white p-3">
                            <div class="d-flex flex-column justify-content-center">
                                <h6 class="card-title text-dark fw-bold pb-2">Missed Labs</h6>
                                <div class="d-flex justify-content-center align-items-center">
                                    <span class="fs-1 text-{{ $missedLabs > 0 ? 'danger' : 'success' }} fw-bold d-block">{{ $missedLabs }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border-0 rounded-4 shadow text-center bg-white p-3">
                            <div class="d-flex flex-column justify-content-center">
                                <h6 class="card-title text-dark fw-bold pb-2">Late Lectures</h6>
                                <div class="d-flex justify-content-center align-items-center">
                                    <span class="fs-1 text-{{ $lateLectures > 0 ? 'danger' : 'success' }} fw-bold d-block">{{ $lateLectures }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border-0 shadow rounded-4 text-center bg-white p-3">
                            <div class="d-flex flex-column justify-content-center">
                                <h6 class="card-title text-dark fw-bold pb-2">Late Labs</h6>
                                <div class="d-flex justify-content-center align-items-center">
                                    <span class="fs-1 text-{{ $lateLabs > 0 ? 'danger' : 'success' }} fw-bold d-block">{{ $lateLabs }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-4">
                    {{-- average attendance --}}
                    <div class="col-md-8">
                        <div class="bg-white border-0 shadow rounded-4 p-3" style="height: 285px;">
                            <div class="rounded">
                                <h5 class="card-title text-dark fw-bold pb-2">Average Attendance</h5>
                                <div class="d-flex justify-content-between align-items-center mt-4">
                                    <div class="text-center">
                                        <h6 class="text-dark mb-2">Your Attendance</h6>
                                        <span class="fs-2 text-dark fw-bold">{{ $attendanceRate }}%</span>
                                    </div>
                                    <div class="text-center">
                                        <h6 class="text-dark mb-2">Department Average</h6>
                                        <span
                                            class="fs-2 text-dark fw-bold">{{ $departmentAverageAttendance }}%</span>
                                    </div>
                                    <div class="text-center">
                                        <h6 class="text-dark mb-2">Department Students</h6>
                                        <span class="fs-2 text-dark fw-bold">
                                            {{ $departmentStudents }} <i class="fa-solid fa-users"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    @if ($attendanceRate > $departmentAverageAttendance)
                                        <div class="alert alert-success" role="alert">
                                            <i class="fa-solid fa-circle-up me-2"></i>
                                            <strong>Excellent Attendance!</strong> Student is {{ number_format($attendanceRate - $departmentAverageAttendance, 1) }}% above the department average.
                                            <small>Consider recognizing their commitment and using them as an example for other students.</small>
                                        </div>
                                    @elseif($attendanceRate < $departmentAverageAttendance)
                                        <div class="alert alert-danger" role="alert">
                                            <i class="fa-solid fa-circle-down me-2"></i>
                                            <strong>Attendance Concern.</strong> Student is {{ number_format($departmentAverageAttendance - $attendanceRate, 1) }}% below the department average.
                                            <small>Consider scheduling a meeting to discuss attendance issues and support options.</small>
                                        </div>
                                    @else
                                        <div class="alert alert-info" role="alert">
                                            <i class="fa-solid fa-thumbs-up me-2"></i>
                                            <strong>Average Attendance.</strong> Student matches the department average.
                                            <small>Monitor for any changes and encourage continued consistency.</small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- attendance rate --}}
                    <div class="col-md-4">
                        <div class="bg-white border-0 shadow rounded-4 p-3" style="height: 285px;">
                            <div class="rounded d-flex flex-column justify-content-center align-items-center">
                                <h5 class="text-dark fw-bold text-center mb-4">Attendance Rate</h5>
                                <div class="position-relative d-inline-block" style="width: 170px; height: 170px;">
                                    <canvas id="attendanceRateChart"></canvas>
                                    <div class="position-absolute top-50 start-50 translate-middle text-center">
                                        <span class="fs-4 text-dark fw-bold d-block">{{ $attendanceRate }}%</span>
                                        <span class="fs-6 text-dark">{{ $totalPresent }}/{{ $totalSessions }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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

        new Chart(totalScoreCtx, {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [scorePercentage, 100 - scorePercentage],
                    backgroundColor: ['#007bff', '#e9ecef'],
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
            quiz1ProgressColor = '#28a745'; 
        } else if (quiz1Percentage <= 50) {
            quiz1ProgressColor = '#dc3545'; 
        } else {
            quiz1ProgressColor = '#007bff';
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

        // Attendance Comparison Chart
        const attendanceComparisonCtx = document.getElementById('attendanceComparisonChart').getContext('2d');
        const lectureAttendance =
            {{ $attendances->where('type', 'lecture')->where('status', 'present')->count() }};
        const labAttendance = {{ $attendances->where('type', 'lab')->where('status', 'present')->count() }};

        new Chart(attendanceComparisonCtx, {
            type: 'bar',
            data: {
                labels: ['Lecture', 'Lab'],
                datasets: [{
                    label: 'Attended Sessions',
                    data: [lectureAttendance, labAttendance],
                    backgroundColor: [
                        'rgba(40, 167, 69, 0.8)', // Green for lectures
                        'rgba(0, 123, 255, 0.8)' // Blue for labs
                    ],
                    borderColor: [
                        'rgb(40, 167, 69)',
                        'rgb(0, 123, 255)'
                    ],
                    borderWidth: 1,
                    barThickness: 90,
                    barRadius: 5,
                    borderRadius: 10
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
                        },
                        max: Math.max(lectureAttendance, labAttendance) + 2
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

        // Attendance Rate Chart
        const attendanceRateCtx = document.getElementById('attendanceRateChart').getContext('2d');
        const attendanceRate = {{ $attendanceRate }};
        let attendanceRateColor;
        if (attendanceRate >= 75) {
            attendanceRateColor = '#28a745';
        } else if (attendanceRate <= 50) {
            attendanceRateColor = '#dc3545';
        } else {
            attendanceRateColor = '#007bff';
        }

        new Chart(attendanceRateCtx, {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [attendanceRate, 100 - attendanceRate],
                    backgroundColor: [attendanceRateColor, '#e9ecef'],
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

    .attendance-cell {
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .attendance-cell:hover {
        background-color: #f1f3f5;
    }
</style>
@endsection
