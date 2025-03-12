@extends('layouts.student')

@section('title', 'Attendance - ' . $course->name)

@section('content')
    <h1 class="pb-5 pt-3">{{ $course->code }}: {{ $course->name }} - Attendance</h1>

    <div class="card" style="background-color: #495057; border: 1px solid #6c757d; margin-bottom: 20px;">
        <div class="card-header" style="background-color: #6c757d;">
            <h5 class="mb-0">Attendance Details</h5>
        </div>
        <div class="card-body" style="background-color: #343a40;">
            <!-- Filter Buttons -->
            <div class="mb-3">
                <a href="{{ route('student.course-attendance', $course->id) }}?type=all" class="btn btn-sm {{ request('type', 'all') === 'all' ? 'btn-primary' : 'btn-secondary' }} mr-2">All</a>
                <a href="{{ route('student.course-attendance', $course->id) }}?type=lecture" class="btn btn-sm {{ request('type') === 'lecture' ? 'btn-primary' : 'btn-secondary' }} mr-2">Lectures</a>
                <a href="{{ route('student.course-attendance', $course->id) }}?type=lab" class="btn btn-sm {{ request('type') === 'lab' ? 'btn-primary' : 'btn-secondary' }}">Labs</a>
            </div>

            <!-- Attendance Table -->
            <table class="table table-dark table-bordered">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $filteredAttendances = $attendances;
                        if (request('type') === 'lecture') {
                            $filteredAttendances = $attendances->where('type', 'lecture');
                        } elseif (request('type') === 'lab') {
                            $filteredAttendances = $attendances->where('type', 'lab');
                        }
                    @endphp
                    @forelse ($filteredAttendances as $attendance)
                        <tr>
                            <td>{{ ucfirst($attendance->type) }}</td>
                            <td>{{ $attendance->date->format('Y M d') }}</td>
                            <td>
                                <span class="badge badge-{{ $attendance->status === 'present' ? 'success' : ($attendance->status === 'absent' ? 'danger' : 'warning') }}">
                                    {{ ucfirst($attendance->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">No attendance records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Attendance Rate -->
            <div class="mt-3">
                <h4>Overall Attendance Rate: <span class="badge badge-info">{{ $attendanceRate }}%</span></h4>
                <p>(Based on "Present" sessions out of {{ $totalSessions }} total sessions)</p>
            </div>
        </div>
    </div>

    <a href="{{ route('student.courses') }}" class="btn btn-secondary">Back to Courses</a>
@endsection