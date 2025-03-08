<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Home Page</title>
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
        }
        .card {
            background-color: #495057;
            border: none;
            margin-bottom: 20px;
        }
        .card-header {
            background-color: #6c757d;
            cursor: pointer;
        }
        .card-body {
            background-color: #495057;
        }
        .btn-primary, .btn-danger {
            margin: 10px;
        }
        .accordion .card-header:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="jumbotron text-center">
            <h1 class="display-4">Hello, {{ $user->name }}!</h1>
            <p class="lead">Welcome to your student dashboard.</p>
            <hr class="my-4">
            <p>Explore your profile and enrolled courses below.</p>
        </div>

        <!-- Student Information -->
        <div class="card">
            <div class="card-body">
                <h3 class="card-title">Your Profile</h3>
                <ul class="list-unstyled">
                    <li><strong>Name:</strong> {{ $user->name }}</li>
                    <li><strong>Email:</strong> {{ $user->email }}</li>
                    <li><strong>Age:</strong> {{ $user->birthdate ? \Carbon\Carbon::parse($user->birthdate)->age : 'Not specified' }}</li>
                    <li><strong>Year:</strong> {{ ucfirst($user->year) }}</li>
                    <li><strong>Major:</strong> {{ $user->major ?? 'Not yet assigned' }}</li>
                </ul>
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-danger">Logout</button>
                </form>
            </div>
        </div>

        <!-- Courses by Semester -->
        <div class="accordion" id="semesterAccordion">
            @php
                $currentYear = 2025; // Based on system date (March 08, 2025)
                $yearOffset = match ($user->year) {
                    'freshman' => 0,
                    'sophomore' => 1,
                    'junior' => 2,
                    'senior' => 3,
                };
                $startYear = $currentYear - $yearOffset; // Student’s starting year
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
                        <div class="card">
                            <div class="card-header" id="heading{{ $yearNum }}{{ $semesterKey }}"
                                data-toggle="collapse" data-target="#collapse{{ $yearNum }}{{ $semesterKey }}"
                                aria-expanded="false" aria-controls="collapse{{ $yearNum }}{{ $semesterKey }}">
                                <h5 class="mb-0">
                                    {{ $yearLabel }} - {{ $semesterLabel }} ({{ $semesterCourses->count() }} Courses)
                                </h5>
                            </div>
                            <div id="collapse{{ $yearNum }}{{ $semesterKey }}"
                                class="collapse" aria-labelledby="heading{{ $yearNum }}{{ $semesterKey }}"
                                data-parent="#semesterAccordion">
                                <div class="card-body">
                                    <ul class="list-group list-group-flush">
                                        @foreach ($semesterCourses as $course)
                                            <li class="list-group-item bg-transparent text-white">
                                                {{ $course->code }}: {{ $course->name }}
                                                (Difficulty: {{ ucfirst($course->difficulty) }},
                                                Type: {{ ucfirst($course->type) }})
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

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>