@extends('layouts.professor')

@section('title', 'Home')

@section('content')
<h2 class="text-dark fw-bold mb-4 mt-3">Welcome {{Auth::user()->name}}</h2>

<div class="row g-4">
    <!-- Course Overview Box -->
    <div class="col-md-4">
        <div class="card shadow-sm h-100 border-0">
            <div class="card-body">
                <h5 class="card-title text-dark fw-bold">Course Overview</h5>
                @if ($courses->isNotEmpty())
                    <ul class="list-group list-group-flush">
                        @foreach ($courses as $course)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>{{ $course->code }}: {{ $course->name }}</span>
                                <span class="badge bg-info text-dark">{{ $course->enrollments->count() }} Students</span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-muted">No courses assigned.</p>
                @endif
                <a href="{{ route('professor.courses') }}" class="btn btn-primary mt-2" style="background-color: #002361;">View All Courses</a>
            </div>
        </div>
    </div>

    <!-- Attendance Summary Box -->
    <div class="col-md-4">
        <div class="card shadow-sm h-100 border-0">
            <div class="card-body">
                <h5 class="card-title text-dark fw-bold">Attendance Summary</h5>
                @php
                    $totalSessions = $courses->sum(function ($course) {
                        return $course->attendance->count();
                    });
                    $presentCount = $courses->sum(function ($course) {
                        return $course->attendance->where('status', 'present')->count();
                    });
                    $attendanceRate = $totalSessions > 0 ? ($presentCount / $totalSessions) * 100 : 0;
                @endphp
                <p class="card-text">Total Sessions: {{ $totalSessions }}</p>
                <p class="card-text">Present: {{ $presentCount }} ({{ number_format($attendanceRate, 1) }}%)</p>
                <div class="progress mt-2" style="height: 10px;">
                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $attendanceRate }}%;" aria-valuenow="{{ $attendanceRate }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <a href="{{ route('professor.course.attendance', $courses[0]->id) }}" class="btn btn-primary mt-2" style="background-color: #002361;">View Attendance</a>
            </div>
        </div>
    </div>

    <!-- Assignment Status Box -->
    <div class="col-md-4">
        <div class="card shadow-sm h-100 border-0">
            <div class="card-body">
                <h5 class="card-title text-dark fw-bold">Assignment Status</h5>
                @php
                    $totalSubmissions = $courses->flatMap(function ($course) {
                        return $course->assignments->flatMap->submissions;
                    })->count();
                    $submittedCount = $courses->flatMap(function ($course) {
                        return $course->assignments->flatMap->submissions->where('status', 'submitted');
                    })->count();
                    $pendingCount = $totalSubmissions - $submittedCount;
                @endphp
                <p class="card-text">Submitted: {{ $submittedCount }}</p>
                <p class="card-text">Pending: {{ $pendingCount }}</p>
                <div class="progress mt-2" style="height: 10px;">
                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $totalSubmissions > 0 ? ($submittedCount / $totalSubmissions) * 100 : 0 }}%;" aria-valuenow="{{ $totalSubmissions > 0 ? ($submittedCount / $totalSubmissions) * 100 : 0 }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <a href="{{ route('professor.course.assignments', $courses[0]->id) }}" class="btn btn-primary mt-2" style="background-color: #002361;">View Assignments</a>
            </div>
        </div>
    </div>

    <!-- Student Performance Box -->
    <div class="col-md-4">
        <div class="card shadow-sm h-100 border-0">
            <div class="card-body">
                <h5 class="card-title text-dark fw-bold">Student Performance</h5>
                @php
                    $totalScores = $courses->flatMap(function ($course) {
                        return $course->assignments->flatMap->submissions->pluck('score');
                    })->filter()->avg() ?? 0;
                @endphp
                <p class="card-text">Average Score: {{ number_format($totalScores, 2) }}/100</p>
                <div class="progress mt-2" style="height: 10px;">
                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $totalScores }}%;" aria-valuenow="{{ $totalScores }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <a href="#" class="btn btn-primary mt-2 disabled" style="background-color: #002361;">View Details (Coming Soon)</a>
            </div>
        </div>
    </div>

    <!-- Upcoming Events Box -->
    <div class="col-md-4">
        <div class="card shadow-sm h-100 border-0">
            <div class="card-body">
                <h5 class="card-title text-dark fw-bold">Upcoming Events</h5>
                @if (!empty($upcomingEvents))
                    <ul class="list-group list-group-flush">
                        @foreach ($upcomingEvents as $event)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>{{ $event['title'] }}</span>
                                <span class="badge bg-info text-dark">{{ $event['date'] }}</span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-muted">No upcoming events.</p>
                @endif
                <a href="#" class="btn btn-primary mt-2" style="background-color: #002361;">View Calendar</a>
            </div>
        </div>
    </div>

    <!-- Notifications Box -->
    <div class="col-md-4">
        <div class="card shadow-sm h-100 border-0">
            <div class="card-body">
                <h5 class="card-title text-dark fw-bold">Notifications</h5>
                @if (!empty($notifications))
                    <ul class="list-group list-group-flush">
                        @foreach ($notifications as $notification)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>{{ $notification['message'] }}</span>
                                <span class="badge bg-warning text-dark">{{ $notification['time'] }}</span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-muted">No new notifications.</p>
                @endif
                <a href="#" class="btn btn-primary mt-2" style="background-color: #002361;">View All</a>
            </div>
        </div>
    </div>

    <!-- Quick Actions Box -->
    <div class="col-md-12">
        <div class="card shadow-sm h-100 border-0">
            <div class="card-body">
                <h5 class="card-title text-dark fw-bold">Quick Actions</h5>
                <div class="row g-3">
                    <div class="col-md-3">
                        <a href="{{ route('professor.course.attendance', $courses[0]->id) }}" class="btn btn-outline-primary w-100" style="border-color: #002361; color: #002361;">Check Attendance</a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('professor.course.assignments', $courses[0]->id) }}" class="btn btn-outline-primary w-100" style="border-color: #002361; color: #002361;">Grade Assignments</a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('professor.profile') }}" class="btn btn-outline-primary w-100" style="border-color: #002361; color: #002361;">Update Profile</a>
                    </div>
                    <div class="col-md-3">
                        <a href="#" class="btn btn-outline-primary w-100 disabled" style="border-color: #002361; color: #002361;">Send Announcement (Soon)</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection