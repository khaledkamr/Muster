@extends('layouts.parent')

@section('title', 'Courses')

@section('content')
<div class="container">
    <h1 class="text-dark fw-bold pt-3 pb-3">{{ $child->name }}'s Courses</h1>
    <div class="d-flex mb-1">
        <h6 class="badge bg-primary fs-6 ">
            <i class="fa-solid fa-clock-rotate-left"></i>
            total credit hours: {{ $totalCreditHours }} HRs 
        </h6>
    </div>
    <div class="accordion" id="coursesAccordion">
        @foreach ($enrollments as $enrollment)
            @php
                $course = $enrollment->course;
                $stats = $courseStats[$course->id];
            @endphp
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
                <div id="collapse{{ $course->id }}" class="accordion-collapse collapse"
                    aria-labelledby="heading{{ $course->id }}" data-bs-parent="#coursesAccordion">
                    <div class="accordion-body bg-white shadow-sm rounded-3 mt-2">
                        <!-- Course Description -->
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-dark fw-bold">Course Description</h6>
                                <p class="text-muted">{{ $course->description }}</p>
                            </div>
                            <div>
                                <span class="badge bg-{{ $course->type === 'compulsory' ? 'success' : 'info' }}">
                                    {{ $course->type }}
                                </span>
                                <span
                                    class="badge bg-{{ $course->difficulty === 'easy' ? 'success' : ($course->difficulty === 'medium' ? 'warning' : 'danger') }}">
                                    {{ $course->difficulty }}
                                </span>
                            </div>
                        </div>
                        <div class="mb-4">
                            <strong>By</strong>
                            <a href="{{ route('student.professor-profile', $course->professor_id) }}"
                                class="text-dark text-decoration-none">
                                {{ $course->professor->name }}
                                <i class="fa-solid fa-user-tie"></i>
                            </a>
                        </div>
                        <!-- Statistics Section -->
                        <div class="row">
                            <!-- Assignment Statistics -->
                            <div class="col-md-4 mb-4">
                                <div class="border-0 rounded-4 p-3 shadow h-100">
                                    <div class="rounded">
                                        <h6 class="card-title text-dark fw-bold pb-2">Assignment Progress</h6>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="text-muted">Completion Rate</span>
                                            <span class="text-dark fw-bold">{{ $stats['completion_rate'] }}%</span>
                                        </div>
                                        <div class="progress mb-3" style="height: 8px;">
                                            <div class="progress-bar bg-{{ $stats['completion_rate'] >= 75 ? 'success' : ($stats['completion_rate'] <= 50 ? 'danger' : 'primary') }}"
                                                role="progressbar" style="width: {{ $stats['completion_rate'] }}%"></div>
                                        </div>
                                        <p class="text-muted mb-0">
                                            {{ $stats['completed_assignments'] }} of {{ $stats['total_assignments'] }}
                                            assignments completed
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Attendance Statistics -->
                            <div class="col-md-4 mb-4">
                                <div class="border-0 rounded-4 p-3 shadow h-100">
                                    <div class="rounded">
                                        <h6 class="card-title text-dark fw-bold pb-2">Attendance Rate</h6>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="text-muted">Attendance</span>
                                            <span class="text-dark fw-bold">{{ $stats['attendance_rate'] }}%</span>
                                        </div>
                                        <div class="progress mb-3" style="height: 8px;">
                                            <div class="progress-bar bg-{{ $stats['attendance_rate'] >= 75 ? 'success' : ($stats['attendance_rate'] <= 50 ? 'danger' : 'primary') }}"
                                                role="progressbar" style="width: {{ $stats['attendance_rate'] }}%"></div>
                                        </div>
                                        <p class="text-muted mb-0">
                                            {{ $stats['present_sessions'] }} of {{ $stats['total_sessions'] }} sessions
                                            attended
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Course Progress -->
                            <div class="col-md-4 mb-4">
                                <div class="border-0 rounded-4 p-3 shadow h-100">
                                    <div class="rounded">
                                        <h6 class="card-title text-dark fw-bold pb-2">Course Progress</h6>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="text-muted">Completed Sessions</span>
                                            <span class="text-dark fw-bold">{{ $stats['course_progress'] }}%</span>
                                        </div>
                                        <div class="progress mb-3" style="height: 8px;">
                                            <div class="progress-bar bg-primary" role="progressbar"
                                                style="width: {{ $stats['course_progress'] }}%"></div>
                                        </div>
                                        <p class="text-muted mb-0">
                                            {{ $stats['remaining_sessions'] }} sessions remaining
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- View Details Button -->
                        <div class="text-end">
                            <a href="{{ route('parent.child.course-details', [$course->id, $child->id]) }}" class="btn btn-primary">
                                View Full Details <i class="fa-solid fa-arrow-up-right-from-square"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Course Total Grades Chart -->
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-body">
            <h4 class="text-dark fw-bold mb-4">Course Total Grades Overview</h4>
            <div class="chart-container" style="position: relative; height:400px;">
                <canvas id="courseTotalGradesChart"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Warning Alert for Lowest Grade Course -->
    @php
        $lowestGradeIndex = array_search(min($chartData['grades']), $chartData['grades']);
        $lowestGradeCourse = $chartData['labels'][$lowestGradeIndex];
        $lowestGrade = $chartData['grades'][$lowestGradeIndex];
    @endphp
    <div class="row">
        <div class="col-md-6">
            <div class="alert alert-warning mt-4 shadow-sm" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle me-3"></i>
                    <div>
                        <h5 class="alert-heading mb-1">Attention Needed</h5>
                        <p class="mb-0">
                            Your child's performance in <strong>{{ $lowestGradeCourse }}</strong> needs attention. With a current grade of {{ $lowestGrade }}, we recommend encouraging them to dedicate more study time to this course and consider seeking additional academic support if needed.
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="alert alert-info mt-4 shadow-sm" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fas fa-circle-info me-3"></i>
                    <div>
                        <h5 class="alert-heading mb-1">GPA Prediction</h5>
                        <small class="text-muted d-block">Based on current performance</small>
                        <div class="d-flex justify-content-between align-items-center mt-0">
                            <div>
                                <p class="mb-1">
                                    <strong>Predicted Semester GPA:</strong>
                                    <span class=" ms-2"> {{ $childPredictedGPA }} </span>
                                </p>
                                <p class="mb-0">
                                    <strong>Predicted CGPA:</strong>
                                    <span class=" ms-2"> {{ $childPredictedCGPA }} </span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('courseTotalGradesChart').getContext('2d');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($chartData['labels']),
                datasets: [{
                    label: 'Total Grades',
                    data: @json($chartData['grades']),
                    backgroundColor: ['rgba(54, 162, 235, 0.8)', 'rgba(75, 192, 192, 0.8)', 'rgba(153, 102, 255, 0.8)', 'rgba(255, 159, 64, 0.8)', 'rgba(255, 99, 132, 0.8)'],
                    borderColor: ['rgba(54, 162, 235, 0.8)', 'rgba(75, 192, 192, 0.8)', 'rgba(153, 102, 255, 0.8)', 'rgba(255, 159, 64, 0.8)', 'rgba(255, 99, 132, 0.8)'],
                    borderWidth: 1,
                    borderRadius: 5,
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
                                return `Total: ${context.raw}`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: Math.max(...@json($chartData['grades'])) + 10,
                        title: {
                            display: true,
                            text: 'Total Grades So Far'
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
    .accordion-button:not(.collapsed) {
        background-color: #fff;
        color: #000;
    }

    .accordion-button:focus {
        box-shadow: none;
        border-color: rgba(0, 0, 0, .125);
    }

    .accordion-button::after {
        margin-left: 1rem;
    }

    .progress {
        background-color: #e9ecef;
        border-radius: 4px;
    }

    .progress-bar {
        border-radius: 4px;
    }
</style>
@endsection
