@extends('layouts.professor')

@section('title', 'Grades')

@section('content')
    <style>
        .table-container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
        }

        .table thead {
            background-color: #f8f9fa;
            color: #333;
        }

        .table th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
            font-size: 14px;
            border-bottom: 1px solid #e9ecef;
        }

        .table td {
            padding: 15px;
            font-size: 14px;
            color: #333;
            border-bottom: 1px solid #e9ecef;
        }

        .table tbody tr:hover {
            background-color: #f1f3f5;
        }

        .table .status-pending {
            background-color: #fff3cd;
            color: #856404;
            padding: 5px 10px;
            border-radius: 12px;
            font-size: 12px;
            display: inline-block;
        }

        .table .status-completed {
            background-color: #d4edda;
            color: #155724;
            padding: 5px 10px;
            border-radius: 12px;
            font-size: 12px;
            display: inline-block;
        }

        .table .status-refunded {
            background-color: #f8d7da;
            color: #721c24;
            padding: 5px 10px;
            border-radius: 12px;
            font-size: 12px;
            display: inline-block;
        }

        .action-icons a,
        .action-icons form {
            display: inline-block;
            margin-right: 5px;
        }

        .action-icons i {
            font-size: 16px;
            color: #6c757d;
        }

        .action-icons i:hover {
            color: #007bff;
        }

        .action-icons .delete-icon:hover {
            color: #dc3545;
        }

        .action-icons form button {
            background: none;
            border: none;
            padding: 0;
        }

        .search-container {
            margin-bottom: 20px;
            display: flex;
            justify-content: flex-start;
        }

        .search-container input {
            width: 250px;
            margin-right: 10px;
        }

        .nav-tabs .nav-link {
            color: #495057;
        }

        .nav-tabs .nav-link.active {
            background-color: #ffffff;
            border-color: #dee2e6 #dee2e6 #ffffff;
            color: #007bff;
        }

        .pagination {
            margin: 0;
            padding: 0;
        }

        .pagination .page-item .page-link {
            color: #0A9442;
            border: 1px solid #dee2e6;
            padding: 8px 16px;
            margin: 0 2px;
            border-radius: 4px;
            transition: 0.3s;
        }

        .pagination .page-item.active .page-link {
            background-color: #0A9442;
            border-color: #0A9442;
            color: white;
        }

        .pagination .page-item .page-link:hover {
            background-color: #0A9442;
            border-color: #0A9442;
            color: white;
        }

        .pagination .page-item.disabled .page-link {
            color: #fff;
            pointer-events: none;
            background-color: #d5d7d8;
            border-color: #dee2e6;
        }

        .table .status-on-track {
            background-color: #cdd0e7;
            color: #2b2ed1;
            padding: 5px 10px;
            border-radius: 12px;
            font-size: 12px;
            display: inline-block;
        }

        .table .status-risk {
            background-color: #f8d7da;
            color: #721c24;
            padding: 5px 10px;
            border-radius: 12px;
            font-size: 12px;
            display: inline-block;
        }
    </style>
    <div class="container">
        <h2 class="text-dark fw-bold pt-2 pb-4">{{ $course->name }} / Grades</h2>

        <div class="container">
            <div class="row mb-4">
                <div class="col-md-6">
                    <form method="GET" action="{{ route('professor.course.grades', $courseId) }}" class="d-flex flex-column">
                        <label for="search" class="form-label text-dark fw-bold">Search for student:</label>
                        <div class="d-flex">
                            <input type="text" name="search" class="form-control" placeholder="Search by ID or Name"
                                value="{{ request()->query('search') }}">
                            <button type="submit" class="btn btn-primary ms-2" style="background-color: #0A9442;">Search</button>
                        </div>
                    </form>
                </div>
                <div class="col-md-6">
                    <form method="GET" action="{{ route('professor.course.grades', $courseId) }}"
                        class="d-flex flex-column">
                        <label for="statusFilter" class="form-label text-dark fw-bold">Filter by Status:</label>
                        <div class="d-flex">
                            <select id="statusFilter" name="status" class="form-select" onchange="this.form.submit()">
                                <option value="all"
                                    {{ request()->query('status') === 'all' || !request()->query('status') ? 'selected' : '' }}>
                                    All Students</option>
                                <option value="on-track" {{ request()->query('status') === 'on-track' ? 'selected' : '' }}>
                                    On Track</option>
                                <option value="at-risk" {{ request()->query('status') === 'at-risk' ? 'selected' : '' }}>At
                                    Risk</option>
                            </select>
                            @if (request()->query('search'))
                                <input type="hidden" name="search" value="{{ request()->query('search') }}">
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            @if (!request()->query('search'))
                <div class="row mb-3">
                    <div class="col-md-4">
                        <div class="card border-0 shadow">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="card-subtitle mb-2 text-dark fw-bold">
                                            Highest Grade
                                        </h5>
                                        <p class="card-text text-muted d-flex align-items-center">
                                            <img src="{{ asset('imgs/user.png') }}" alt="Student" class="me-2"
                                                style="width: 24px; height: 24px;">
                                            {{ $stats['max_grade']['student_name'] }}
                                            <i class="fas fa-crown text-muted ms-2"></i>
                                        </p>
                                    </div>
                                    <div>
                                        <h3 class="card-title fs-1 text-success fw-bold">
                                            {{ $stats['max_grade']['value'] }}</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="card-subtitle mb-2 text-dark fw-bold">
                                            Lowest Grade
                                        </h5>
                                        <p class="card-text text-muted d-flex align-items-center">
                                            <img src="{{ asset('imgs/user.png') }}" alt="Student" class="me-2"
                                                style="width: 24px; height: 24px;">
                                            {{ $stats['min_grade']['student_name'] }}
                                            <i class="fas fa-thumbs-down text-muted ms-2"></i>
                                        </p>
                                    </div>
                                    <div>
                                        <h3 class="card-title fs-1 text-danger fw-bold">
                                            {{ $stats['min_grade']['value'] }}</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="card-subtitle mb-2 text-dark fw-bold">
                                            Average Grade
                                        </h5>
                                        <p class="card-text text-muted d-flex align-items-center">
                                            <i class="fas fa-users me-2"></i> Total Students:
                                            {{ $stats['total_students'] }}
                                        </p>
                                    </div>
                                    <div>
                                        <h3 class="card-title fs-1 text-primary fw-bold">
                                            {{ $stats['average_grade'] }}</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('errors'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('errors') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="table-container">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th class="text-center bg-dark text-white">ID</th>
                            <th class="text-center bg-dark text-white">Name</th>
                            <th class="text-center bg-dark text-white">Quiz 1</th>
                            <th class="text-center bg-dark text-white">Midterm</th>
                            <th class="text-center bg-dark text-white">Quiz 2</th>
                            <th class="text-center bg-dark text-white">Assignments</th>
                            <th class="text-center bg-dark text-white">Project</th>
                            <th class="text-center bg-dark text-white">Final</th>
                            <th class="text-center bg-dark text-white">Total</th>
                            <th class="text-center bg-dark text-white">Status</th>
                            <th class="text-center bg-dark text-white">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($students->isEmpty())
                            <tr>
                                <td colspan="10" class="text-center">
                                    <div class="status-refunded fs-6">No students enrolled in this course.</div>
                                </td>
                            </tr>
                        @else
                            @foreach ($students as $student)
                                @php
                                    $grade = $student['grades'];
                                    $quiz1 = $grade && !empty($grade['quiz1']) ? $grade['quiz1'] : '-';
                                    $midterm = $grade && !empty($grade['midterm']) ? $grade['midterm'] : '-';
                                    $quiz2 = $grade && !empty($grade['quiz2']) ? $grade['quiz2'] : '-';
                                    $assignments = $grade && !empty($grade['assignments']) ? $grade['assignments'] : '-';
                                    $project = $grade && !empty($grade['project']) ? $grade['project'] : '-';
                                    $final = $grade && !empty($grade['final']) ? $grade['final'] : '-';
                                    $total = $grade
                                        ? $grade['quiz1'] +
                                            $grade['midterm'] +
                                            $grade['quiz2'] +
                                            $grade['assignments'] +
                                            $grade['project'] +
                                            $grade['final']
                                        : '-';
                                @endphp
                                <tr>
                                    <td class="text-center">{{ $student['id'] }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('professor.student.profile', ['studentId' => $student['id'], 'courseId' => $courseId]) }}"
                                            class="text-dark text-decoration-none">
                                            {{ $student['name'] }}
                                        </a>
                                    </td>
                                    <td class="text-center">{{ $quiz1 }}</td>
                                    <td class="text-center">{{ $midterm }}</td>
                                    <td class="text-center">{{ $quiz2 }}</td>
                                    <td class="text-center">{{ $assignments }}</td>
                                    <td class="text-center">{{ $project }}</td>
                                    <td class="text-center">{{ $final }}</td>
                                    <td class="text-center">{{ is_numeric($total) ? $total : '-' }}</td>
                                    <td class="text-center">
                                        <span
                                            class="status-{{ $student['performance_group'] == 'At risk' ? 'risk' : 'on-track' }}">
                                            {{ $student['performance_group'] == 'At risk' ? 'At Risk' : 'On Track' }}
                                        </span>
                                    </td>
                                    <td class="action-icons text-center">
                                        <a href="{{ route('professor.course.student.details', ['course_id' => $courseId, 'student_id' => $student['id']]) }}"
                                            title="View"><i class="fa-solid fa-eye"></i></a>
                                        <button class="btn btn-link p-0 pb-1 m-0" type="button" data-bs-toggle="modal" data-bs-target="#sendFeedbackModal{{ $student['id'] }}">
                                            <i class="fa-solid fa-message" title="Send"></i>
                                        </button>
                                    </td>
                                </tr>

                                <!-- Feedback Modal -->
                                <div class="modal fade" id="sendFeedbackModal{{ $student['id'] }}" tabindex="-1" aria-labelledby="sendFeedbackModalLabel{{ $student['id'] }}" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content rounded-4">
                                            <div class="modal-header">
                                                <h5 class="modal-title text-dark fw-bold" id="sendFeedbackModalLabel{{ $student['id'] }}">Feedback for <span>{{ $student['name'] }}</span></h5>
                                                <button type="button" class="btn-close text-muted" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form method="POST" action="{{ route('professor.send.feedback', ['studentId' => $student['id'], 'courseId' => $courseId]) }}">
                                                    @csrf
                                                    <div class="mb-3">
                                                        <label for="feedback-message{{ $student['id'] }}" class="form-label text-dark">Feedback Message</label>
                                                        <textarea name="content" class="form-control" id="feedback-message{{ $student['id'] }}" rows="4" placeholder="Enter your feedback..." required></textarea>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="feedback-rating{{ $student['id'] }}" class="form-label text-dark">Rating</label>
                                                        <select name="rate" class="form-select" id="feedback-rating{{ $student['id'] }}" required>
                                                            <option value="" disabled selected>Select a rating</option>
                                                            <option value="excellent">Excellent</option>
                                                            <option value="good">Good</option>
                                                            <option value="average">Average</option>
                                                            <option value="bad">Bad</option>
                                                        </select>
                                                    </div>
                                                    <div class="d-flex justify-content-end align-items-center gap-3">
                                                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-primary">Submit Feedback</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $students->appends(request()->query())->onEachSide(1)->links() }}
            </div>
        </div>
    </div>
@endsection
