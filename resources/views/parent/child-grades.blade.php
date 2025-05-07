@extends('layouts.parent')

@section('title', 'Grades')

@section('content')
<div class="container">
    <h1 class="pb-5 pt-3 text-dark fw-bold">{{ $child->name }}'s Grades</h1>

    <div class="mb-4">
        <label for="semesterFilter" class="form-label text-dark fw-bold">Select Semester:</label>
        <select id="semesterFilter" class="form-select" style="max-width: 300px;">
            <option value="" selected disabled>Select a semester</option>
            @foreach ($semesters as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </select>
    </div>

    <div class="container">
        <div class="table-container">
            <table class="table table-striped" id="gradesTable">
                <thead>
                    <tr>
                        <th class="bg-dark text-light text-center">Course Code</th>
                        <th class="bg-dark text-light text-center">Course Name</th>
                        <th class="bg-dark text-light text-center">Difficulty</th>
                        <th class="bg-dark text-light text-center">Type</th>
                        <th class="bg-dark text-light text-center">Grade</th>
                        <th class="bg-dark text-light text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    
                </tbody>
            </table>
        </div>
    </div>
</div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const semesterFilter = document.getElementById('semesterFilter');
            const gradesTableBody = document.getElementById('gradesTable').querySelector('tbody');
            const currentSemester = '{{ $currentSemesterValue }}';

            const enrollments = @json($enrollments);
            const startYear = {{ $startYear }};

            const courseDetailsBaseUrl = "{{ route('parent.child.course-details', [':childId', ':courseId']) }}"; //parent.child.course-details

            semesterFilter.addEventListener('change', function () {
                const selectedSemester = this.value;
                gradesTableBody.innerHTML = ''; 

                if (!selectedSemester) {
                    return; 
                }

                // if (selectedSemester === currentSemester) {
                //     return;
                // }

                // Parse the selected semester (e.g., "year1-first")
                const [yearPart, semesterKey] = selectedSemester.split('-');
                const selectedYear = parseInt(yearPart.replace('year', ''));

                // Filter enrollments for the selected semester
                const semesterCourses = enrollments.filter(enrollment => {
                    const enrollmentYear = parseInt(new Date(enrollment.enrolled_at).getFullYear()) - startYear + 1;
                    return enrollmentYear === selectedYear && enrollment.course.semester === semesterKey;
                });

                semesterCourses.forEach(enrollment => {
                    const course = enrollment.course;
                    const grade = course.grades.find(g => g.student_id === {{ $child->id }}) || {};
                    const row = document.createElement('tr');
                    const courseDetailsUrl = courseDetailsBaseUrl.replace(':childId', {{ $child->id }}).replace(':courseId', course.id);
                    row.innerHTML = `
                        <td class="text-center">${course.code}</td>
                        <td class="text-center">${course.name}</td>
                        <td class="text-center">${course.difficulty.charAt(0).toUpperCase() + course.difficulty.slice(1)}</td>
                        <td class="text-center">${course.type.charAt(0).toUpperCase() + course.type.slice(1)}</td>
                        <td class="text-center text-${grade.status === 'pass' ? 'success' : 'danger'}">${grade.grade || 'N/A'}</td>
                        <td class="text-center action-icons">
                            <a href="${courseDetailsUrl}" class="btn btn-sm btn-success text-light ps-3 pe-3">
                                <i class="fa-solid fa-arrow-up-right-from-square text-light"></i> Details
                            </a>
                        </td>
                    `;
                    console.log(courseDetailsUrl);
                    gradesTableBody.appendChild(row);
                });
            });
        });
    </script>

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
@endsection