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
        {{-- Students distribution --}}
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <h5 class="text-dark fw-bold mb-3">Students distribution</h5>
                        <i class="fa-solid fa-chart-pie mb-3 fa-lg ps-2"></i>
                    </div>
                    <div class="chart-container p-4" style="position: relative; height:300px;">
                        <canvas id="studentsDistribution"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        {{-- Professors distribution --}}
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <h5 class="text-dark fw-bold mb-3">Professors distribution</h5>
                        <i class="fa-solid fa-chart-pie mb-3 fa-lg ps-2"></i>
                    </div>
                    <div class="chart-container p-1" style="position: relative; height:300px;">
                        <canvas id="professorsDistribution"></canvas>
                    </div>
                </div>
            </div>
        </div>
        {{-- Courses distribution --}}
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title text-dark fw-bold mb-4">Courses distribution</h5>
                    <div class="events-container">
                        <div class="event-card mb-3 p-3 bg-light rounded shadow-sm border-start border-4 border-primary">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1 fw-bold">General Education department</h6>
                                </div>
                                <p class="mb-0 text-muted fw-bold">
                                    {{ $GEcourses }} <i class="fa-solid fa-sitemap ms-2"></i>
                                </p>
                            </div>
                        </div>
                        <div class="event-card mb-3 p-3 bg-light rounded shadow-sm border-start border-4 border-primary">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1 fw-bold">Computer Science department</h6>
                                </div>
                                <p class="mb-0 text-muted fw-bold">
                                    {{ $CScourses }} <i class="fa-solid fa-code ms-2"></i>
                                </p>
                            </div>
                        </div>
                        <div class="event-card mb-3 p-3 bg-light rounded shadow-sm border-start border-4 border-primary">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1 fw-bold">Artificial Intelligence department</h6>
                                </div>
                                <p class="mb-0 text-muted fw-bold">
                                    {{ $AIcourses }} <i class="fa-solid fa-hexagon-nodes ms-2"></i>
                                </p>
                            </div>
                        </div>
                        <div class="event-card mb-3 p-3 bg-light rounded shadow-sm border-start border-4 border-primary">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1 fw-bold">Information System department</h6>
                                </div>
                                <p class="mb-0 text-muted fw-bold">
                                    {{ $IScourses }} <i class="fa-solid fa-database ms-2"></i>
                                </p>
                            </div>
                        </div>
                    </div>
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

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                labels: ['freshman', 'sophomore', 'junior', 'senior'],
                datasets: [{
                    data: @json($studentDistribution),
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
                maintainAspectRatio: false,
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

        const professorsDistribution = document.getElementById('professorsDistribution').getContext('2d');
        new Chart(professorsDistribution, {
            type: 'pie',
            data: {
                labels: ['General Education', 'Computer Science', 'Artificial Intelligence', 'Information System'],
                datasets: [{
                    data: @json($professorsDistribution),
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
                maintainAspectRatio: false,
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
