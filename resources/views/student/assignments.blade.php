@extends('layouts.student')

@section('title', 'Assignments')

@section('content')
    <h1 class="pb-5"Your Assignments</h1>
    @if ($submissions->isNotEmpty())
        <div class="table-responsive">
            <table class="table table-dark table-bordered">
                <thead>
                    <tr>
                        <th>Course</th>
                        <th>Assignment</th>
                        <th>Created At</th>
                        <th>Due Date</th>
                        <th>Status</th>
                        <th>Submitted At</th>
                        <th>Score</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($submissions as $submission)
                        <tr>
                            <td>{{ $submission->assignment->course->code }}: {{ $submission->assignment->course->name }}</td>
                            <td>{{ $submission->assignment->title }}</td>
                            <td>{{ $submission->assignment->created_at->format('M d, Y') }}</td>
                            <td>{{ $submission->assignment->due_date->format('M d, Y') }}</td>
                            <td>
                                <div class="btn btn-{{ $submission->status == 'submitted' ? 'success' : 'danger' }}">
                                    {{ ucfirst($submission->status) }}
                                </div>
                            </td>
                            <td>{{ $submission->submitted_at ? $submission->submitted_at->format('M d, Y') : 'N/A' }}</td>
                            <td>{{ $submission->score }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p class="text-center">No assignments found.</p>
    @endif
@endsection