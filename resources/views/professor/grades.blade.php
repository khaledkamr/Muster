@extends('layouts.professor')

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
    .action-icons a, .action-icons form {
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
</style>
<h2 class="text-dark fw-bold pt-2 pb-4">{{ $course->name }} / Grades</h2>

<div class="container">
    <div class="search-container">
        <form method="GET" action="{{ route('professor.course.grades', $courseId) }}" class="d-flex flex-column">
            <label for="search" class="form-label text-dark fw-bold">Search for student:</label>
            <div class="d-flex">
                <input type="text" name="search" class="form-control" placeholder="Search by ID or Name" value="{{ request()->query('search') }}">
                <button type="submit" class="btn btn-primary" style="background-color: #0A9442;">Search</button>
            </div>
        </form>
    </div>

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
                    <th class="text-center bg-dark text-white">Actions</th>
                </tr>
            </thead>
            <tbody>
                @if($students->isEmpty())
                <tr>
                    <td colspan="10" class="text-center">
                        <div class="status-refunded fs-6">No students enrolled in this course.</div>
                    </td>
                </tr>
                @else
                @foreach($students as $student)
                @php
                    $grade = $student->grades->first();
                    $quiz1 = ($grade && !empty($grade->quiz1)) ? $grade->quiz1 : '-';
                    $midterm = ($grade && !empty($grade->midterm)) ? $grade->midterm : '-';
                    $quiz2 = ($grade && !empty($grade->quiz2)) ? $grade->quiz2 : '-';
                    $assignments = ($grade && !empty($grade->assignments)) ? $grade->assignments : '-';
                    $project = ($grade && !empty($grade->project)) ? $grade->project : '-';
                    $final = ($grade && !empty($grade->final)) ? $grade->final : '-';
                    $total = $grade ? ($grade->quiz1 + $grade->midterm + $grade->quiz2 + $grade->assignments + $grade->project + $grade->final) : '-';
                @endphp
                <tr>
                    <td class="text-center">{{ $student->id }}</td>
                    <td class="text-center">{{ $student->name }}</td>
                    <td class="text-center">{{ $quiz1 }}</td>
                    <td class="text-center">{{ $midterm }}</td>
                    <td class="text-center">{{ $quiz2 }}</td>
                    <td class="text-center">{{ $assignments }}</td>
                    <td class="text-center">{{ $project }}</td>
                    <td class="text-center">{{ $final }}</td>
                    <td class="text-center">{{ is_numeric($total) ? $total : '-' }}</td>
                    <td class="action-icons text-center">
                        <a href="{{ route('professor.student.profile', ['studentId' => $student->id, 'courseId' => $courseId]) }}" title="View"><i class="fa-solid fa-eye"></i></a>
                        <a href="#"><i class="fa-solid fa-message" title="Send"></i></a>
                    </td>
                </tr>
                @endforeach
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection