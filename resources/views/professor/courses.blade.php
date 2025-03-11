@extends('layouts.professor')

@section('title', 'Courses')

@section('content')
    <h1 class="pb-5 pt-3">Courses You Teach</h1>
    <div class="accordion" id="coursesAccordion">
        @php
            $courses = $user->courses()->with('enrollments')->get();
            $semesters = $courses->groupBy(function ($course) {
                $year = $course->enrollments->min('enrolled_at') ? $course->enrollments->min('enrolled_at')->format('Y') : 'Unknown';
                return "$year - {$course->semester}";
            });
        @endphp

        @forelse ($semesters as $semester => $semesterCourses)
            @php
                [$year, $semesterKey] = explode(' - ', $semester);
                $semesterLabel = ucfirst($semesterKey) . ' Semester';
            @endphp
            <div class="card" style="background-color: #495057; border: 1px solid #6c757d; margin-bottom: 10px;">
                <div class="card-header" id="heading{{ md5($semester) }}"
                     data-toggle="collapse" data-target="#collapse{{ md5($semester) }}"
                     aria-expanded="false" aria-controls="collapse{{ md5($semester) }}"
                     style="background-color: #6c757d; cursor: pointer;">
                    <h5 class="mb-0">
                        {{ $year }} - {{ $semesterLabel }} ({{ $semesterCourses->count() }} Courses)
                    </h5>
                </div>
                <div id="collapse{{ md5($semester) }}" class="collapse"
                     aria-labelledby="heading{{ md5($semester) }}" data-parent="#coursesAccordion">
                    <div class="card-body" style="background-color: #343a40;">
                        <ul class="list-group list-group-flush">
                            @foreach ($semesterCourses as $course)
                                <li class="list-group-item bg-transparent text-white">
                                    {{ $course->code }}: {{ $course->name }}
                                    ({{ $course->enrollments->count() }} Enrolled)
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-center">You are not assigned to teach any courses yet.</p>
        @endforelse
    </div>
@endsection