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
</style>
<h2 class="text-dark fw-bold pt-2 pb-4">{{ $course->name }} / Students</h2>

<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <a href="" class="nav-link active">All Students</a>
    </li>
    <li class="nav-item">
        <a href="" class="nav-link">High Performance</a>
    </li>
    <li class="nav-item">
        <a href="" class="nav-link">Average Performance</a>
    </li>
    <li class="nav-item">
        <a href="" class="nav-link">Low Performance</a>
    </li>
</ul>

<div class="container">
    <div class="table-container">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th class="text-center bg-dark text-white">ID</th>
                    <th class="text-center bg-dark text-white">Name</th>
                    <th class="text-center bg-dark text-white">Year</th>
                    <th class="text-center bg-dark text-white">Email</th>
                    <th class="text-center bg-dark text-white">Performance</th>
                    <th class="text-center bg-dark text-white">Actions</th>
                </tr>
            </thead>
            <tbody>
                @if($students->isEmpty())
                <tr>
                    <td colspan="6" class="text-center">
                        <div class="status-refunded fs-6">No students enrolled in this course.</div>
                    </td>
                </tr>
                @else
                @foreach($students as $student)
                <tr>
                    <td class="text-center">{{ $student->id }}</td>
                    <td class="text-center">{{ $student->name }}</td>
                    <td class="text-center">{{ $student->year }}</td>
                    <td class="text-center">{{ $student->email }}</td>
                    <td class="text-center">
                        <span class="status-{{ $loop->iteration % 3 == 0 ? 'pending' : ($loop->iteration % 3 == 1 ? 'completed' : 'refunded') }}">
                            {{ $loop->iteration % 3 == 0 ? 'Average' : ($loop->iteration % 3 == 1 ? 'High' : 'Low') }}
                        </span>
                    </td>
                    <td class="action-icons text-center">
                        <a href="{{ route('professor.student.profile', ['studentId' => $student->id, 'courseId' => $courseId]) }}" title="View"><i class="fa-solid fa-eye"></i></a>
                        <a href=""><i class="fa-solid fa-message" title="send"></i></a>
                    </td>
                </tr>
                @endforeach
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection