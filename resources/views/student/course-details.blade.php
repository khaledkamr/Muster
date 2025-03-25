@extends('layouts.student')

@section('title', 'Course Details - ' . $course->name)

@section('content')
    <h1 class="pb-3 pt-3 text-dark">{{ $course->code }}: {{ $course->name }}</h1>
        
    <div class="card-body" style="">
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
                <tr class="table-primary">
                    <td><strong>Total</strong></td>
                    <td><strong>{{ $grade->total }}</strong></td>
                    <td><strong>170</strong></td>
                </tr>
            </tbody>
        </table>

        <div class="mt-3 text-dark">
            <h4>Final Grade: <span class="badge bg-{{ $grade->status === 'pass' ? 'success' : 'danger' }}">{{ $grade->grade }}</span></h4>
            <p>Status: <strong>{{ $grade->status }}</strong></p>
        </div>
    </div>

    <a href="{{ route('student.grades') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i>
        Back to Courses
    </a>

    <!DOCTYPE html>
   
    <style>
        .progress-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            font-family: Arial, sans-serif;
            background: #f5ebe3;
            padding: 20px;
            width: 200px;
            border-radius: 10px;
        }

        .progress-text {
            position: absolute;
            font-size: 20px;
            font-weight: bold;
            color: #333;
        }

        .progress-circle {
            transform: rotate(-90deg);
            transition: stroke-dasharray 1s ease-in-out;
        }
    </style>

    <div class="progress-container">
        <span>CGPA</span>
        <svg width="200" height="200" viewBox="0 0 100 100">
            <!-- Background Circle -->
            <circle cx="50" cy="50" r="40" stroke="#dbeafe" stroke-width="10" fill="none"/>
            <!-- Progress Circle -->
            <circle cx="50" cy="50" r="40" stroke="#2563eb" stroke-width="10" fill="none"
                    stroke-linecap="round" class="progress-circle"
                    stroke-dasharray="0 251.2"/>
        </svg>
        <div class="progress-text" style="position: absolute;">3.58</div>
    </div>

    <script>
        function setProgress(value) {
            const radius = 40;
            const circumference = 2 * Math.PI * radius;
            const progress = (value / 4) * circumference; // Assuming CGPA max = 4
            document.querySelector('.progress-circle').setAttribute('stroke-dasharray', `${progress} ${circumference}`);
        }

        setProgress(3.58);
    </script>


@endsection