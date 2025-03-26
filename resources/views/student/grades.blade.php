@extends('layouts.student')

@section('title', 'Grades')

@section('content')
    <h1 class="pb-5 pt-3 text-dark fw-bold">Your Grades</h1>

    <div class="mb-4">
        <label for="semesterFilter" class="form-label text-dark fw-bold">Select Semester:</label>
        <select id="semesterFilter" class="form-select" style="max-width: 300px;">
            <option value="" selected disabled>Select a semester</option>
            @foreach ($semesters as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </select>
    </div>

    <div class="table-responsive">
        <table class="table table-dark table-bordered" id="gradesTable">
            <thead>
                <tr class="text-center">
                    <th>Course</th>
                    <th>Difficulty</th>
                    <th>Type</th>
                    <th>Grade</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody class="text-center">
                
            </tbody>
        </table>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const semesterFilter = document.getElementById('semesterFilter');
            const gradesTableBody = document.getElementById('gradesTable').querySelector('tbody');
            const currentSemester = '{{ $currentSemesterValue }}';

            const enrollments = @json($enrollments);
            const startYear = {{ $startYear }};

            const courseDetailsBaseUrl = "{{ route('student.course-details', ':id') }}";

            semesterFilter.addEventListener('change', function () {
                const selectedSemester = this.value;
                gradesTableBody.innerHTML = ''; 

                if (!selectedSemester) {
                    return; 
                }

                if (selectedSemester === currentSemester) {
                    return;
                }

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
                    const grade = course.grades.find(g => g.student_id === {{ $user->id }}) || {};
                    const row = document.createElement('tr');
                    const courseDetailsUrl = courseDetailsBaseUrl.replace(':id', course.id);
                    row.innerHTML = `
                        <td>${course.code}: ${course.name}</td>
                        <td>${course.difficulty.charAt(0).toUpperCase() + course.difficulty.slice(1)}</td>
                        <td>${course.type.charAt(0).toUpperCase() + course.type.slice(1)}</td>
                        <td class="text-${grade.status === 'pass' ? 'success' : 'danger'}">${grade.grade || 'N/A'}</td>
                        <td>
                            <a href="${courseDetailsUrl}" class="btn btn-sm btn-success ps-3 pe-3">
                                <i class="fa-solid fa-arrow-up-right-from-square"></i> Details
                            </a>
                        </td>
                    `;
                    gradesTableBody.appendChild(row);
                });
            });
        });
    </script>

    <style>
        .table-dark th, .table-dark td {
            padding-top: 10px;
            padding-bottom: 10px;
        }
        .table-dark th, .table-dark td {
            border: none;
        }
        .table-dark {
            background-color: #343640;
        }
        .form-select {
            background-color: #495057;
            color: #ffffff;
            border: 1px solid #6c757d;
        }
        .form-select:focus {
            background-color: #495057;
            color: #ffffff;
            border-color: #58bc82;
            box-shadow: 0 0 0 0.2rem rgba(88, 188, 130, 0.25);
        }
    </style>
@endsection