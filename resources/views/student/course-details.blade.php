@extends('layouts.student')

@section('title', 'Course Details - ' . $course->name)

@section('content')
    <h1 class="pb-3 pt-3">{{ $course->code }}: {{ $course->name }}</h1>
        
    <div class="card-body" style="background-color: #343a40;">
        <table class="table table-dark table-bordered">
            <thead>
                <tr>
                    <th>Component</th>
                    <th>Score</th>
                    <th>Max Score</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Quiz 1</td>
                    <td>{{ $grade->quiz1 }}</td>
                    <td>10</td>
                </tr>
                <tr>
                    <td>Quiz 2</td>
                    <td>{{ $grade->quiz2 }}</td>
                    <td>10</td>
                </tr>
                <tr>
                    <td>Midterm</td>
                    <td>{{ $grade->midterm }}</td>
                    <td>30</td>
                </tr>
                <tr>
                    <td>Project</td>
                    <td>{{ $grade->project }}</td>
                    <td>30</td>
                </tr>
                <tr>
                    <td>Assignments</td>
                    <td>{{ $grade->assignments }}</td>
                    <td>30</td>
                </tr>
                <tr>
                    <td>Final</td>
                    <td>{{ $grade->final }}</td>
                    <td>60</td>
                </tr>
                <tr class="table-active">
                    <td><strong>Total</strong></td>
                    <td><strong>{{ $grade->total }}</strong></td>
                    <td><strong>170</strong></td>
                </tr>
            </tbody>
        </table>

        <div class="mt-3">
            <h4>Final Grade: <span class="badge badge-{{ $grade->status === 'pass' ? 'success' : 'danger' }}">{{ $grade->grade }}</span></h4>
            <p>Status: <strong>{{ $grade->status }}</strong></p>
        </div>
    </div>

    <a href="{{ route('student.courses') }}" class="btn btn-secondary">Back to Courses</a>
@endsection