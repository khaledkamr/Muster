@extends('layouts.admin')

@section('title', 'home')

@section('content')
<div class="container mt-4">
    <div class="row">
        {{-- Users overview --}}
        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title text-dark fw-bold mb-4">Users overview</h5>
                    <div class="events-container">
                        <div class="event-card mb-3 p-3 bg-light rounded shadow-sm border-start border-4 border-primary">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1 fw-bold">Total users</h6>
                                </div>
                                <p class="mb-0 text-muted fw-bold">
                                    {{ $usersCount }} <i class="fa-solid fa-users ms-2"></i>
                                </p>
                            </div>
                        </div>
                        <div class="event-card mb-3 p-3 bg-light rounded shadow-sm border-start border-4 border-primary">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1 fw-bold">Total students</h6>
                                </div>
                                <p class="mb-0 text-muted fw-bold">
                                    {{ $studentsCount }} <i class="fa-solid fa-user-graduate ms-2"></i>
                                </p>
                            </div>
                        </div>
                        <div class="event-card mb-3 p-3 bg-light rounded shadow-sm border-start border-4 border-primary">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1 fw-bold">Total professors</h6>
                                </div>
                                <p class="mb-0 text-muted fw-bold">
                                    {{ $professorsCount }} <i class="fa-solid fa-user-tie ms-2"></i>
                                </p>
                            </div>
                        </div>
                        <div class="event-card mb-3 p-3 bg-light rounded shadow-sm border-start border-4 border-primary">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1 fw-bold">Total admins</h6>
                                </div>
                                <p class="mb-0 text-muted fw-bold">
                                    {{ $adminsCount }} <i class="fa-solid fa-user-gear ms-2"></i>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <style>
                /* .event-card {
                    border-left: 4px solid #002361;
                } */
            </style>
        </div>
        {{-- Students growth --}}
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <h5 class="text-dark fw-bold mb-3">Students growth</h5>
                        <i class="fa-solid fa-chart-line fa-lg mb-3 ps-2"></i>
                    </div>
                    <div class="chart-container" style="position: relative; height:300px;">
                        <canvas id="studentsGrowth"></canvas>
                    </div>
                </div>
            </div>
        </div>
        {{-- Students performance --}}
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <h5 class="text-dark fw-bold mb-3">Students performance</h5>
                        <i class="fa-solid fa-chart-pie mb-3 fa-lg ps-2"></i>
                    </div>
                    <form method="GET" action="" class="d-flex flex-column mb-3">
                        <div class="d-flex">
                            <select id="course" name="course" class="form-select" onchange="this.form.submit()">
                                @foreach($currentSemesterCourses as $course)
                                    <option value="{{ $course->id }}"  {{ request()->query('course') == $course->id ? 'selected' : ''}}>{{ $course->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                    <div class="chart-container" style="position: relative; height:248px;">
                        <canvas id="studentsDistribution"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        {{-- Students status by course --}}
        <div class="col-md-7">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <h5 class="text-dark fw-bold mb-3">Students status by course</h5>
                        <i class="fa-solid fa-chart-bar mb-3 fa-lg ps-2"></i>
                    </div>
                    <div class="d-flex justify-content-center align-items-center">
                        <div class="me-3 d-flex align-items-center">
                            <span class="me-1 rounded-circle bg-primary d-block" style="width: 15px; height: 15px;"></span>
                            <span class="text-muted d-block fs-6">Pass</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="me-1 rounded-circle bg-danger d-block" style="width: 15px; height: 15px;"></span>
                            <span class="text-muted d-block fs-6">Fail</span>
                        </div>
                    </div>
                    @foreach($passFailPercentage as $course)
                        <div class="d-flex align-items-center mt-3">
                            <a href="" class="text-decoration-none text-dark me-3" title="{{ $course->name }}" data-bs-toggle="tooltip" data-bs-title="{{ $course->name }}">{{ $course->code }}</a>
                            <div class="progress-stacked flex-grow-1" style="height: 30px">
                                <div class="progress" role="progressbar" aria-label="Segment one" aria-valuenow="{{ $course->passPercentage }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $course->passPercentage }}%; height: 30px">
                                    <div class="progress-bar bg-primary fw-bold"> {{ round($course->passPercentage, 2) }}% </div>
                                </div>
                                <div class="progress" role="progressbar" aria-label="Segment two" aria-valuenow="{{ $course->failPercentage }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $course->failPercentage }}%; height: 30px">
                                    <div class="progress-bar bg-danger fw-bold"> {{ round($course->failPercentage, 2) }}% </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        {{-- Top 5 Courses by Enrollments --}}
        <div class="col-md-5">
            <div class="card bg-light text-dark border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <h5 class="card-title fw-bold mb-3">Top 5 Courses by Enrollments</h5>
                        <i class="fa-solid fa-chart-column fa-lg mb-3 ps-2"></i>
                    </div>
                    <div class="chart-container" style="position: relative; height:300px;">
                        <canvas id="topCoursesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const studentsGrowth = document.getElementById('studentsGrowth').getContext('2d');
        new Chart(studentsGrowth, {
            type: 'line',
            data: {
                labels: ['2022-Jan', '2022-Aug', '2023-Jan', '2023-Aug', '2024-Jan', '2024-Aug', '2025-Jan', '2025-Aug'],
                datasets: [{
                    label: 'Students',
                    data: @json($userRegistrationTrend),
                    borderColor: '#007bff',
                    backgroundColor: 'rgba(0, 123, 255, 0.1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: false,
                    pointBackgroundColor: '#007bff',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 500,
                        },
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            maxRotation: 45,
                            minRotation: 30
                        },
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

        const studentsDistribution = document.getElementById('studentsDistribution').getContext('2d');
        new Chart(studentsDistribution, {
            type: 'pie',
            data: {
                labels: ['high performance', 'average performance', 'at risk'],
                datasets: [{
                    data: @json($studentsPerformance),
                    backgroundColor: [
                        'rgba(0, 123, 255, 0.65)', 
                        'rgba(102, 16, 242, 0.65)',
                        'rgba(220, 53, 69, 0.65)', 
                        'rgba(40, 167, 69, 0.65)', 
                    ],
                    borderColor: [
                        '#fff',
                        '#fff',
                        '#fff',
                        '#fff',
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#212529',
                            padding: 15,
                            usePointStyle: true,
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `Total: ${context.raw}`;
                            }
                        }
                    }
                }
            }
        });

        const topCoursesChart = document.getElementById('topCoursesChart').getContext('2d');
        new Chart(topCoursesChart, {
            type: 'bar',
            data: {
                labels: @json($topFiveCourses['labels']),
                datasets: [{
                    label: 'students distribution',
                    data: @json($topFiveCourses['data']),
                    backgroundColor: 'rgba(0, 123, 255, 0.7)',
                    borderColor: '#007bff',
                    borderWidth: 1,
                    borderRadius: 15,
                    barThickness: 50,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `Total: ${context.raw} students`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: Math.max(...@json($topFiveCourses['data'])) + 10,
                        ticks: {
                            stepSize: 30
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
