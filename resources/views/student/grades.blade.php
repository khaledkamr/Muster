@extends('layouts.student')

@section('title', 'Grades')

@section('content')
    <div class="container">
        <h1 class="pb-5 pt-3 text-dark fw-bold">Your Grades</h1>

        <div class="row mb-4">
            <div class="col-md-6">
                <label for="semesterFilter" class="form-label text-dark fw-bold">Select Semester:</label>
                <select id="semesterFilter" class="form-select" style="max-width: 300px;">
                    <option value="" selected disabled>Select a semester</option>
                    @foreach ($semesters as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Semester Statistics -->
        <div class="row mb-4" id="semesterStats" style="display: none;">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title text-dark fw-bold">Semester Statistics</h5>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Earned Hours:</span>
                            <span class="fw-bold" id="semesterCredits">0</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Semester GPA:</span>
                            <span class="fw-bold" id="semesterGPA">0.00</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title text-dark fw-bold">Cumulative Statistics</h5>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Total Hours:</span>
                            <span class="fw-bold" id="totalCredits">0</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">CGPA:</span>
                            <div class="d-flex align-items-center">
                                <span class="fw-bold me-2" id="cgpa">0.00</span>
                                <i id="cgpaTrend" class="fas fa-circle text-secondary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-container">
            <table class="table table-striped" id="gradesTable">
                <thead>
                    <tr>
                        <th class="bg-dark text-light text-center" width="10%">Course Code</th>
                        <th class="bg-dark text-light text-center" width="20%">Course Name</th>
                        <th class="bg-dark text-light text-center" width="10%">Hours</th>
                        <th class="bg-dark text-light text-center" width="15%">Difficulty</th>
                        <th class="bg-dark text-light text-center" width="15%">Type</th>
                        <th class="bg-dark text-light text-center" width="10%">Grade</th>
                        <th class="bg-dark text-light text-center" width="20%">Action</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const semesterFilter = document.getElementById('semesterFilter');
            const gradesTableBody = document.getElementById('gradesTable').querySelector('tbody');
            const semesterStats = document.getElementById('semesterStats');
            const currentSemester = '{{ $currentSemesterValue }}';

            const enrollments = @json($enrollments);
            const startYear = {{ $startYear }};
            const semesterStatsData = @json($semesterStats);

            const courseDetailsBaseUrl = "{{ route('student.course-details', ':id') }}";

            function updateSemesterStats(semesterValue) {
                const stats = semesterStatsData[semesterValue];
                if (stats) {
                    document.getElementById('semesterCredits').textContent = stats.credits;
                    document.getElementById('semesterGPA').textContent = stats.gpa.toFixed(2);
                    document.getElementById('totalCredits').textContent = stats.total_credits;
                    document.getElementById('cgpa').textContent = stats.cgpa.toFixed(2);

                    const trendIcon = document.getElementById('cgpaTrend');
                    trendIcon.className = '';
                    if (stats.cgpa_trend === 'up') {
                        trendIcon.classList.add('fas', 'fa-circle-up', 'text-success');
                    } else if (stats.cgpa_trend === 'down') {
                        trendIcon.classList.add('fas', 'fa-circle-down', 'text-danger');
                    } else {
                        trendIcon.classList.add('fa-regular', 'fa-circle', 'text-secondary');
                    }
                }
            }

            semesterFilter.addEventListener('change', function() {
                const selectedSemester = this.value;
                gradesTableBody.innerHTML = '';
                semesterStats.style.display = selectedSemester ? 'flex' : 'none';

                if (!selectedSemester) {
                    return;
                }

                updateSemesterStats(selectedSemester);

                const [yearPart, semesterKey] = selectedSemester.split('-');
                const selectedYear = parseInt(yearPart.replace('year', ''));

                const semesterCourses = enrollments.filter(enrollment => {
                    const enrollmentYear = parseInt(new Date(enrollment.enrolled_at)
                        .getFullYear()) - startYear + 1;
                    return enrollmentYear === selectedYear && enrollment.course.semester ===
                        semesterKey;
                });

                semesterCourses.forEach(enrollment => {
                    const course = enrollment.course;
                    const grade = course.grades.find(g => g.student_id === {{ $user->id }}) ||
                        {};
                    const row = document.createElement('tr');
                    const courseDetailsUrl = courseDetailsBaseUrl.replace(':id', course.id);
                    row.innerHTML = `
                    <td class="text-center">${course.code}</td>
                    <td class="text-center">${course.name}</td>
                    <td class="text-center">${course.credit_hours}</td>
                    <td class="text-center">${course.difficulty.charAt(0).toUpperCase() + course.difficulty.slice(1)}</td>
                    <td class="text-center">${course.type.charAt(0).toUpperCase() + course.type.slice(1)}</td>
                    <td class="text-center text-${grade.status === 'pass' ? 'success' : 'danger'}">${grade.grade || '-'}</td>
                    <td class="text-center action-icons">
                        <a href="${courseDetailsUrl}" class="btn btn-sm btn-success text-light ps-3 pe-3">
                            <i class="fa-solid fa-arrow-up-right-from-square text-light"></i> Details
                        </a>
                    </td>
                `;
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

        .card {
            border: none;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        .card-body {
            padding: 1.5rem;
        }

        .card-title {
            margin-bottom: 1.5rem;
            font-size: 1.1rem;
        }

        .text-muted {
            color: #6c757d !important;
        }

        .fw-bold {
            font-weight: 600 !important;
        }
    </style>
@endsection
