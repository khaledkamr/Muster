@extends('layouts.student')

@section('title', 'Grades')

@section('content')
    <h1 class="pb-5 pt-3 text-dark fw-bold">Your Grades</h1>
    <div class="accordion" id="semesterAccordion">
        @foreach ([1 => 'Year 1', 2 => 'Year 2', 3 => 'Year 3', 4 => 'Year 4'] as $yearNum => $yearLabel)
            @foreach (['first' => 'First Semester', 'second' => 'Second Semester'] as $semesterKey => $semesterLabel)
                @php
                    $semesterCourses = $enrollments
                        ->filter(function ($enrollment) use ($yearNum, $semesterKey, $startYear) {
                            $enrollmentYear = (int) $enrollment->enrolled_at->format('Y') - $startYear + 1;
                            return $enrollmentYear === $yearNum && $enrollment->course->semester === $semesterKey;
                        })
                        ->pluck('course');
                @endphp

                @if ($semesterCourses->isNotEmpty())
                    <div class="card" style="background-color: #495057; border: 1px solid #6c757d; margin-bottom: 10px;">
                        <div class="card-header" id="heading{{ $yearNum }}{{ $semesterKey }}"
                             data-bs-toggle="collapse" data-bs-target="#collapse{{ $yearNum }}{{ $semesterKey }}"
                             aria-expanded="false" aria-controls="collapse{{ $yearNum }}{{ $semesterKey }}"
                             style="background-color: #6c757d; cursor: pointer;">
                            <h5 class="mb-0">
                                {{ $yearLabel }} - {{ $semesterLabel }} ({{ $semesterCourses->count() }} Courses)
                            </h5>
                        </div>
                        <div id="collapse{{ $yearNum }}{{ $semesterKey }}" class="collapse"
                             aria-labelledby="heading{{ $yearNum }}{{ $semesterKey }}"
                             data-bs-parent="#semesterAccordion">
                            <div class="card-body" style="background-color: #343a40;">
                                <ul class="list-group list-group-flush">
                                    @foreach ($semesterCourses as $course)
                                        @php
                                            $grade = $course->grades->where('student_id', $user->id)->first();
                                        @endphp
                                        <li class="list-group-item bg-transparent text-white d-flex justify-content-between align-items-center">
                                            <span>
                                                {{ $course->code }}: {{ $course->name }}
                                                (Difficulty: {{ ucfirst($course->difficulty) }},
                                                Type: {{ ucfirst($course->type) }})
                                            </span>
                                            <div>
                                                <span class=" text-{{ $grade->status === 'pass' ? 'success' : 'danger' }} me-4 pe-4">
                                                    {{ $grade->grade ?? 'N/A' }}
                                                </span>
                                                <a href="{{ route('student.course-details', $course->id) }}" class="btn btn-sm btn-success ms-4">
                                                    Details
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