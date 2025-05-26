@extends('layouts.student')

@section('title', 'Courses')

@section('content')
    <h1 class="text-dark fw-bold pt-3 pb-3">Your Courses</h1>

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
                            <a href="{{ route('student.course-details', $course->id) }}" class="btn btn-primary">
                                View Full Details <i class="fa-solid fa-arrow-up-right-from-square"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

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
