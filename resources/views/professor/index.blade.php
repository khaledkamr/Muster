<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Professor Home Page</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #343a40;
            color: #ffffff;
            font-family: Arial, sans-serif;
        }
        .jumbotron {
            background-color: #495057;
            border-radius: 10px;
            padding: 2rem;
        }
        .accordion .card {
            background-color: #495057;
            border: 1px solid #6c757d;
            margin-bottom: 10px;
        }
        .accordion .card-header {
            background-color: #6c757d;
            cursor: pointer;
            padding: 1rem;
        }
        .accordion .card-header h5 {
            margin: 0;
            color: #ffffff;
        }
        .accordion .card-body {
            background-color: #343a40;
            padding: 1rem;
        }
        .list-group-item {
            background-color: #495057;
            color: #ffffff;
            border: 1px solid #6c757d;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }
        .btn-danger:hover {
            background-color: #b02a37;
            border-color: #b02a37;
        }
        hr {
            border-top: 1px solid #6c757d;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="jumbotron text-center">
            <h1 class="display-4">Hello, Professor {{ $user->name }}!</h1>
            <p class="lead">Welcome to your home page.</p>
            <hr class="my-4">
            <p>Below are the courses you teach, along with enrollment details.</p>
            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-danger btn-lg mt-3">Logout</button>
            </form>
        </div>

        <!-- Courses Accordion -->
        <div class="accordion" id="coursesAccordion">
            @php
                $courses = $user->courses()->withCount('enrollments')->get();
            @endphp

            @forelse ($courses as $index => $course)
                <div class="card">
                    <div class="card-header" id="heading{{ $index }}"
                         data-toggle="collapse" data-target="#collapse{{ $index }}"
                         aria-expanded="false" aria-controls="collapse{{ $index }}">
                        <h5 class="mb-0">
                            {{ $course->code }}: {{ $course->name }} ({{ $course->enrollments_count }} Enrolled)
                        </h5>
                    </div>
                    <div id="collapse{{ $index }}" class="collapse"
                         aria-labelledby="heading{{ $index }}" data-parent="#coursesAccordion">
                        <div class="card-body">
                            @if ($course->enrollments->isNotEmpty())
                                <ul class="list-group">
                                    @foreach ($course->enrollments as $enrollment)
                                        @php
                                            $student = $enrollment->student; 
                                        @endphp
                                        <li class="list-group-item">
                                            <strong>{{ $student->name }}</strong> (ID: {{ $student->id }})<br>
                                            Email: {{ $student->email }}<br>
                                            Major: {{ $student->major }}<br>
                                            Year: {{ ucfirst($student->year) }}<br>
                                            Enrolled At: {{ $enrollment->enrolled_at->format('M d, Y') }}
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p>No students enrolled in this course.</p>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="card">
                    <div class="card-body text-center">
                        <p>You are not assigned to teach any courses yet.</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>