@extends('layouts.professor')

@section('title', 'Assignments')

@section('content')
    <h1 class="pb-5">Your Assignments</h1>
    @if ($assignments->isNotEmpty())
        <div class="table-responsive">
            <table class="table table-dark table-bordered">
                <thead>
                    <tr>
                        <th>Course</th>
                        <th>Assignment</th>
                        <th>Created At</th>
                        <th>Due Date</th>
                        <th>Submissions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($assignments as $assignment)
                        <tr>
                            <td>{{ $assignment->course->code }}: {{ $assignment->course->name }}</td>
                            <td>{{ $assignment->title }}</td>
                            <td>{{ $assignment->created_at->format('M d, Y') }}</td>
                            <td>{{ $assignment->due_date->format('M d, Y') }}</td>
                            <td>
                                @php
                                    $submitted = $assignment->submissions->where('status', 'submitted')->count();
                                    $total = $assignment->submissions->count();
                                @endphp
                                {{ $submitted }} / {{ $total }} Submitted
                                <ul class="list-group mt-2">
                                    @foreach ($assignment->submissions as $submission)
                                        <li class="list-group-item bg-transparent text-white">
                                            {{ $submission->user->name }}: 
                                            <div class="btn btn-{{ $submission->status == 'submitted' ? 'success' : 'danger' }}">
                                                {{ ucfirst($submission->status) }}
                                            </div>
                                            @if ($submission->status === 'submitted')
                                                (Score: {{ $submission->score }}, Submitted: {{ $submission->submitted_at->format('M d, Y') }})
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p class="text-center">No assignments created yet.</p>
    @endif
@endsection