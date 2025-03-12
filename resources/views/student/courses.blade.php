@extends('layouts.student')

@section('title', 'Courses')

@section('content')
    <h1 class="pb-5 pt-3">Your Courses</h1>
    <div class="accordion" id="semesterAccordion">
        @php
            $currentYear = 2025;
            $yearOffset = match ($user->year) {
                'freshman' => 0,
                'sophomore' => 1,
                'junior' => 2,
                'senior' => 3,
            };
            $startYear = $currentYear - $yearOffset;
        @endphp

        @foreach ([1 => 'Year 1', 2 => 'Year 2', 3 => 'Year 3', 4 => 'Year 4'] as $yearNum => $yearLabel)
            @foreach (['first' => 'First Semester', 'second' => 'Second Semester'] as $semesterKey => $semesterLabel)
                @php
                    $semesterCourses = $user->enrollments
                        ->filter(function ($enrollment) use ($yearNum, $semesterKey, $startYear) {
                            $enrollmentYear = (int) $enrollment->enrolled_at->format('Y') - $startYear + 1;
                            return $enrollmentYear === $yearNum && $enrollment->course->semester === $semesterKey;
                        })
                        ->pluck('course');
                @endphp

                @if ($semesterCourses->isNotEmpty())
                    <div class="card" style="background-color: #495057; border: 1px solid #6c757d; margin-bottom: 10px;">
                        <div class="card-header" id="heading{{ $yearNum }}{{ $semesterKey }}"
                             data-toggle="collapse" data-target="#collapse{{ $yearNum }}{{ $semesterKey }}"
                             aria-expanded="false" aria-controls="collapse{{ $yearNum }}{{ $semesterKey }}"
                             style="background-color: #6c757d; cursor: pointer;">
                            <h5 class="mb-0">
                                {{ $yearLabel }} - {{ $semesterLabel }} ({{ $semesterCourses->count() }} Courses)
                            </h5>
                        </div>
                        <div id="collapse{{ $yearNum }}{{ $semesterKey }}" class="collapse"
                             aria-labelledby="heading{{ $yearNum }}{{ $semesterKey }}"
                             data-parent="#semesterAccordion">
                            <div class="card-body" style="background-color: #343a40;">
                                <ul class="list-group list-group-flush">
                                    @foreach ($semesterCourses as $course)
                                        <li class="list-group-item bg-transparent text-white d-flex justify-content-between align-items-center">
                                            <span>
                                                {{ $course->code }}: {{ $course->name }}
                                                (Difficulty: {{ ucfirst($course->difficulty) }},
                                                Type: {{ ucfirst($course->type) }})
                                            </span>
                                            <div>
                                                <a href="{{ route('student.course-details', $course->id) }}" class="btn btn-sm btn-success mr-2">
                                                    Details
                                                </a>
                                                <a href="{{ route('student.course-attendance', $course->id) }}" class="btn btn-sm btn-info">
                                                    Attendance
                                                </a>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        @endforeach
    </div>
@endsection