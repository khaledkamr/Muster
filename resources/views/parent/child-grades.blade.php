@extends('layouts.parent')

@section('title', 'Grades')

@section('content')
<div class="container">
    <h1 class="pb-5 pt-3 text-dark fw-bold">{{ $child->name }}'s Grades</h1>
    
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
        <div class="col-md-4">
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
        <div class="col-md-4">
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
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title text-dark fw-bold">Department Statistics</h5>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted">Total Students:</span>
                        <span class="fw-bold" id="totalStudents">0</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Avg CGPA:</span>
                        <div class="d-flex align-items-center">
                            <span class="fw-bold" id="avgcgpa">0.00</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="table-container mb-4">
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
                <tr>
                    <td colspan="7" class="text-center">
                        <div class="status-danger fs-6">No semester selected!</div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Grade Distribution Chart -->
    <div class="row mb-4" id="gradeDistribution" style="display: none;">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title text-dark fw-bold">Grade Distribution</h5>
                    <canvas id="gradeDistributionChart" style="max-height: 300px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- GPA Trend Chart -->
    <div class="row mb-4" id="gpaTrend" style="display: none;">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title text-dark fw-bold">GPA Trend</h5>
                    <canvas id="gpaTrendChart" style="max-height: 300px;"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const semesterFilter = document.getElementById('semesterFilter');
        const gradesTableBody = document.getElementById('gradesTable').querySelector('tbody');
        const semesterStats = document.getElementById('semesterStats');
        const currentSemester = '{{ $currentSemesterValue }}';

        const enrollments = @json($enrollments);
        const startYear = {{ $startYear }};
        const semesterStatsData = @json($semesterStats);
        const gradeLabels = @json($gradeLabels);
        const gpaTrendData = @json($gpaTrendData);

        const courseDetailsBaseUrl = "{{ route('parent.child.course-details', [':courseId', ':childId']) }}"; 

        const gradeDistribution = document.getElementById('gradeDistribution');
        // Initialize Chart.js
        let gradeDistributionChart = null;
        let gpaTrendChart = null;

        // Initialize GPA Trend Chart
        function initializeGpaTrendChart() {
            gpaTrend.style.display = 'flex';
            const ctx = document.getElementById('gpaTrendChart').getContext('2d');

            gpaTrendChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: gpaTrendData.map(item => item.semester),
                    datasets: [{
                        label: 'Semester GPA',
                        data: gpaTrendData.map(item => item.gpa),
                        borderColor: '#28a745',
                        backgroundColor: 'rgba(40, 167, 69, 0.1)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#28a745',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 4.0,
                            ticks: {
                                stepSize: 0.5,
                                callback: function(value) {
                                    return value.toFixed(1);
                                }
                            },
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Semester'
                            },
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `GPA: ${context.parsed.y.toFixed(2)}`;
                                }
                            }
                        }
                    }
                }
            });
        }

        function updateGradeDistributionChart(distributionData) {
            gradeDistribution.style.display = 'flex';
            const ctx = document.getElementById('gradeDistributionChart').getContext('2d');

            if (gradeDistributionChart) {
                gradeDistributionChart.destroy();
            }

            gradeDistributionChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: gradeLabels,
                    datasets: [{
                        label: 'NO. Courses',
                        data: distributionData,
                        backgroundColor: [
                            '#28a745', // A+
                            '#28a745', // A
                            '#28a745', // A-
                            '#17a2b8', // B+
                            '#17a2b8', // B
                            '#17a2b8', // B-
                            '#ffc107', // C+
                            '#ffc107', // C
                            '#ffc107', // C-
                            '#dc3545', // D+
                            '#dc3545', // D
                            '#dc3545', // D-
                            '#dc3545' // F
                        ],
                        borderColor: '#ffffff',
                        borderWidth: 1,
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            },
                            title: {
                                display: true,
                                text: 'Number of Courses'
                            }
                        },
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        }

        function updateSemesterStats(semesterValue) {
            const stats = semesterStatsData[semesterValue];
            if (stats) {
                document.getElementById('semesterCredits').textContent = stats.credits;
                document.getElementById('semesterGPA').textContent = stats.gpa.toFixed(2);
                document.getElementById('totalCredits').textContent = stats.total_credits;
                document.getElementById('cgpa').textContent = stats.cgpa.toFixed(2);

                // Update department statistics
                document.getElementById('totalStudents').textContent = stats.department_stats.total_students;
                document.getElementById('avgcgpa').textContent = stats.department_stats.avg_cgpa.toFixed(2);

                const trendIcon = document.getElementById('cgpaTrend');
                trendIcon.className = '';
                if (stats.cgpa_trend === 'up') {
                    trendIcon.classList.add('fas', 'fa-circle-up', 'text-success');
                } else if (stats.cgpa_trend === 'down') {
                    trendIcon.classList.add('fas', 'fa-circle-down', 'text-danger');
                } else {
                    trendIcon.classList.add('fa-regular', 'fa-circle', 'text-secondary');
                }

                // Update grade distribution chart
                updateGradeDistributionChart(stats.grade_distribution);
            }
        }

        semesterFilter.addEventListener('change', function () {
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
                const enrollmentYear = parseInt(new Date(enrollment.enrolled_at).getFullYear()) - startYear + 1;
                return enrollmentYear === selectedYear && enrollment.course.semester === semesterKey;
            });

            semesterCourses.forEach(enrollment => {
                const course = enrollment.course;
                const grade = course.grades.find(g => g.student_id === {{ $child->id }}) || {};
                const row = document.createElement('tr');
                const courseDetailsUrl = courseDetailsBaseUrl.replace(':courseId', course.id).replace(':childId', {{ $child->id }});
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

            initializeGpaTrendChart();
        });
    });
</script>

<style>
    .table-container {
        /* color: #ec5690; */
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

    .status-danger {
        background-color: #f8d7da;
        color: #721c24;
        padding: 5px 10px;
        border-radius: 12px;
        font-size: 12px;
        display: inline-block;
    }
</style>
@endsection